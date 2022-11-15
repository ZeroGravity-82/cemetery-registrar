<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\NaturalPerson;

use Cemetery\Registrar\Domain\View\FetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface NaturalPersonFetcherInterface extends FetcherInterface
{
    /**
     * Returns a list of all natural persons.
     */
    public function findAll(?string $term = null): NaturalPersonSimpleList;

    /**
     * Returns a list of alive natural persons.
     */
    public function findAlive(?string $term = null): NaturalPersonSimpleList;
}
