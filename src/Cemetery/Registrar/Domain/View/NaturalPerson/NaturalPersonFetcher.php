<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\NaturalPerson;

use Cemetery\Registrar\Domain\View\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface NaturalPersonFetcher extends Fetcher
{
    public const DEFAULT_PAGE_SIZE = 10;

    /**
     * Returns a list of all natural persons.
     */
    public function findAll(): NaturalPersonSimpleList;

    /**
     * Returns a list of alive natural persons.
     */
    public function findAlive(): NaturalPersonSimpleList;
}
