<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\AbstractInn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Inn extends AbstractInn
{
    private const INN_LENGTH = 12;

    /**
     * {@inheritdoc}
     */
    protected function getInnLength(): int
    {
        return self::INN_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertValidCheckDigits(string $value): void
    {
        $checkDigit1        = $this->calculateCheckDigit($value, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
        $checkDigit2        = $this->calculateCheckDigit($value, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
        $isCheckDigit1Valid = $checkDigit1 === (int) $value[10];
        $isCheckDigit2Valid = $checkDigit2 === (int) $value[11];
        if (!$isCheckDigit1Valid || !$isCheckDigit2Valid) {
            $this->throwIncorrectCheckDigitsException();
        }
    }
}
