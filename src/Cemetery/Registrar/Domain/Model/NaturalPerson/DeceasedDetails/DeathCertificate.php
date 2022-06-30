<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeathCertificate
{
    /**
     * @param string             $series
     * @param string             $number
     * @param \DateTimeImmutable $issuedAt
     */
    public function __construct(
        private readonly string             $series,
        private readonly string             $number,
        private readonly \DateTimeImmutable $issuedAt,
    ) {
        $this->assertValidSeries($series);
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
    }

    /**
     * @return string
     */
    public function series(): string
    {
        return $this->series;
    }

    /**
     * @return string
     */
    public function number(): string
    {
        return $this->number;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function issuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    /**
     * @param self $deathCertificate
     *
     * @return bool
     */
    public function isEqual(self $deathCertificate): bool
    {
        $isSameSeries   = $deathCertificate->series() === $this->series();
        $isSameNumber   = $deathCertificate->number() === $this->number();
        $isSameIssuedAt = $deathCertificate->issuedAt()->format('Y-m-d') === $this->issuedAt()->format('Y-m-d');

        return $isSameSeries && $isSameNumber && $isSameIssuedAt;
    }

    /**
     * @param string $series
     */
    private function assertValidSeries(string $series): void
    {
        $this->assertNotEmpty($series, 'Серия свидетельства о смерти');
    }

    /**
     * @param string $number
     */
    private function assertValidNumber(string $number): void
    {
        $this->assertNotEmpty($number, 'Номер свидетельства о смерти');
    }

    /**
     * @param \DateTimeImmutable $issuedAt
     *
     * @throws \RuntimeException when the issuing date is in the future
     */
    private function assertValidIssuedAt(\DateTimeImmutable $issuedAt): void
    {
        $now = new \DateTimeImmutable();
        if ($issuedAt > $now) {
            throw new \RuntimeException('Дата выдачи свидетельства о смерти не может иметь значение из будущего.');
        }
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws \RuntimeException when the value is empty
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if (\trim($value) === '') {
            throw new \RuntimeException(\sprintf('%s не может иметь пустое значение.', $name));
        }
    }
}
