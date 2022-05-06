<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite\GraveSiteSizeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSizeTypeTest extends CustomStringTypeTest
{
    protected string $className = GraveSiteSizeType::class;
    protected string $typeName  = 'grave_site_size';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '2.5';
        $this->phpValue = new GraveSiteSize('2.5');
    }
}
