<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands\Users;

/**
 * PKP namespaces;
 */
use APP\core\Application;
use APP\facades\Repo;

/**
 * Scout namespaces
 */
use RamosHenrique\Scout\Traits\{
    IteratingPKPApplication,
    ValidatingCLIParameters
};

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

class CountByRole extends Command
{
    use IteratingPKPApplication;
    use ValidatingCLIParameters;

    protected function configure()
    {
        $this->setName('users:count_by_role')
            ->setDescription('Count users by role')
            ->addOption('contextId', 'c', InputOption::VALUE_REQUIRED, 'Filtering by the Context Id');
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

        $rolesNames = Application::get()->getRoleNames();

        $collector = Repo::user()
            ->getCollector()
            ->filterByContextIds(['contextId' => $contextId]);

        $total = Repo::user()->dao->getCount($collector);

        $amountByRoles = [];
        foreach ($rolesNames as $roleId => $roleName) {
            $amountByRoles[] = [
                'id' => $roleId,
                'name' => __($roleName, [], 'en_US'),
                'value' => Repo::user()->dao->getCount($collector->filterByRoleIds([$roleId])),
            ];
        }

        $table = new Table($output);
        $table
            ->setFooterTitle('Total ' . $total)
            ->setHeaders(
                [
                    [
                        new TableCell(
                            'Amount by Roles',
                            [
                                'colspan' => 3,
                                'style' => new TableCellStyle(['align' => 'center'])
                            ]
                        )
                    ],
                    [
                        'Role Id',
                        'Role name',
                        'Amount'
                    ]
                ]
            )
            ->setRows($amountByRoles);

        $table->render();

        return 1;
    }
}
