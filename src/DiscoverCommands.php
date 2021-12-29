<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout;

use Symfony\Component\Console\Application as SymfonyApplication;

use RamosHenrique\Scout\Adapters\{
    LocalCommands,
    PKPCommands
};

class DiscoverCommands extends SymfonyApplication
{
    /**
     * Get the discovered commands for the application.
     *
     */
    public function discoveredCommands(): array
    {
        $commands = [];
        $localCommands = new LocalCommands();

        foreach ($localCommands->commandsFolderPath() as $directory) {
            $commands = array_merge_recursive(
                $commands,
                $localCommands->discoveryCommandsWithin($directory)
            );
        }

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