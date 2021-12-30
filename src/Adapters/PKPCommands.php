<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout\Adapters;

use Exception;
use SplFileInfo;

/**
 * Scout namespaces
 */
use RamosHenrique\Scout\Interfaces\DiscoverCommand;
use RamosHenrique\Scout\Traits\IteratingPKPApplication;

/**
 * Symfony namespaces
 */
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

class PKPCommands implements DiscoverCommand
{
    use IteratingPKPApplication;

    protected $commandsFolders = [];

    public function __construct()
    {
        $this->discoverableApplication();

        $this->setCommandsFolderPath([
            app()->basePath('lib/pkp/classes/console/Commands'),
            app()->basePath('classes/console/Commands')
        ]);
    }

    public function discoveryCommandsWithin(string $folder): array
    {
        try {
            return $this->sanitizeCommandsList((new Finder())->files()->name('*.php')->in($folder), $folder);
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
        string $folder = ''
    ): array {
        $commandsList = [];

        foreach ($commands as $command) {
            try {
                $commandPath = $this->parseCommandPath($command, app()->basePath());
                $commandName = $this->buildCommandNamespace($command, app()->basePath());

                import($commandPath);
            } catch (Exception $e) {
                continue;
            }

            if (!is_subclass_of($commandName, SymfonyCommand::class)) {
                continue;
            }

            $commandsList[] = new $commandName();
        }

        return array_filter($commandsList);
    }

    public function setCommandsFolderPath(array $commandsFolders = []): self
    {
        $this->commandsFolders = $commandsFolders;

        return $this;
    }

    /**
     * Which folders will be scanned?
     *
     * @return array
     */
    public function commandsFolderPath(): array
    {
        return $this->commandsFolders;
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
        $namespace = trim(str_replace($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        $namespace = str_replace('.inc.php', '', $namespace);

        return str_replace(DIRECTORY_SEPARATOR, '.', $namespace);
    }

    /**
     * Build a command namespace from the path
     *
     * @param  SplFileInfo $file
     * @param  string  $basePath
     *
     * @return string
     */
    public function buildCommandNamespace(
        SplFileInfo $file,
        string $basePath = ''
    ): string {
        $namespace = trim(str_replace($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        $namespace = str_replace('.inc.php', '', $namespace);
        $namespace = str_replace('lib/pkp/classes/', 'PKP/', $namespace);
        $namespace = str_replace('classes/', 'APP/', $namespace);
        $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', $namespace);

        return $namespace;
    }
}