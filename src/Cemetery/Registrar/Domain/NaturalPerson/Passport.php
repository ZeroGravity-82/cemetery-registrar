<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Passport
{
    /**
     * @var string
     */
    private string $series;

    /**
     * @var string
     */
    private string $number;

    /**
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $issuedAt;

    /**
     * @var string
     */
    private string $issuedBy;

    /**
     * @var string|null
     */
    private ?string $divisionCode = null;

    /**
     * @param string|null             $series
     * @param string|null             $number
     * @param \DateTimeImmutable|null $issuedAt
     * @param string|null             $issuedBy
     * @param string|null             $divisionCode
     */
    public function __construct(
        ?string             $series,
        ?string             $number,
        ?\DateTimeImmutable $issuedAt,
        ?string             $issuedBy,
        ?string             $divisionCode,
    ) {
        $this->assertValidSeries($series);
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
        $this->assertValidIssuedBy($issuedBy);
        $this->assertValidDivisionCode($divisionCode);
        $this->series       = $series;
        $this->number       = $number;
        $this->issuedAt     = $issuedAt;
        $this->issuedBy     = $issuedBy;
        $this->divisionCode = $divisionCode;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            '%s %s, issued at %s by %s (division code %s)',
            $this->getSeries(),
            $this->getNumber(),
            $this->getIssuedAt()->format('d.m.Y'),
            $this->getIssuedBy(),
            $this->getDivisionCode(),
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
     * @return string|null
     */
    public function getDivisionCode(): ?string
    {
        return $this->divisionCode;
    }

    /**
     * @param self $passport
     *
     * @return bool
     */
    public function isEqual(self $passport): bool
    {
        $isSameSeries       = $passport->getSeries() === $this->getSeries();
        $isSameNumber       = $passport->getNumber() === $this->getNumber();
        $isSameIssuedAt     = $passport->getIssuedAt()->format('Y-m-d') === $this->getIssuedAt()->format('Y-m-d');
        $isSameIssuedBy     = $passport->getIssuedBy() === $this->getIssuedBy();
        $isSameDivisionCode = $passport->getDivisionCode() === $this->getDivisionCode();

        return $isSameSeries && $isSameNumber && $isSameIssuedAt && $isSameIssuedBy && $isSameDivisionCode;
    }

    /**
     * @param string|null $series
     */
    private function assertValidSeries(?string $series): void
    {
        $this->assertNotEmpty($series, 'passport series');
    }

    /**
     * @param string|null $number
     */
    private function assertValidNumber(?string $number): void
    {
        $this->assertNotEmpty($number, 'passport number');
    }

    /**
     * @param \DateTimeImmutable|null $issuedAt
     */
    private function assertValidIssuedAt(?\DateTimeImmutable $issuedAt): void
    {
        $this->assertNotEmpty($issuedAt, 'passport issued at');
        $this->assertNotIssuedInTheFuture($issuedAt);
    }

    /**
     * @param string|null $issuedBy
     */
    private function assertValidIssuedBy(?string $issuedBy): void
    {
        $this->assertNotEmpty($issuedBy, 'passport issued by');
    }

    /**
     * @param string|null $divisionCode
     */
    private function assertValidDivisionCode(?string $divisionCode): void
    {
        if ($divisionCode === null) {
            return;
        }
        $this->assertNotEmpty($divisionCode, 'division code');
    }

    /**
     * @param mixed  $value
     * @param string $name
     *
     * @throws \InvalidArgumentException when the value is empty
     */
    private function assertNotEmpty(mixed $value, string $name): void
    {
        if ($value === '' || $value === null) {
            throw new \InvalidArgumentException(\sprintf('%s value cannot be empty.', ucfirst($name)));
        }
    }

    /**
     * @param \DateTimeImmutable $issuedAt
     *
    * @throws \InvalidArgumentException when the issued at value is in the future
     */
    private function assertNotIssuedInTheFuture(\DateTimeImmutable $issuedAt): void
    {
        $now = new \DateTimeImmutable();
        if ($issuedAt > $now) {
            throw new \InvalidArgumentException('Passport cannot be issued in the future.');
        }
    }
}
