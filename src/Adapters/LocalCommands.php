<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout\Adapters;

use ReflectionClass;
use ReflectionException;
use SplFileInfo;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

use RamosHenrique\Scout\Interfaces\DiscoverCommand;

class LocalCommands implements DiscoverCommand
{
    public function discoveryCommandsWithin(string $folder): array
    {
        try {
            return $this->sanitizeCommandsList(
                (new Finder())->files()->name('*.php')->in($folder),
                $folder
            );
        } catch (DirectoryNotFoundException $e) {
            // Keep the processing if directory is not found
            return [];
        }
    }

    /**
     * Get a sanitized commands list
     *
     * @param iterable $commands
     * @param string $folder
     *
     * @return array
     */
    public function sanitizeCommandsList(
        iterable $commands,
        string $folder
    ): array {
        $commandsList = [];

        foreach ($commands as $commandFile) {
            try {
                $commandName = $this->buildCommandNamespace($commandFile, $folder);
                $command = new ReflectionClass($commandName);
            } catch (ReflectionException $e) {
                continue;
            }

            if (!$command->isSubclassOf(SymfonyCommand::class)) {
                continue;
            }

            $commandsList[] = $commandName;
        }

        return array_filter($commandsList);
    }

    /**
     * Which folders will be scanned?
     *
     * @return array
     */
    public function commandsFolderPath(): array
    {
        return [
            dirname(__DIR__) . '/Commands',
        ];
    }

    /**
     * Parse the command path to be included
     *
     * @param  SplFileInfo $file
     * @param  string  $basePath
     *
     * @return string
     */
    public function parseCommandPath(
        SplFileInfo $file,
        string $basePath = ''
    ): string {
        return trim(str_replace($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
    }

    /**
     * Build a command namespace from the path
     *
     * @param  SplFileInfo $file
     * @param  string  $basePath
     * @return string
     */
    public function buildCommandNamespace(
        SplFileInfo $file,
        string $basePath = ''
    ): string {
        $className = str_replace(
            '.php',
            '',
            $this->parseCommandPath($file, $basePath)
        );
        $className = str_replace('/', '\\', $className);

        return str_replace('\Adapters', '', __NAMESPACE__) . '\Commands\\' . ucfirst($className);
    }
}