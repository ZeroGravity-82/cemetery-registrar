<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class TextExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ucfirst', [$this, 'ucfirst'], ['needs_environment' => true]),
        ];
    }

    /**
     * @param Environment $environment
     * @param string      $value
     *
     * @return string
     */
    public function ucfirst(Environment $environment, string $value): string
    {
        $charset = $environment->getCharset();
        if ($charset) {
            $result =
                \mb_strtoupper(\mb_substr($value, 0, 1, $charset), $charset) .
                \mb_substr($value, 1, \mb_strlen($value, $charset));
        } else {
            $result = \ucfirst($value);
        }

        return $result;
    }
}
