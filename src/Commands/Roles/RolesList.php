<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands\Roles;

/**
 * Scout namespaces
 */
use RamosHenrique\Scout\Traits\{
    IteratingPKPApplication,
    ValidatingCLIParameters
};

/**
 * PKP namespaces
 */
use PKP\db\DAORegistry;

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

class RolesList extends Command
{
    use IteratingPKPApplication;
    use ValidatingCLIParameters;

    protected function configure()
    {
        $this->setName('roles:list')
            ->setDescription('List all roles for a context')
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

        $availableRoles = [];
        $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
        $userGroups = $userGroupDao->getByContextId($contextId);
        while ($userGroup = $userGroups->next()) {
            $rawName = $userGroup->getName(null);
            $sanitizedName = array_shift($rawName);
            $availableRoles[(int) $userGroup->getId()] = [
                'id' => $userGroup->getId(),
                'name' => $sanitizedName,
            ];
        }

        $tableHeaders = [
            'Id',
            'Name',
        ];

        $table = new Table($output);
        $table
            ->setHeaders(
                [
                    [
                        new TableCell(
                            'Roles Listing',
                            [
                                'colspan' => 2,
                                'style' => new TableCellStyle(['align' => 'center'])
                            ]
                        )
                    ],
                    $tableHeaders
                ]
            )
            ->setRows($availableRoles);

        $table->render();

        return 1;
    }
}
