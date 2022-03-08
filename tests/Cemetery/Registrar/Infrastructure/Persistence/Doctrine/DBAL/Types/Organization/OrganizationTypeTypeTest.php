<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization;

use Cemetery\Registrar\Domain\Organization\OrganizationType;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\OrganizationTypeType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\AbstractStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationTypeTypeTest extends AbstractStringTypeTest
{
    protected string $className = OrganizationTypeType::class;

    protected string $typeName = 'organization_type';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = OrganizationType::JURISTIC_PERSON;
        $this->phpValue = new OrganizationType(OrganizationType::JURISTIC_PERSON);
    }
}
