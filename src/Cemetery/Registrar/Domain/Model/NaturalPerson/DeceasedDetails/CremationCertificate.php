<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CremationCertificate
{
    /**
     * @throws Exception when the number is empty
     * @throws Exception when the issuing date is in the future
     */
    public function __construct(
        private string             $number,
        private \DateTimeImmutable $issuedAt,
    ) {
        $this->assertValidNumber($number);
        $this->assertValidIssuedAt($issuedAt);
    }

    public function number(): string
    {
        return $this->number;
    }

    public function issuedAt(): \DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function isEqual(self $cremationCertificate): bool
    {
        $isSameNumber   = $cremationCertificate->number() === $this->number();
        $isSameIssuedAt = $cremationCertificate->issuedAt()->format('Y-m-d') === $this->issuedAt()->format('Y-m-d');

        return $isSameNumber && $isSameIssuedAt;
    }

    /**
     * @throws Exception when the number is empty
     */
    private function assertValidNumber(string $number): void
    {
        $this->assertNotEmpty($number);
    }

    /**
     * @throws Exception when the issuing date is in the future
     */
    private function assertValidIssuedAt(\DateTimeImmutable $issuedAt): void
    {
        $now = new \DateTimeImmutable();
        if ($issuedAt > $now) {
            throw new Exception('Дата выдачи справки о кремации не может иметь значение из будущего.');
        }
    }

    /**
     * @throws Exception when the number is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Номер справки о кремации не может иметь пустое значение.');
        }
    }
}
