<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeathCertificate
{
    /**
     * @throws Exception when the series is empty
     * @throws Exception when the number is empty
     * @throws Exception when the issuing date is in the future
     */
    public function __construct(
        private string             $series,
        private string             $number,
        private \DateTimeImmutable $issuedAt,
    ) {
        $this->assertValidSeries($series);
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
    }

    public function series(): string
    {
        return $this->series;
    }

    public function number(): string
    {
        return $this->number;
    }

    public function issuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function isEqual(self $deathCertificate): bool
    {
        $isSameSeries   = $deathCertificate->series() === $this->series();
        $isSameNumber   = $deathCertificate->number() === $this->number();
        $isSameIssuedAt = $deathCertificate->issuedAt()->format('Y-m-d') === $this->issuedAt()->format('Y-m-d');

        return $isSameSeries && $isSameNumber && $isSameIssuedAt;
    }

    /**
     * @throws Exception when the series is empty
     */
    private function assertValidSeries(string $series): void
    {
        $this->assertNotEmpty($series, 'Серия свидетельства о смерти');
    }

    /**
     * @throws Exception when the number is empty
     */
    private function assertValidNumber(string $number): void
    {
        $this->assertNotEmpty($number, 'Номер свидетельства о смерти');
    }

    /**
     * @throws Exception when the issuing date is in the future
     */
    private function assertValidIssuedAt(\DateTimeImmutable $issuedAt): void
    {
        $now = new \DateTimeImmutable();
        if ($issuedAt > $now) {
            throw new Exception('Дата выдачи свидетельства о смерти не может иметь значение из будущего.');
        }
    }

    /**
     * @throws Exception when the value is empty
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if (\trim($value) === '') {
            throw new Exception(\sprintf('%s не может иметь пустое значение.', $name));
        }
    }
}
