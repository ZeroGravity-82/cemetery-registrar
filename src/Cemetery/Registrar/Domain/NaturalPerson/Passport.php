<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Passport
{
    /**
     * @param string             $series
     * @param string             $number
     * @param \DateTimeImmutable $issuedAt
     * @param string             $issuedBy
     */
    public function __construct(
        private string             $series,
        private string             $number,
        private \DateTimeImmutable $issuedAt,
        private string             $issuedBy,
    ) {
        $this->assertValidSeries($series);
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
        $this->assertValidIssuedBy($issuedBy);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            '%s %s, issued at %s by %s',
            $this->getSeries(),
            $this->getNumber(),
            $this->getIssuedAt()->format('d.m.Y'),
            $this->getIssuedBy()
        );
    }

    /**
     * @return string
     */
    public function getSeries(): string
    {
        return $this->series;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getIssuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    /**
     * @return string
     */
    public function getIssuedBy(): string
    {
        return $this->issuedBy;
    }

    /**
     * @param self $passport
     *
     * @return bool
     */
    public function isEqual(self $passport): bool
    {
        $isSameSeries   = $passport->getSeries() === $this->getSeries();
        $isSameNumber   = $passport->getNumber() === $this->getNumber();
        $isSameIssuedAt = $passport->getIssuedAt()->format('Y-m-d') === $this->getIssuedAt()->format('Y-m-d');
        $isSameIssuedBy = $passport->getIssuedBy() === $this->getIssuedBy();

        return $isSameSeries && $isSameNumber && $isSameIssuedAt && $isSameIssuedBy;
    }

    /**
     * @param string $series
     */
    private function assertValidSeries(string $series): void
    {
        $this->assertNotEmpty($series, 'passport series');
    }

    /**
     * @param string $number
     */
    private function assertValidNumber(string $number): void
    {
        $this->assertNotEmpty($number, 'passport number');
    }

    /**
     * @param \DateTimeImmutable $issuedAt
     */
    private function assertValidIssuedAt(\DateTimeImmutable $issuedAt): void
    {
        $now = new \DateTimeImmutable();
        if ($issuedAt > $now) {
            throw new \InvalidArgumentException('Passport cannot be issued in the future.');
        }
    }

    /**
     * @param string $issuedBy
     */
    private function assertValidIssuedBy(string $issuedBy): void
    {
        $this->assertNotEmpty($issuedBy, 'passport issued by');
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws \InvalidArgumentException when the value is empty
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException(\sprintf('%s value cannot be empty string.', ucfirst($name)));
        }
    }
}