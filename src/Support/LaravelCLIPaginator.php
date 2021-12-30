<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout\Support;

use Illuminate\Pagination\LengthAwarePaginator;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LaravelCLIPaginator
{
    protected $input;
    protected $output;
    protected $paginator;

    public function __construct(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->input = $input;
        $this->output = $output;
    }

    public function setPagination(LengthAwarePaginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function getPagination(): LengthAwarePaginator
    {
        return $this->paginator;
    }

    public function currentPage(): string
    {
        return 'Current page ' . (int) $this->getPagination()->currentPage();
    }

    public function nextPage(): string
    {
        if (!$this->getPagination()->hasMorePages()) {
            return '';
        }

        return 'Next page ' . (int) ($this->getPagination()->currentPage() + 1);
    }

    public function previousPage(): string
    {
        if ($this->getPagination()->currentPage() < 1) {
            return 'Previous Page: 1';
        }

        if (($this->getPagination()->currentPage() - 1) <= 0) {
            return '';
        }

        return 'Previous Page: ' . ((int) $this->getPagination()->currentPage() - 1);
    }

    public function render(): void
    {
        $io = new SymfonyStyle($this->input, $this->output);

        $message = [
            'Pagination',
            $this->currentPage(),
            $this->nextPage(),
            $this->previousPage()
        ];

        $io->block(implode(PHP_EOL, array_filter($message)), null, 'fg=yellow', '| ');
    }
}