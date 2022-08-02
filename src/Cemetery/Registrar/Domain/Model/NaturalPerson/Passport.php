<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Passport
{
    /**
     * @throws Exception when the series is empty
     * @throws Exception when the number is empty
     * @throws Exception when the issuing date is in the future
     * @throws Exception when the issuing authority name is empty
     * @throws Exception when the division code (if any) is empty
     */
    public function __construct(
        private string             $series,
        private string             $number,
        private \DateTimeImmutable $issuedAt,
        private string             $issuedBy,
        private ?string            $divisionCode,
    ) {
        $this->assertValidSeries($series);
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
        $this->assertValidIssuedBy($issuedBy);
        $this->assertValidDivisionCode($this->divisionCode);
    }

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

    public function issuedBy(): string
    {
        return $this->issuedBy;
    }

    public function divisionCode(): ?string
    {
        return $this->divisionCode;
    }

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
     * @throws Exception when the series is empty
     */
    private function assertValidSeries(string $series): void
    {
        $this->assertNotEmpty($series, 'Серия паспорта');
    }

    /**
     * @throws Exception when the number is empty
     */
    private function assertValidNumber(string $number): void
    {
        $this->assertNotEmpty($number, 'Номер паспорта');
    }

    /**
     * @throws Exception when the issuing date is in the future
     */
    private function assertValidIssuedAt(\DateTimeImmutable $issuedAt): void
    {
        $now = new \DateTimeImmutable();
        if ($issuedAt > $now) {
            throw new Exception('Дата выдачи паспорта не может иметь значение из будущего.');
        }
    }

    /**
     * @throws Exception when the issuing authority name is empty
     */
    private function assertValidIssuedBy(string $issuedBy): void
    {
        $this->assertNotEmpty($issuedBy, 'Наименование органа, выдавшего паспорт,');
    }

    /**
     * @throws Exception when the division code is empty
     */
    private function assertValidDivisionCode(?string $divisionCode): void
    {
        if ($divisionCode === null) {
            return;
        }
        $this->assertNotEmpty($divisionCode, 'Код подразделения');
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
