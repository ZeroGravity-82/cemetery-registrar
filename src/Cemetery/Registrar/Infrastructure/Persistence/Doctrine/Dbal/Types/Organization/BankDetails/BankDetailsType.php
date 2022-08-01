<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetailsType extends CustomJsonType
{
    protected string $className = BankDetails::class;
    protected string $typeName  = 'bank_details';

    /**
     * @throws \UnexpectedValueException when the decoded value has invalid format
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\is_array($decodedValue)                                 ||
            !\array_key_exists('bankName',             $decodedValue) ||
            !\array_key_exists('bik',                  $decodedValue) ||
            !\array_key_exists('correspondentAccount', $decodedValue) ||
            !\array_key_exists('currentAccount',       $decodedValue);
        if ($isInvalidValue) {
            throw new \UnexpectedValueException(\sprintf(
                'Неверный формат декодированного значения для банковских реквизитов: "%s".',
                $value,
            ));
        }
    }

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
     * @throws Exception when the bank name is invalid
     * @throws Exception when the BIK is invalid
     * @throws Exception when the correspondent account is invalid
     * @throws Exception when the current account is invalid
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
