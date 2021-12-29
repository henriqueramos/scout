<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout;

use Symfony\Component\Console\Application as SymfonyApplication;

class ScoutApplication extends SymfonyApplication
{
    protected $asciiLogo = <<<LOGO
    _____                  __ 
    / ___/_________  __  __/ /_
    \__ \/ ___/ __ \/ / / / __/
   ___/ / /__/ /_/ / /_/ / /_  
  /____/\___/\____/\__,_/\__/  
                               
LOGO;

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->setLogo($this->asciiLogo);

        $commands = new DiscoverCommands();

        $availableCommands = $commands->discoveredCommands();
        foreach ($availableCommands as $command) {
            $this->add((new $command()));
        }
    }

    /**
     * Set the Ascii Logo
     *
     *
     * @return self;
     */
    public function setLogo(string $logo = ''): self
    {
        $this->asciiLogo = $logo;

        return $this;
    }

    /**
     * Get the Ascii Logo
     *
     * @return string
     */
    public function getLogo(): string
    {
        return $this->asciiLogo;
    }

    /**
     * Shows the Help header section
     *
     * @return string
     */
    public function getHelp(): string
    {
        return $this->getLogo() . PHP_EOL . parent::getHelp();
    }
}