<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsFactory
{
    /**
     * @throws Exception       when the death certificate data is incomplete (if any)
     * @throws Exception       when the cremation certificate data is incomplete (if any)
     * @throws \LogicException when the date of death has invalid format
     * @throws \LogicException when the death certificate issue date has invalid format (if any)
     * @throws \LogicException when the cremation certificate issue date has invalid format (if any)
     */
    public function create(
        ?string $diedAt,
        ?int    $age,
        ?string $causeOfDeathId,
        ?string $deathCertificateSeries,
        ?string $deathCertificateNumber,
        ?string $deathCertificateIssuedAt,
        ?string $cremationCertificateNumber,
        ?string $cremationCertificateIssuedAt,
    ): DeceasedDetails {
        $this->assertCompleteDeathCertificateData(
            $deathCertificateSeries,
            $deathCertificateNumber,
            $deathCertificateIssuedAt,
        );
        $this->assertCompleteCremationCertificateData(
            $cremationCertificateNumber,
            $cremationCertificateIssuedAt,
        );

        $diedAt = \DateTimeImmutable::createFromFormat('Y-m-d', $diedAt);
        if ($diedAt === false) {
            $this->throwInvalidDateFormatException('даты смерти');
        }
        $age              = $age            !== null ? new Age($age)                       : null;
        $causeOfDeathId   = $causeOfDeathId !== null ? new CauseOfDeathId($causeOfDeathId) : null;
        $deathCertificate = null;
        if ($deathCertificateSeries   !== null &&
            $deathCertificateNumber   !== null &&
            $deathCertificateIssuedAt !== null) {
            $deathCertificateIssuedAt = \DateTimeImmutable::createFromFormat('Y-m-d', $deathCertificateIssuedAt);
            if ($deathCertificateIssuedAt === false) {
                $this->throwInvalidDateFormatException('даты выдачи свидетельства о смерти');
            }
            $deathCertificate = new DeathCertificate(
                $deathCertificateSeries,
                $deathCertificateNumber,
                $deathCertificateIssuedAt,
            );
        }
        $cremationCertificate = null;
        if ($cremationCertificateNumber !== null &&
            $cremationCertificateIssuedAt !== null) {
            $cremationCertificateIssuedAt = \DateTimeImmutable::createFromFormat('Y-m-d', $cremationCertificateIssuedAt);
            if ($cremationCertificateIssuedAt === false) {
                $this->throwInvalidDateFormatException('даты выдачи справки о кремации');
            }
            $cremationCertificate = new CremationCertificate(
                $cremationCertificateNumber,
                $cremationCertificateIssuedAt,
             );
        }

        return new DeceasedDetails(
            $diedAt,
            $age,
            $causeOfDeathId,
            $deathCertificate,
            $cremationCertificate,
        );
    }

    /**
     * @throws Exception when the death certificate data is incomplete (if any)
     */
    private function assertCompleteDeathCertificateData(
        ?string $deathCertificateSeries,
        ?string $deathCertificateNumber,
        ?string $deathCertificateIssuedAt,
    ): void {
        if ($deathCertificateSeries   === null &&
            $deathCertificateNumber   === null &&
            $deathCertificateIssuedAt === null
        ) {
            return;
        }
        $documentName = 'свидетельства о смерти';
        if ($deathCertificateSeries === null) {
            $this->throwIncompleteDocumentDataException($documentName, 'серии');
        }
        if ($deathCertificateNumber === null) {
            $this->throwIncompleteDocumentDataException($documentName, 'номера');
        }
        if ($deathCertificateIssuedAt === null) {
            $this->throwIncompleteDocumentDataException($documentName, 'даты выдачи');
        }
    }

    /**
     * @throws Exception when the cremation certificate data is incomplete (if any)
     */
    private function assertCompleteCremationCertificateData(
        ?string $cremationCertificateNumber,
        ?string $cremationCertificateIssuedAt,
    ): void {
        if ($cremationCertificateNumber   === null &&
            $cremationCertificateIssuedAt === null
        ) {
            return;
        }
        $documentName = 'справки о смерти';
        if ($cremationCertificateNumber === null) {
            $this->throwIncompleteDocumentDataException($documentName, 'номера');
        }
        if ($cremationCertificateIssuedAt === null) {
            $this->throwIncompleteDocumentDataException($documentName, 'даты выдачи');
        }
    }

    /**
     * @throws Exception about incomplete document data
     */
    private function throwIncompleteDocumentDataException(string $documentName, string $fieldName): void
    {
        throw new Exception(\sprintf(
            'Неполные данные %s: не указано значение для %s.',
            $documentName,
            $fieldName,
        ));
    }

    /**
     * @throws \LogicException about invalid date format
     */
    private function throwInvalidDateFormatException(string $name): void
    {
        throw new \LogicException(\sprintf('Неверный формат %s.', $name));
    }
}
