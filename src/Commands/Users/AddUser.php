<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands\Users;

use InvalidArgumentException;

/**
 * PKP namespaces
 */
use APP\facades\Repo;
use PKP\core\Core;
use PKP\db\DAORegistry;
use PKP\security\Validation;

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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddUser extends Command
{
    use IteratingPKPApplication;
    use ValidatingCLIParameters;

    protected $outputStyled;
    protected $filesystem;
    protected $maintenanceModeFilePath;

    protected function configure()
    {
        $this->setName('users:add')
            ->setDescription('Add an User')
            ->setHelp('This command creates an User and associate roles to it')
            ->addOption('contextId', 'c', InputOption::VALUE_REQUIRED, 'Filtering by the Context Id')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'User\'s name')
            ->addOption('surname', null, InputOption::VALUE_REQUIRED, 'User\'s surname')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'User\'s email')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'User\'s username')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'User\'s password')
            ->addOption('role', 'role', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'User Roles to associate. Use the command roles:list to see which roles are available')
            ->addOption('changePassword', null, InputOption::VALUE_NONE, 'With this flag, the user will need to reset his password once login for first time')
            ->addOption('help', 'h', InputOption::VALUE_NONE, 'With this flag, the user will have the inline help');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->outputStyled = new SymfonyStyle($input, $output);
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->discoverableApplication();
        $this->contextIdHasBeenPassed($input);

        if (!$input->getOption('name')) {
            throw new InvalidArgumentException('You should pass a name for the user');
        }

        if (!$input->getOption('surname')) {
            throw new InvalidArgumentException('You should pass a surname for the user');
        }

        if (!$input->getOption('email')) {
            throw new InvalidArgumentException('You should pass an email for the user');
        }

        if(!filter_var($input->getOption('email'), FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('You should provide an valid email for this user');
        }

        if (!$input->getOption('username')) {
            throw new InvalidArgumentException('You should pass an username for the user');
        }

        if (!$input->getOption('password')) {
            throw new InvalidArgumentException('You should pass a password for the user');
        }

        if ($input->getOption('role') === []) {
            throw new InvalidArgumentException('You should pass at least one role for this user');
        }

        $availableRoles = [];
        $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
        $userGroups = $userGroupDao->getByContextId($input->getOption('contextId'));
        while ($userGroup = $userGroups->next()) {
            $availableRoles[] = (int) $userGroup->getId();
        }

        if (array_intersect($input->getOption('role'), $availableRoles) === []) {
            throw new InvalidArgumentException('Invalid role for this user');
        }
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $rawRoles = $input->getOption('role');

        $roles = [];
        foreach ($rawRoles as $currentRole) {
            $roles[] = (int) trim($currentRole);
        }

        $roles = array_filter($roles);

        $user = Repo::user()->newDataObject();

        $user->setUsername($username);
        $user->setEmail($input->getOption('email'));
        $user->setPassword(Validation::encryptCredentials($username, $password));
        $user->setGivenName($input->getOption('name'), null);
        $user->setFamilyName($input->getOption('surname'), null);
        $user->setDateRegistered(Core::getCurrentDate());

        if ($input->getOption('changePassword')) {
            $user->setMustChangePassword(true);
        }

        if ($input->getOption('help')) {
            $user->setInlineHelp(true);
        }

        $userId = Repo::user()->add($user);

        if ($roles !== []) {
            $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
            foreach ($roles as $userGroupId) {
                $userGroupDao->assignUserToGroup($userId, $userGroupId);
            }
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('User ' . $username . ' has been created');

        return 1;
    }
}
