<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands\Users;

/**
 * PKP namespaces
 */
use APP\facades\Repo;
use PKP\config\Config;

/**
 * Scout namespaces
 */
use RamosHenrique\Scout\Support\LaravelCLIPaginator;
use RamosHenrique\Scout\Traits\{
    IteratingPKPApplication,
    ValidatingCLIParameters
};

/**
 * Laravel namespaces
 */
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Symfony namespaces
 */
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListingUsers extends Command
{
    use IteratingPKPApplication;
    use ValidatingCLIParameters;

    protected function configure()
    {
        $this->setName('users:lists')
            ->setDescription('List users')
            ->addOption('contextId', 'c', InputOption::VALUE_REQUIRED, 'Filtering by the Context Id')
            ->addOption('registeredAfter', 'ra', InputOption::VALUE_OPTIONAL, 'Filtering by registered after date')
            ->addOption('registeredBefore', 'rb', InputOption::VALUE_OPTIONAL, 'Filtering by registered before date')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limiting the output')
            ->addOption('page', 'p', InputOption::VALUE_OPTIONAL, 'Results pagination');
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->discoverableApplication();
        $this->contextIdHasBeenPassed($input);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $contextId = $input->getOption('contextId');
        $limit = $input->getOption('limit');
        $page = $input->getOption('page');
        $registeredBefore = $input->getOption('registeredBefore');
        $registeredAfter = $input->getOption('registeredAfter');
        $includeRegisteredDate = $input->getOption('includeRegisteredDate');

        $offsetRows = $limit * ($page - 1);

        $collector = Repo::user()
            ->getCollector()
            ->filterByContextIds(['contextId' => $contextId]);

        if ($registeredBefore) {
            $sanitizedBeforeDate = Carbon::parse($registeredBefore)->format('Y-m-d');
            $collector->filterRegisteredBefore($sanitizedBeforeDate);
        }

        if ($registeredAfter) {
            $sanitizedAfterDate = Carbon::parse($registeredAfter)->format('Y-m-d');
            $collector->filterRegisteredAfter($sanitizedAfterDate);
        }

        $total = Repo::user()->dao->getCount($collector);

        if ($limit) {
            $collector->limit((int) $limit);
            $collector->offset($offsetRows);
        }

        $paginated = new LengthAwarePaginator(
            Repo::user()->getMany($collector),
            $total,
            $limit,
            $page
        );

        if ($paginated->isEmpty()) {
            $io = new SymfonyStyle($input, $output);
            $io->warning('Empty result for search');

            return 1;
        }

        (new LaravelCLIPaginator($input, $output))->setPagination($paginated)
            ->render();

        $users = [];
        foreach ($paginated->items() as $currentUser) {
            $user = [
                'id' => $currentUser->getData('id'),
                'name' => $currentUser->getLocalizedData('givenName', Config::getVar('i18n', 'locale')),
                'surname' => $currentUser->getLocalizedData('familyName', Config::getVar('i18n', 'locale')),
                'email' => $currentUser->getData('email'),
                'disabled' => ($currentUser->getDisabled() ? 'Yes' : 'No'),
            ];

            if ($includeRegisteredDate) {
                $user['registeredDate'] = $currentUser->getDateRegistered();
            }

            $users[] = $user;
        }

        $tableHeaders = [
            'Id',
            'Name',
            'Surname',
            'Username',
            'Disabled',
        ];

        if ($includeRegisteredDate) {
            $tableHeaders[] = 'Registered Date';
        }

        $table = new Table($output);
        $table
            ->setFooterTitle('Total in database ' . $total)
            ->setHeaders(
                [
                    [
                        new TableCell(
                            'User Listing',
                            [
                                'colspan' => count($tableHeaders),
                                'style' => new TableCellStyle(['align' => 'center'])
                            ]
                        )
                    ],
                    $tableHeaders
                ]
            )
            ->setRows($users);

        $table->render();

        return 1;
    }
}
