<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorld extends Command
{
    protected function configure()
    {
        $this->setName('hello_world')
            ->setDescription('Say Hello to World!');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('Hello World!');

        return 1;
    }
}
