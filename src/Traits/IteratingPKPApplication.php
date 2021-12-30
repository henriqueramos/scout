<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout\Traits;

use LogicException;

/**
 * PKP namespaces;
 */
use APP\core\Application;
use APP\facades\Repo;

trait IteratingPKPApplication
{
    /**
     * Its PKP Application discoverable?
     *
     * @throws LogicException In case of not founding the PKP Application's, throw a LogicException
     *
     * @return bool
     */
    public function discoverableApplication(): bool
    {
        if (!class_exists(Application::class) || !class_exists(Repo::class)) {
            throw new LogicException('Unable to identify OJS/OMP/OPS application');
        }

        return true;
    }
}