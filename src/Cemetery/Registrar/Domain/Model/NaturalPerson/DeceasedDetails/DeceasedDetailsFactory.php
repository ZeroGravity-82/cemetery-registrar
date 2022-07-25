<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsFactory
{
    /**
     * @param string|null $diedAt
     * @param int|null    $age
     * @param string|null $causeOfDeathId
     * @param string|null $deathCertificateSeries
     * @param string|null $deathCertificateNumber
     * @param string|null $deathCertificateIssuedAt
     * @param string|null $cremationCertificateNumber
     * @param string|null $cremationCertificateIssuedAt
     *
     * @return DeceasedDetails
     *
     * @throws \RuntimeException when the death certificate data is incomplete (if any)
     * @throws \RuntimeException when the cremation certificate data is incomplete (if any)
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

        $diedAt           = \DateTimeImmutable::createFromFormat('Y-m-d', $diedAt);
        $age              = $age            !== null ? new Age($age)                       : null;
        $causeOfDeathId   = $causeOfDeathId !== null ? new CauseOfDeathId($causeOfDeathId) : null;
        $deathCertificate = $deathCertificateSeries   !== null &&
                              $deathCertificateNumber   !== null &&
                              $deathCertificateIssuedAt !== null
            ? new DeathCertificate(
                $deathCertificateSeries,
                $deathCertificateNumber,
                \DateTimeImmutable::createFromFormat('Y-m-d', $deathCertificateIssuedAt)
            )
            : null;
        $cremationCertificate = $cremationCertificateNumber !== null && $cremationCertificateIssuedAt !== null
            ? new CremationCertificate(
                $cremationCertificateNumber,
                \DateTimeImmutable::createFromFormat('Y-m-d', $cremationCertificateIssuedAt)
            )
            : null;

        return new DeceasedDetails(
            $diedAt,
            $age,
            $causeOfDeathId,
            $deathCertificate,
            $cremationCertificate,
        );
    }

    /**
     * @param string|null $deathCertificateSeries
     * @param string|null $deathCertificateNumber
     * @param string|null $deathCertificateIssuedAt
     *
     * @throws \RuntimeException when the death certificate data is incomplete (if any)
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
     * @param string|null $cremationCertificateNumber
     * @param string|null $cremationCertificateIssuedAt
     *
     * @throws \RuntimeException when the cremation certificate data is incomplete (if any)
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
     * @param string $documentName document name in the genitive case
     * @param string $fieldName    field name in the genitive case
     *
     * @throws \RuntimeException about incomplete document data
     */
    private function throwIncompleteDocumentDataException(string $documentName, string $fieldName): void
    {
        throw new \RuntimeException(\sprintf(
            'Неполные данные %s: не указано значение для %s.',
            $documentName,
            $fieldName,
        ));
    }
}
