<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands;

/**
 * Scout namespaces
 */
use RamosHenrique\Scout\Traits\IteratingPKPApplication;

/**
 * Symfony namespaces
 */
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class MaintenanceMode extends Command
{
    use IteratingPKPApplication;

    protected $outputStyled;
    protected $filesystem;
    protected $maintenanceModeFilePath;

    protected function configure()
    {
        $this->setName('maintenance')
            ->setDescription('Create the .maintenance file on root folder application')
            ->setHelp('This command only works alongside the plugin henriqueramos/maintenanceMode')
            ->addOption('disable', 'd', InputOption::VALUE_NONE, 'Disable maintenance mode')
            ->addOption('enable', 'e', InputOption::VALUE_NONE, 'Enable maintenance mode');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->outputStyled = new SymfonyStyle($input, $output);
        $this->filesystem = new Filesystem();
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->discoverableApplication();

        $this->maintenanceModeFilePath = app()->basePath('.maintenance');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $disable = $input->getOption('disable');
        $enable = $input->getOption('enable');

        if ($disable) {
            return (int) $this->disableMaintenance();
        }

        if ($enable) {
            return (int) $this->enableMaintenance();
        }

        $this->maintenanceStatus();

        return 1;
    }

    protected function disableMaintenance(): bool
    {
        $this->outputStyled->info('Maintenance mode ended');
        $this->filesystem->remove([$this->maintenanceModeFilePath]);

        return true;
    }

    protected function enableMaintenance(): bool
    {
        $this->outputStyled->warning('Maintenance mode started');
        $this->filesystem->touch($this->maintenanceModeFilePath);

        return true;
    }

    protected function maintenanceStatus(): void
    {
        $fileExists = $this->filesystem->exists($this->maintenanceModeFilePath);

        if ($fileExists) {
            $this->outputStyled->warning('This application is under maintenance mode.');

            return;
        }

        $this->outputStyled->info('This application is running in normal mode.');
    }
}
