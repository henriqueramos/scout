<?php

declare(strict_types = 1);

namespace RamosHenrique\Scout\Traits;

use InvalidArgumentException;

use Symfony\Component\Console\Input\InputInterface;

trait ValidatingCLIParameters
{
    /**
     * ContextId parameter has been passed?
     *
     * @throws InvalidArgumentException If contextId isn't passed, throw an InvalidArgumentException
     *
     * @return bool
     */
    public function contextIdHasBeenPassed(InputInterface $input): bool
    {
        $contextId = $input->getOption('contextId');

        if (!$contextId) {
            throw new InvalidArgumentException('You should pass a contextId on this');
        }

        return true;
    }

    /**
     * userIds parameter has been passed?
     *
     * @throws InvalidArgumentException If userIds isn't passed, throw an InvalidArgumentException
     *
     * @return bool
     */
    public function userIdsHasBeenPassed(InputInterface $input): bool
    {
        $userIds = $input->getOption('userIds');

        if (!$userIds) {
            throw new InvalidArgumentException('You should pass an userIds on this');
        }

        return true;
    }
}