<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands\Users;

use InvalidArgumentException;

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


class DisableUsers extends Command
{
    use IteratingPKPApplication;
    use ValidatingCLIParameters;

    protected function configure()
    {
        $this->setName('users:disable')
            ->setDescription('Disable a list of users Ids')
            ->addOption('contextId', 'c', InputOption::VALUE_REQUIRED, 'Filtering by the Context Id')
            ->addOption('userIds', 'i', InputOption::VALUE_REQUIRED, 'Comma-separated list with Ids')
            ->addOption('reason', 'r', InputOption::VALUE_REQUIRED, 'Reason for disabling those users');
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->discoverableApplication();
        $this->contextIdHasBeenPassed($input);
        $this->userIdsHasBeenPassed($input);

        $reason = $input->getOption('reason');

        if (!$reason) {
            throw new InvalidArgumentException('You should pass a reason on this');
        }
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $contextId = $input->getOption('contextId');
        $rawUserIds = $input->getOption('userIds');
        $reason = $input->getOption('reason');

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

        $disabled = 0;
        foreach($collection as $user) {
            if ($this->disableUser($user, $reason)) {
                $disabled++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $io = new SymfonyStyle($input, $output);
        $io->info($disabled . ' users have been disable');

        return 1;
    }

    protected function disableUser(
        User $user,
        string $reason
    ): bool {
        $user->setDisabled(true);
        $user->setData('disabled_reason', $reason);

        Repo::user()->edit($user);

        return (Repo::user()->get($user->getData('id')) === null);
    }
}
