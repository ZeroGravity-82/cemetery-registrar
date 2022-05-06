<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
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

    protected function getConversionTests(): array
    {
        return [
            // database value, PHP value
            ['{"class":"NaturalPersonId","value":"NP001"}',  new CustomerId(new NaturalPersonId('NP001'))],
            ['{"class":"JuristicPersonId","value":"JP001"}', new CustomerId(new JuristicPersonId('JP001'))],
            ['{"class":"SoleProprietorId","value":"SP001"}', new CustomerId(new SoleProprietorId('SP001'))],
        ];
    }
}
