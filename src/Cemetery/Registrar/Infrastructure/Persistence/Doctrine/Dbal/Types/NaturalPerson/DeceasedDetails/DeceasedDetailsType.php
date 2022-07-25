<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsType extends CustomJsonType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = DeceasedDetails::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'deceased_details';

    /**
     * {@inheritdoc}
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
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
            throw new \RuntimeException(\sprintf('Неверный формат данных умершего: "%s".', $value));
        }
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): DeceasedDetails
    {
        return new DeceasedDetails(
            \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['diedAt']),
            !\is_null($decodedValue['age'])            ? new Age($decodedValue['age'])                       : null,
            !\is_null($decodedValue['causeOfDeathId']) ? new CauseOfDeathId($decodedValue['causeOfDeathId']) : null,
            !\is_null($decodedValue['deathCertificate'])
                ? new DeathCertificate(
                    $decodedValue['deathCertificate']['series'],
                    $decodedValue['deathCertificate']['number'],
                    \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['deathCertificate']['issuedAt']),
                )
                : null,
            !\is_null($decodedValue['cremationCertificate'])
                ? new CremationCertificate(
                    $decodedValue['cremationCertificate']['number'],
                    \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['cremationCertificate']['issuedAt']),
                )
                : null,
        );
    }
}
