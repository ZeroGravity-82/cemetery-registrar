<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact;

use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Contact\EmailType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EmailTypeTest extends CustomStringTypeTest
{
    protected string $className = EmailType::class;
    protected string $typeName  = 'email';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'info@google.com';
        $this->phpValue = new Email('info@google.com');
    }
}
