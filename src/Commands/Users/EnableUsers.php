<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands\Users;

/**
 * PKP namespaces
 */
use APP\facades\Repo;
use PKP\user\User;

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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class EnableUsers extends Command
{
    use IteratingPKPApplication;
    use ValidatingCLIParameters;

    protected function configure()
    {
        $this->setName('users:enable')
            ->setDescription('Enable a list of users Ids')
            ->addOption('contextId', 'c', InputOption::VALUE_REQUIRED, 'Filtering by the Context Id')
            ->addOption('userIds', 'i', InputOption::VALUE_REQUIRED, 'Comma-separated list with Ids');
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->discoverableApplication();
        $this->contextIdHasBeenPassed($input);
        $this->userIdsHasBeenPassed($input);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $contextId = $input->getOption('contextId');
        $rawUserIds = $input->getOption('userIds');

        $userIds = explode(',', $rawUserIds);

        $collector = Repo::user()->getCollector()
            ->filterByContextIds([$contextId])
            ->filterByUserIds($userIds)
            ->filterByStatus(Repo::user()->getCollector()::STATUS_ALL);

        $collection = Repo::user()->getMany($collector);

        $customOutput = clone $output;
        $customOutput->setVerbosity($output::VERBOSITY_VERY_VERBOSE);
        $progressBar = new ProgressBar($customOutput);
        $progressBar->start();

        $enabled = 0;
        foreach($collection as $user) {
            if ($this->enableUser($user)) {
                $enabled++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $io = new SymfonyStyle($input, $output);
        $io->info($enabled . ' users have been enabled');

        return 1;
    }

    protected function enableUser(User $user): bool
    {
        $user->setDisabled(false);
        $user->setData('disabled_reason', null);

        Repo::user()->edit($user);

        return (Repo::user()->get($user->getData('id')) !== null);
    }
}
