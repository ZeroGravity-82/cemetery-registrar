<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact\WebsiteType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class WebsiteTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = WebsiteType::class;
    protected string $typeName  = 'website';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'https://example.com';
        $this->phpValue = new Website('https://example.com');
    }
}
