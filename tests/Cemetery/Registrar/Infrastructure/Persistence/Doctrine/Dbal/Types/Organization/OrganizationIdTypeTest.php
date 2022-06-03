<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
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

    protected function getConversionTests(): array
    {
        return [
            // database value, PHP value
            ['{"type":"JURISTIC_PERSON","value":"JP001"}', new OrganizationId(new JuristicPersonId('JP001'))],
            ['{"type":"SOLE_PROPRIETOR","value":"SP001"}', new OrganizationId(new SoleProprietorId('SP001'))],
        ];
    }
}