<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout\Interfaces;

use SplFileInfo;

interface DiscoverCommand
{
    public function discoveryCommandsWithin(string $folder): array;
    public function sanitizeCommandsList(iterable $commands, string $folder): array;
    public function commandsFolderPath(): array;
    public function parseCommandPath(SplFileInfo $file, string $folder): string;
    public function buildCommandNamespace(SplFileInfo $file, string $folder): string;
}