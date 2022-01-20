<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Site\SiteId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\SiteIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SiteIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = SiteIdType::class;

    protected string $typeName = 'site_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '28485684-6cf6-4bca-adfc-37a67c3ec4ec';
        $this->phpValue = new SiteId('28485684-6cf6-4bca-adfc-37a67c3ec4ec');
    }
}
