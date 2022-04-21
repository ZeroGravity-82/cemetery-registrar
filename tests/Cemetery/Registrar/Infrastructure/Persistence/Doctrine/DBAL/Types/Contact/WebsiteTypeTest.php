<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Contact;

use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Contact\WebsiteType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class WebsiteTypeTest extends StringTypeTest
{
    protected string $className = WebsiteType::class;

    protected string $typeName = 'website';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'https://example.com';
        $this->phpValue = new Website('https://example.com');
    }
}
