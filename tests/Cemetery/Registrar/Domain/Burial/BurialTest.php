<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Site\SiteId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialId    = new BurialId('777');
        $burialCode  = new BurialCode('CCC');
        $deceasedId  = new NaturalPersonId('888');
        $customerId  = new CustomerId('999', CustomerType::naturalPerson());
        $siteId      = new SiteId('BBB');
        $siteOwnerId = new NaturalPersonId('AAA');
        $burial      = new Burial($burialId, $burialCode, $deceasedId, $siteId, $customerId, $siteOwnerId);

        $this->assertInstanceOf(BurialId::class, $burial->getId());
        $this->assertSame('777', (string) $burial->getId());
        $this->assertInstanceOf(BurialCode::class, $burial->getCode());
        $this->assertSame('CCC', (string) $burial->getCode());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getDeceasedId());
        $this->assertSame('888', (string) $burial->getDeceasedId());
        $this->assertInstanceOf(CustomerId::class, $burial->getCustomerId());
        $this->assertSame('999', $burial->getCustomerId()->getValue());
        $this->assertSame(CustomerType::NATURAL_PERSON, (string) $burial->getCustomerId()->getType());
        $this->assertInstanceOf(SiteId::class, $burial->getSiteId());
        $this->assertSame('BBB', (string) $burial->getSiteId());
        $this->assertInstanceOf(NaturalPersonId::class, $burial->getSiteOwnerId());
        $this->assertSame('AAA', (string) $burial->getSiteOwnerId());
    }
}
