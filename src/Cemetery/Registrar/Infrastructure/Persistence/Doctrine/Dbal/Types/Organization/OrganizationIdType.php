<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class OrganizationIdType extends EntityMaskingIdType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = OrganizationId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'organization_id';

    /**
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): OrganizationId
    {
        return match ($decodedValue['type']) {
            JuristicPerson::CLASS_SHORTCUT => new OrganizationId(new JuristicPersonId($decodedValue['value'])),
            SoleProprietor::CLASS_SHORTCUT => new OrganizationId(new SoleProprietorId($decodedValue['value'])),
        };
    }
}
