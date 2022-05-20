<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdType;

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
    protected function buildPhpValue(array $decodedValue): CustomerId
    {
        return match ($decodedValue['type']) {
            NaturalPerson::CLASS_SHORTCUT  => new CustomerId(new NaturalPersonId($decodedValue['value'])),
            SoleProprietor::CLASS_SHORTCUT => new CustomerId(new SoleProprietorId($decodedValue['value'])),
            JuristicPerson::CLASS_SHORTCUT => new CustomerId(new JuristicPersonId($decodedValue['value'])),
        };
    }
}
