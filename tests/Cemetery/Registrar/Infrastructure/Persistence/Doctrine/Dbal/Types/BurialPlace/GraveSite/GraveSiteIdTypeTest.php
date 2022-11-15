<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite\GraveSiteIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteIdTypeTest extends AbstractCustomStringTypeTest
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
