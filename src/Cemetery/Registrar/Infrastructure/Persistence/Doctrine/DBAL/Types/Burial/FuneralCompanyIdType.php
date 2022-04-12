<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Burial;

use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyIdType extends JsonType
{
    private const TYPE_NAME = 'funeral_company_id';

    /**
     * Registers type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::TYPE_NAME)) {
            return;
        }
        self::addType(self::TYPE_NAME, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if (!$value instanceof FuneralCompanyId) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', FuneralCompanyId::class]
            );
        }

        try {
            return \json_encode(
                ['type' => $value->getIdType(), 'value' => $value->getId()->getValue()],
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?FuneralCompanyId
    {
        if ($value === null || $value instanceof FuneralCompanyId) {
            return $value;
        }

        try {
            $decodedValue = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $this->assertValid($decodedValue);

            return match ($decodedValue['type']) {
                'JuristicPersonId' => new FuneralCompanyId(new JuristicPersonId($decodedValue['value'])),
                'SoleProprietorId' => new FuneralCompanyId(new SoleProprietorId($decodedValue['value'])),
            };
        } catch (\JsonException|\RuntimeException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * @param mixed $decodedValue
     *
     * @throws \RuntimeException when the decoded value has invalid format.
     */
    private function assertValid(mixed $decodedValue): void
    {
        if (!isset($decodedValue['type'], $decodedValue['value'])) {
            throw new \RuntimeException('Неверный формат для ID похоронной фирмы.');
        }
    }
}
