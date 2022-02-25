<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Contact;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Website
{
    private const DOMAIN_NAME_MAX_LENGTH = 253;
    private const LABEL_MAX_LENGTH       = 63;

    /**
     * @param string $value
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param self $website
     *
     * @return bool
     */
    public function isEqual(self $website): bool
    {
        return $website->getValue() === $this->getValue();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidFullLength($value);
        $this->assertValidLabelsLength($value);
        $this->assertValidFormat($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the website address is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('Адрес веб-сайта не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the website address is too long
     */
    private function assertValidFullLength(string $value): void
    {
        $value = $this->trim($value);
        if (\mb_strlen($value) > self::DOMAIN_NAME_MAX_LENGTH) {
            throw new \InvalidArgumentException(
                \sprintf('Адрес веб-сайта не может иметь длину более %d символов.', self::DOMAIN_NAME_MAX_LENGTH)
            );
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the fragment of the website address is too long
     */
    private function assertValidLabelsLength(string $value): void
    {
        $value = $this->trim($value);
        foreach (\explode('.', $value) as $label) {
            if (\mb_strlen($label) > self::LABEL_MAX_LENGTH) {
                throw new \InvalidArgumentException(
                    \sprintf('Фрагмент адреса веб-сайта не может иметь длину более %d символов.', self::LABEL_MAX_LENGTH)
                );
            }
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the website address has invalid format
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
            throw new \InvalidArgumentException('Неверный формат адреса веб-сайта.');
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function trim(string $value): string
    {
        $value = \str_replace(['http://', 'https://'], '', $value);

        return \preg_replace('~/$~', '', $value);                     // remove trailing slash
    }
}
