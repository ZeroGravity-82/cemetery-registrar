<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Passport
{
    /**
     * @param string             $series
     * @param string             $number
     * @param \DateTimeImmutable $issuedAt
     * @param string             $issuedBy
     * @param string|null        $divisionCode
     */
    public function __construct(
        private readonly string             $series,
        private readonly string             $number,
        private readonly \DateTimeImmutable $issuedAt,
        private readonly string             $issuedBy,
        private readonly ?string            $divisionCode,
    ) {
        $this->assertValidSeries($series);
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
        $this->assertValidIssuedBy($issuedBy);
        $this->assertValidDivisionCode($this->divisionCode);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $string = \sprintf(
            'Паспорт серия %s номер %s, выдан %s %s',
            $this->series(),
            $this->number(),
            $this->issuedBy(),
            $this->issuedAt()->format('d.m.Y'),
        );
        if ($this->divisionCode) {
            $string = \sprintf('%s (код подразделения %s)', $string, $this->divisionCode());
        }

        return $string;
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
     * @return string
     */
    public function issuedBy(): string
    {
        return $this->issuedBy;
    }

    /**
     * @return string|null
     */
    public function divisionCode(): ?string
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
        $isSameSeries       = $passport->series() === $this->series();
        $isSameNumber       = $passport->number() === $this->number();
        $isSameIssuedAt     = $passport->issuedAt()->format('Y-m-d') === $this->issuedAt()->format('Y-m-d');
        $isSameIssuedBy     = $passport->issuedBy() === $this->issuedBy();
        $isSameDivisionCode = $passport->divisionCode() === $this->divisionCode();

        return $isSameSeries && $isSameNumber && $isSameIssuedAt && $isSameIssuedBy && $isSameDivisionCode;
    }

    /**
     * @param string $series
     */
    private function assertValidSeries(string $series): void
    {
        $this->assertNotEmpty($series, 'Серия паспорта');
    }

    /**
     * @param string $number
     */
    private function assertValidNumber(string $number): void
    {
        $this->assertNotEmpty($number, 'Номер паспорта');
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
            throw new \InvalidArgumentException('Дата выдачи паспорта не может иметь значение из будущего.');
        }
    }

    /**
     * @param string $issuedBy
     */
    private function assertValidIssuedBy(string $issuedBy): void
    {
        $this->assertNotEmpty($issuedBy, 'Наименование органа, выдавшего паспорт,');
    }

    /**
     * @param string|null $divisionCode
     */
    private function assertValidDivisionCode(?string $divisionCode): void
    {
        if ($divisionCode === null) {
            return;
        }
        $this->assertNotEmpty($divisionCode, 'Код подразделения');
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws \InvalidArgumentException when the value is empty
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException(\sprintf('%s не может иметь пустое значение.', $name));
        }
    }
}
