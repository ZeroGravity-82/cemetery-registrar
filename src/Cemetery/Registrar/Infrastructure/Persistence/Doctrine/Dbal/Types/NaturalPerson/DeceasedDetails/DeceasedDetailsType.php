<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsType extends AbstractCustomJsonType
{
    protected string $className = DeceasedDetails::class;
    protected string $typeName  = 'deceased_details';

    /**
     * @throws \UnexpectedValueException when the decoded value has invalid format
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\is_array($decodedValue)                                 ||
            !\array_key_exists('diedAt',               $decodedValue) ||
            !\array_key_exists('age',                  $decodedValue) ||
            !\array_key_exists('causeOfDeathId',       $decodedValue) ||
            !\array_key_exists('deathCertificate',     $decodedValue) ||
            !\array_key_exists('cremationCertificate', $decodedValue);
        if (!$isInvalidValue) {
            $isInvalidValue = \is_array($decodedValue['deathCertificate']) && (
                !\array_key_exists('series', $decodedValue['deathCertificate']) ||
                !\array_key_exists('number', $decodedValue['deathCertificate']) ||
                !\array_key_exists('issuedAt', $decodedValue['deathCertificate'])
            );
        }
        if (!$isInvalidValue) {
            $isInvalidValue = \is_array($decodedValue['cremationCertificate']) && (
                !\array_key_exists('number', $decodedValue['cremationCertificate']) ||
                !\array_key_exists('issuedAt', $decodedValue['cremationCertificate'])
            );
        }

        if ($isInvalidValue) {
            throw new \UnexpectedValueException(\sprintf(
                'Неверный формат декодированного значения для данных умершего: "%s".',
                $value,
            ));
        }
    }

    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        /** @var DeceasedDetails $value */
        return [
            'diedAt'           => $value->diedAt()->format('Y-m-d'),
            'age'              => $value->age() ? (float) $value->age()->value() : null,
            'causeOfDeathId'   => $value->causeOfDeathId()?->value(),
            'deathCertificate' => $value->deathCertificate()
                ? [
                    'series'   => $value->deathCertificate()->series(),
                    'number'   => $value->deathCertificate()->number(),
                    'issuedAt' => $value->deathCertificate()->issuedAt()->format('Y-m-d'),
                ]
                : null,
            'cremationCertificate' => $value->cremationCertificate()
                ? [
                    'number'   => $value->cremationCertificate()->number(),
                    'issuedAt' => $value->cremationCertificate()->issuedAt()->format('Y-m-d'),
                ]
                : null,
        ];
    }

    /**
     * @throws \UnexpectedValueException when the date of death has invalid format
     * @throws \UnexpectedValueException when the death certificate issue date has invalid format
     * @throws \UnexpectedValueException when the cremation certificate issue date has invalid format
     * @throws Exception                 when the age is invalid
     * @throws Exception                 when the cause of death ID is invalid
     * @throws Exception                 when the death certificate details is invalid
     * @throws Exception                 when the cremation certificate details is invalid
     */
    protected function buildPhpValue(array $decodedValue): DeceasedDetails
    {
        $diedAt = \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['diedAt']);
        if ($diedAt === false) {
            $this->throwInvalidDateFormatException(
                'даты смерти',
                $decodedValue['diedAt'],
            );
        }
        $deathCertificateIssuedAt = isset($decodedValue['deathCertificate']['issuedAt'])
            ? \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['deathCertificate']['issuedAt'])
            : null;
        if ($deathCertificateIssuedAt === false) {
            $this->throwInvalidDateFormatException(
                'даты выдачи свидетельства о смерти',
                $decodedValue['deathCertificate']['issuedAt'],
            );
        }
        $cremationCertificateIssuedAt = isset($decodedValue['cremationCertificate']['issuedAt'])
            ? \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['cremationCertificate']['issuedAt'])
            : null;
        if ($cremationCertificateIssuedAt === false) {
            $this->throwInvalidDateFormatException(
                'даты выдачи справки о кремации',
                $decodedValue['cremationCertificate']['issuedAt'],
            );
        }

        return new DeceasedDetails(
            $diedAt,
            !\is_null($decodedValue['age'])            ? new Age($decodedValue['age'])                       : null,
            !\is_null($decodedValue['causeOfDeathId']) ? new CauseOfDeathId($decodedValue['causeOfDeathId']) : null,
            !\is_null($decodedValue['deathCertificate'])
                ? new DeathCertificate(
                    $decodedValue['deathCertificate']['series'],
                    $decodedValue['deathCertificate']['number'],
                    $deathCertificateIssuedAt,
                )
                : null,
            !\is_null($decodedValue['cremationCertificate'])
                ? new CremationCertificate(
                    $decodedValue['cremationCertificate']['number'],
                    $cremationCertificateIssuedAt,
                )
                : null,
        );
    }

    /**
     * @throws \UnexpectedValueException about invalid date format
     */
    private function throwInvalidDateFormatException(string $name, string $value): void
    {
        throw new \UnexpectedValueException(\sprintf(
            'Неверный формат декодированного значения для %s: "%s".',
            $name,
            $value,
        ));
    }
}
