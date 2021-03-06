<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout;

/**
 * Scout namespaces
 */
use RamosHenrique\Scout\Adapters\PKPCommands;
use RamosHenrique\Scout\Commands\{
    Inspire,
    MaintenanceMode
};
use RamosHenrique\Scout\Commands\Roles\RolesList;
use RamosHenrique\Scout\Commands\Users\{
    AddUser,
    CountByRole,
    DisableUsers,
    EnableUsers,
    ListingUsers
};

/**
 * Symfony namespaces
 */
use Symfony\Component\Console\Application as SymfonyApplication;

class DiscoverCommands extends SymfonyApplication
{
    /**
     * Get the discovered commands for the application.
     *
     */
    public function discoveredCommands(): array
    {
        $commands = [
            AddUser::class,
            CountByRole::class,
            DisableUsers::class,
            EnableUsers::class,
            Inspire::class,
            ListingUsers::class,
            MaintenanceMode::class,
            RolesList::class,
        ];

        $pkpCommands = new PKPCommands();

        foreach ($pkpCommands->commandsFolderPath() as $directory) {
            $commands = array_merge_recursive(
                $commands,
                $pkpCommands->discoveryCommandsWithin($directory)
            );
        }

        return $commands;
    }
}