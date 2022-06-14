<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\AbstractInn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Inn extends AbstractInn
{
    private const INN_LENGTH = 12;

    /**
     * {@inheritdoc}
     */
    protected function innLength(): int
    {
        return self::INN_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertValidCheckDigits(string $value): void
    {
        $checkDigit1 = (int) $value[$this->innLength() - 2];
        $checkDigit2 = (int) $value[$this->innLength() - 1];
        $checkValue1 = $this->calculateCheckDigit($value, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
        $checkValue2 = $this->calculateCheckDigit($value, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
        if ($checkDigit1 !== $checkValue1 || $checkDigit2 !== $checkValue2) {
            $this->throwInvalidCheckDigitsException();
        }
    }
}
