<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\EntityMaskingIdType;

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
    public static function getClassShortcut(string $className): string
    {
        return match ($className) {
            JuristicPersonId::class => 'JuristicPersonId',
            SoleProprietorId::class => 'SoleProprietorId',
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): FuneralCompanyId
    {
        return match ($decodedValue['class']) {
            self::getClassShortcut(JuristicPersonId::class) => new FuneralCompanyId(new JuristicPersonId($decodedValue['value'])),
            self::getClassShortcut(SoleProprietorId::class) => new FuneralCompanyId(new SoleProprietorId($decodedValue['value'])),
        };
    }
}
