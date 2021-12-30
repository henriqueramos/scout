<?php

declare(strict_types=1);

namespace RamosHenrique\Scout\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Inspire extends Command
{
    protected function configure()
    {
        $this->setName('inspire')
            ->setDescription('Display an inspiring quote every time');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        // Some of those are extract from here @link https://www.forbes.com/sites/kevinkruse/2013/05/28/inspirational-quotes/
        $quotes = [
            'Strive not to be a success, but rather to be of value. –Albert Einstein',
            'I attribute my success to this: I never gave or took any excuse. –Florence Nightingale',
            'The most difficult thing is the decision to act, the rest is merely tenacity. –Amelia Earhart',
            'Do what you can, with what you have, where you are. - Theodore Roosevelt',
            'If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius',
            'It is not the man who has too little, but the man who craves more, that is poor. - Seneca',
            'It is quality rather than quantity that matters. - Lucius Annaeus Seneca',
            'No surplus words or unnecessary actions. - Marcus Aurelius',
            'Nothing worth having comes easy. - Theodore Roosevelt',
            'Order your soul. Reduce your wants. - Augustine',
            'People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius',
            'Simplicity is an acquired taste. - Katharine Gerould',
            'Simplicity is the consequence of refined emotions. - Jean D\'Alembert',
            'Simplicity is the essence of happiness. - Cedric Bledsoe',
            'Simplicity is the ultimate sophistication. - Leonardo da Vinci',
            'The whole future lies in uncertainty: live immediately. - Seneca',
            'Very little is needed to make a happy life. - Marcus Aurelius',
            'Waste no more time arguing what a good man should be, be one. - Marcus Aurelius',
            'Well begun is half done. - Aristotle',
            'Go confidently in the direction of your dreams.  Live the life you have imagined. –Henry David Thoreau',
            'I would rather die of passion than of boredom. –Vincent van Gogh',
            'Education costs money.  But then so does ignorance. –Sir Claus Moser',
            'Our lives begin to end the day we become silent about things that matter. –Martin Luther King Jr.',
            'Remember no one can make you feel inferior without your consent. –Eleanor Roosevelt',
            'It’s not the years in your life that count. It’s the life in your years. –Abraham Lincoln'
        ];

        $quote = $quotes[array_rand($quotes)];
        $io = new SymfonyStyle($input, $output);
        $io->comment($quote);

        return 1;
    }
}
