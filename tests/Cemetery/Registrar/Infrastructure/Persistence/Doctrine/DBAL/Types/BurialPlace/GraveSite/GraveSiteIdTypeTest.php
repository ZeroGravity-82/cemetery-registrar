<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\GraveSite\GraveSiteIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteIdTypeTest extends CustomStringTypeTest
{
    protected string $className = GraveSiteIdType::class;
    protected string $typeName  = 'grave_site_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'GS001';
        $this->phpValue = new GraveSiteId('GS001');
    }
}
