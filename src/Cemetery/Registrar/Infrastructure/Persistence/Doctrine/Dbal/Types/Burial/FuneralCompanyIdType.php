<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
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
        return match ($decodedValue['classShortcut']) {
            JuristicPersonId::CLASS_SHORTCUT => new FuneralCompanyId(new JuristicPersonId($decodedValue['value'])),
            SoleProprietorId::CLASS_SHORTCUT => new FuneralCompanyId(new SoleProprietorId($decodedValue['value'])),
        };
    }
}
