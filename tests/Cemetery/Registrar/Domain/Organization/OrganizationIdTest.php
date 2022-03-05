<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\OrganizationType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $organizationType = new OrganizationType(OrganizationType::JURISTIC_PERSON);
        $organizationId   = new OrganizationId('777', $organizationType);
        $this->assertSame('777', $organizationId->getValue());
        $this->assertSame($organizationType, $organizationId->getType());
    }

    public function testItStringifyable(): void
    {
        $organizationType = new OrganizationType(OrganizationType::JURISTIC_PERSON);
        $organizationId   = new OrganizationId('777', $organizationType);
        $this->assertSame(OrganizationType::JURISTIC_PERSON . '.' . '777', (string) $organizationId);
    }
    
    public function testItComparable(): void
    {
        $organizationIdA = new OrganizationId('777', new OrganizationType(OrganizationType::SOLE_PROPRIETOR));
        $organizationIdB = new OrganizationId('777', new OrganizationType(OrganizationType::JURISTIC_PERSON));
        $organizationIdC = new OrganizationId('888', new OrganizationType(OrganizationType::SOLE_PROPRIETOR));
        $organizationIdD = new OrganizationId('777', new OrganizationType(OrganizationType::SOLE_PROPRIETOR));

        $this->assertFalse($organizationIdA->isEqual($organizationIdB));
        $this->assertFalse($organizationIdA->isEqual($organizationIdC));
        $this->assertTrue($organizationIdA->isEqual($organizationIdD));
        $this->assertFalse($organizationIdB->isEqual($organizationIdC));
        $this->assertFalse($organizationIdB->isEqual($organizationIdD));
        $this->assertFalse($organizationIdC->isEqual($organizationIdD));
    }
}
