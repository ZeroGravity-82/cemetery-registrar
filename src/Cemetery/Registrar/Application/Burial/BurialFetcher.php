<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface BurialFetcher
{
    /**
     * @param string $id
     *
     * @return BurialView
     *
     * @throws \RuntimeException when the burial is not found by ID
     */
    public function getById(string $id): BurialView;

    /**
     * @param int|null    $page
     * @param string|null $term
     *
     * @return BurialView[]|array
     */
    public function findAll(?int $page = null, ?string $term = null): array;
}
