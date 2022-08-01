<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationIdType extends EntityMaskingIdType
{
    protected string $className = OrganizationId::class;
    protected string $typeName  = 'organization_id';

    /**
     * @throws Exception when the ID is invalid
     */
    protected function buildPhpValue(array $decodedValue): OrganizationId
    {
        return match ($decodedValue['type']) {
            JuristicPerson::CLASS_SHORTCUT => new OrganizationId(new JuristicPersonId($decodedValue['value'])),
            SoleProprietor::CLASS_SHORTCUT => new OrganizationId(new SoleProprietorId($decodedValue['value'])),
        };
    }
}
