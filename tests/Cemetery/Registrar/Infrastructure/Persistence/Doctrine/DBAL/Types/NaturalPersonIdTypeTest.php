<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\NaturalPersonIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = NaturalPersonIdType::class;

    protected string $typeName = 'natural_person_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '28485684-6cf6-4bca-adfc-37a67c3ec4ec';
        $this->phpValue = new NaturalPersonId('28485684-6cf6-4bca-adfc-37a67c3ec4ec');
    }
}
