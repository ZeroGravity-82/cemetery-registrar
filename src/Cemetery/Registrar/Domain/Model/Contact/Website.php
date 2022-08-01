<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Contact;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Website
{
    private const DOMAIN_NAME_MAX_LENGTH = 253;
    private const LABEL_MAX_LENGTH       = 63;

    /**
     * @throws Exception when the website address is empty
     * @throws Exception about invalid website address format
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $website): bool
    {
        return $website->value() === $this->value();
    }

    /**
     * @throws Exception when the website address is empty
     * @throws Exception about invalid website address format
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidFullLength($value);
        $this->assertValidLabelsLength($value);
        $this->assertValidFormat($value);
    }

    /**
     * @throws Exception when the website address is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Адрес веб-сайта не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when website address format is invalid
     */
    private function assertValidFullLength(string $value): void
    {
        $value = $this->trim($value);
        if (\mb_strlen($value) > self::DOMAIN_NAME_MAX_LENGTH) {
            $this->throwInvalidFormatException();
        }
    }

    /**
     * @throws Exception when website address format is invalid
     */
    private function assertValidLabelsLength(string $value): void
    {
        $value = $this->trim($value);
        foreach (\explode('.', $value) as $label) {
            if (\mb_strlen($label) > self::LABEL_MAX_LENGTH) {
                $this->throwInvalidFormatException();
            }
        }
    }

    /**
     * @throws Exception when website address format is invalid
     */
    private function assertValidFormat(string $value): void
    {
        $value              = $this->trim($value);
        $alphanumericSymbol = '[a-z\p{Cyrillic}\d]';
        $alphabetSymbol     = '[a-z\p{Cyrillic}]';
        $pattern            = \sprintf(
            '/^(%s(-*%s)*)(\.(%s(-*%s)*))*\.%s{2,}$/iu',
            $alphanumericSymbol,
            $alphanumericSymbol,
            $alphanumericSymbol,
            $alphanumericSymbol,
            $alphabetSymbol
        );
        if (!\preg_match($pattern, $value)) {
            $this->throwInvalidFormatException();
        }
    }

    private function trim(string $value): string
    {
        $value = \str_replace(['http://', 'https://'], '', $value);

        return \preg_replace('~/$~', '', $value);                     // remove trailing slash
    }

    /**
     * @throws Exception about invalid website address format
     */
    private function throwInvalidFormatException(): void
    {
        throw new Exception('Неверный формат адреса веб-сайта.');
    }
}
