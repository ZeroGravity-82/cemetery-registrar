<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetailsType extends CustomJsonType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = BankDetails::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'bank_details';

    /**
     * {@inheritdoc}
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\array_key_exists('bankName',             $decodedValue) ||
            !\array_key_exists('bik',                  $decodedValue) ||
            !\array_key_exists('correspondentAccount', $decodedValue) ||
            !\array_key_exists('currentAccount',       $decodedValue);
        if ($isInvalidValue) {
            throw new \RuntimeException(\sprintf('Неверный формат банковских реквизитов: "%s".', $value));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        /** @var BankDetails $value */
        return [
            'bankName'             => $value->bankName()->value(),
            'bik'                  => $value->bik()->value(),
            'correspondentAccount' => $value->correspondentAccount()?->value(),
            'currentAccount'       => $value->currentAccount()->value(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): BankDetails
    {
        return new BankDetails(
            $decodedValue['bankName'],
            $decodedValue['bik'],
            $decodedValue['correspondentAccount'],
            $decodedValue['currentAccount'],
        );
    }
}
