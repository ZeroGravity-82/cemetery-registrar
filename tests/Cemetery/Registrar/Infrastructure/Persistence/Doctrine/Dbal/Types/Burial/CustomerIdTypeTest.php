<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\CustomerIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\EntityMaskingIdTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdTypeTest extends EntityMaskingIdTypeTest
{
    protected string $className         = CustomerIdType::class;
    protected string $typeName          = 'customer_id';
    protected string $phpValueClassName = CustomerId::class;

    protected function getConversionData(): iterable
    {
        // database value, PHP value
        yield ['{"type":"NATURAL_PERSON","value":"NP001"}',  new CustomerId(new NaturalPersonId('NP001'))];
        yield ['{"type":"JURISTIC_PERSON","value":"JP001"}', new CustomerId(new JuristicPersonId('JP001'))];
        yield ['{"type":"SOLE_PROPRIETOR","value":"SP001"}', new CustomerId(new SoleProprietorId('SP001'))];
    }
}
