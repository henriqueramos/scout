<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout\Adapters;

use Exception;
use SplFileInfo;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

use RamosHenrique\Scout\Interfaces\DiscoverCommand;

class PKPCommands implements DiscoverCommand
{
    protected $commandsFolders = [];

    public function __construct()
    {
        // Trying to include the tools/bootstrap.inc.php from PKP ecosystem
        if (@include_once(dirname(__DIR__) . '/tools/bootstrap.inc.php')) {
            $this->setCommandsFolderPath([
                app()->basePath('lib/pkp/classes/console/Commands'),
                app()->basePath('classes/console/Commands')
            ]);
        }
    }

    public function discoveryCommandsWithin(string $folder): array
    {
        try {
            return $this->sanitizeCommandsList((new Finder())->files()->name('*.php')->in($folder));
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
                $commandName = $this->buildCommandNamespace($command);

                import($commandPath);
            } catch (Exception $e) {
                continue;
            }

            if (!is_subclass_of($commandName, SymfonyCommand::class)) {
                continue;
            }

            $commandsList[] = $commandName;
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
        return trim(str_replace($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
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
        return str_replace('.inc.php', '', $file->getFileName());
    }
}