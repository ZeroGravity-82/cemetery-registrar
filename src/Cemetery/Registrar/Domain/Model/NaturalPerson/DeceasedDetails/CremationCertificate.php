<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CremationCertificate
{
    /**
     * @param string             $number
     * @param \DateTimeImmutable $issuedAt
     */
    public function __construct(
        private readonly string             $number,
        private readonly \DateTimeImmutable $issuedAt,
    ) {
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
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
     * @param self $cremationCertificate
     *
     * @return bool
     */
    public function isEqual(self $cremationCertificate): bool
    {
        $isSameNumber   = $cremationCertificate->number() === $this->number();
        $isSameIssuedAt = $cremationCertificate->issuedAt()->format('Y-m-d') === $this->issuedAt()->format('Y-m-d');

        return $isSameNumber && $isSameIssuedAt;
    }

    /**
     * @param string $number
     */
    private function assertValidNumber(string $number): void
    {
        $this->assertNotEmpty($number);
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
            throw new \RuntimeException('Дата выдачи справки о кремации не может иметь значение из будущего.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \RuntimeException when the value is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \RuntimeException('Номер справки о кремации не может иметь пустое значение.');
        }
    }
}
