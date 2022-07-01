<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\OrganizationIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationIdTypeTest extends EntityMaskingIdTypeTest
{
    protected string $className         = OrganizationIdType::class;
    protected string $typeName          = 'organization_id';
    protected string $phpValueClassName = OrganizationId::class;

    protected function getConversionData(): iterable
    {
        // database value, PHP value
        yield ['{"type":"JURISTIC_PERSON","value":"JP001"}', new OrganizationId(new JuristicPersonId('JP001'))];
        yield ['{"type":"SOLE_PROPRIETOR","value":"SP001"}', new OrganizationId(new SoleProprietorId('SP001'))];
    }
}
