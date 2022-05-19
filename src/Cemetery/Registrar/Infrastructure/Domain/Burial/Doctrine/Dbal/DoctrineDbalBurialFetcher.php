<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialFormView;
use Cemetery\Registrar\Application\Burial\BurialViewListItem;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineDbalBurialFetcher implements BurialFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getById(string $id): BurialFormView
    {

    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {

    }
}
