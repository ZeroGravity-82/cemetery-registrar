<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyIdType extends EntityMaskingIdType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = FuneralCompanyId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'funeral_company_id';

    /**
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): FuneralCompanyId
    {
        return match ($decodedValue['type']) {
            JuristicPerson::CLASS_SHORTCUT => new FuneralCompanyId(new JuristicPersonId($decodedValue['value'])),
            SoleProprietor::CLASS_SHORTCUT => new FuneralCompanyId(new SoleProprietorId($decodedValue['value'])),
        };
    }
}
