<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\EntityMaskingIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CustomerIdType extends EntityMaskingIdType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CustomerId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'customer_id';

    /**
     * {@inheritdoc}
     */
    public static function getClassShortcut(string $className): string
    {
        return match ($className) {
            NaturalPersonId::class  => 'NaturalPersonId',
            JuristicPersonId::class => 'JuristicPersonId',
            SoleProprietorId::class => 'SoleProprietorId',
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): CustomerId
    {
        return match ($decodedValue['class']) {
            self::getClassShortcut(NaturalPersonId::class)  => new CustomerId(new NaturalPersonId($decodedValue['value'])),
            self::getClassShortcut(JuristicPersonId::class) => new CustomerId(new JuristicPersonId($decodedValue['value'])),
            self::getClassShortcut(SoleProprietorId::class) => new CustomerId(new SoleProprietorId($decodedValue['value'])),
        };
    }
}
