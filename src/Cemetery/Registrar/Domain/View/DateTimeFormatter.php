<?php

namespace Cemetery\Registrar\Domain\View;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DateTimeFormatter
{
    public const DATE_FORMAT     = 'd.m.Y';
    public const DATETIME_FORMAT = 'd.m.Y H:i';

    public function formatDate(\DateTimeImmutable $value): string
    {
        return $value->format(self::DATE_FORMAT);
    }

    public function formatDateTime(\DateTimeImmutable $value): string
    {
        return $value->format(self::DATETIME_FORMAT);
    }
}
