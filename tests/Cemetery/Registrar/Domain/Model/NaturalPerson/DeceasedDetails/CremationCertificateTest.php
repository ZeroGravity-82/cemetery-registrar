<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CremationCertificateTest extends TestCase
{
    private string             $numberA;
    private string             $numberB;
    private \DateTimeImmutable $issuedAtA;
    private \DateTimeImmutable $issuedAtB;

    public function setUp(): void
    {
        $this->numberA   = '12964';
        $this->numberB   = '811/19';
        $this->issuedAtA = new \DateTimeImmutable('2002-10-28');
        $this->issuedAtB = new \DateTimeImmutable('2011-03-23');
    }

    public function testItSuccessfullyCreated(): void
    {
        $cremationCertificate = new CremationCertificate(
            $this->numberA,
            $this->issuedAtA,
        );
        $this->assertSame($this->numberA, $cremationCertificate->number());
        $this->assertSame($this->issuedAtA->format('Y-m-d'), $cremationCertificate->issuedAt()->format('Y-m-d'));
    }

    public function testItFailsWithEmptyNumberValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new CremationCertificate(
            '',
            $this->issuedAtA,
        );
    }

    public function testItFailsWithNumberValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new CremationCertificate(
            '   ',
            $this->issuedAtA,
        );
    }

    public function testItFailsWithFutureIssuedAtValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Дата выдачи справки о кремации не может иметь значение из будущего.');
        new CremationCertificate(
            $this->numberA,
            (new \DateTimeImmutable())->modify('+1 day'),
        );
    }

    public function testItComparable(): void
    {
        $cremationCertificateA = new CremationCertificate(
            $this->numberA,
            $this->issuedAtA,
        );
        $cremationCertificateB = new CremationCertificate(
            $this->numberB,
            $this->issuedAtA,
        );
        $cremationCertificateC = new CremationCertificate(
            $this->numberA,
            $this->issuedAtB,
        );
        $cremationCertificateD = new CremationCertificate(
            $this->numberA,
            $this->issuedAtA,
        );

        $this->assertFalse($cremationCertificateA->isEqual($cremationCertificateB));
        $this->assertFalse($cremationCertificateA->isEqual($cremationCertificateC));
        $this->assertTrue($cremationCertificateA->isEqual($cremationCertificateD));
        $this->assertFalse($cremationCertificateB->isEqual($cremationCertificateC));
        $this->assertFalse($cremationCertificateB->isEqual($cremationCertificateD));
        $this->assertFalse($cremationCertificateC->isEqual($cremationCertificateD));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Номер справки о кремации не может иметь пустое значение.');
    }
}
