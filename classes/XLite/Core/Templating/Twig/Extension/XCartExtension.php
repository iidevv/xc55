<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Extension;

use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\Extension\EscaperExtension;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;
use XLite\Core\Templating\Twig\Functions;
use XLite\Core\Templating\Twig\TokenParser\Form;
use XLite\Core\Translation\Label;
use XLite\Core\Templating\Twig\Node\Expression\IntactArgNamesFunction;

class XCartExtension extends AbstractExtension
{
    private Functions $functions;

    public function __construct(Functions $functions)
    {
        $this->functions = $functions;
    }

    public function getTokenParsers(): array
    {
        return [new Form()];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'widget',
                [$this->functions, 'widget'],
                [
                    'is_variadic'       => true,
                    'needs_environment' => true,
                    'node_class'        => IntactArgNamesFunction::class,
                ]
            ),
            new TwigFunction(
                'widget_list',
                [$this->functions, 'widgetList'],
                [
                    'is_variadic'       => true,
                    'needs_environment' => true,
                    'node_class'        => IntactArgNamesFunction::class,
                ]
            ),
            new TwigFunction(
                'svg',
                [$this->functions, 'svg'],
                [
                    'needs_environment' => true
                ]
            ),
            new TwigFunction(
                't',
                [$this->functions, 't']
            ),
            new TwigFunction(
                'url',
                [$this->functions, 'url'],
                [
                    'needs_environment' => true
                ]
            ),
            new TwigFunction(
                'xurl',
                [$this->functions, 'xurl'],
                [
                    'needs_environment' => true
                ]
            ),
            new TwigFunction(
                'asset',
                [$this->functions, 'asset'],
                [
                    'needs_environment' => true
                ]
            ),
        ];
    }

    public function getFilters(): array
    {
        return [
            // TODO: remove to get back to the default escaping strategy when escaping-on-input will be removed (HTML Purifier)
            new TwigFilter('escape', 'XLite\Core\Templating\Twig\Extension\xcart_twig_escape_filter', ['needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe']),
        ];
    }
}

// phpcs:disable
/**
 * Escapes a string.
 *
 * A copy of standard Twig 3.3.7 'twig_escape_filter' function with 'double_encode' set to false
 *
 * @param mixed  $string     The value to be escaped
 * @param string $strategy   The escaping strategy
 * @param string $charset    The charset
 * @param bool   $autoescape Whether the function is called by the auto-escaping feature (true) or by the developer (false)
 *
 * @return string
 */
function xcart_twig_escape_filter(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
{
    if (
        $autoescape
        && (
            $string instanceof Markup
            || (
                $string instanceof Label
                && $string->translate() instanceof Markup
            )
        )
    ) {
        return $string;
    }

    if ($autoescape && $string instanceof \XLite\View\AView) {
        return $string->getContent();
    }

    if (!\is_string($string)) {
        if (\is_object($string) && method_exists($string, '__toString')) {
            if ($autoescape) {
                $c = \get_class($string);
                $ext = $env->getExtension(EscaperExtension::class);
                if (!isset($ext->safeClasses[$c])) {
                    $ext->safeClasses[$c] = [];
                    foreach (class_parents($string) + class_implements($string) as $class) {
                        if (isset($ext->safeClasses[$class])) {
                            $ext->safeClasses[$c] = array_unique(array_merge($ext->safeClasses[$c], $ext->safeClasses[$class]));
                            foreach ($ext->safeClasses[$class] as $s) {
                                $ext->safeLookup[$s][$c] = true;
                            }
                        }
                    }
                }
                if (isset($ext->safeLookup[$strategy][$c]) || isset($ext->safeLookup['all'][$c])) {
                    return (string) $string;
                }
            }

            $string = (string) $string;
        } elseif (\in_array($strategy, ['html', 'js', 'css', 'html_attr', 'url'])) {
            return $string;
        }
    }

    if ('' === $string) {
        return '';
    }

    if (null === $charset) {
        $charset = $env->getCharset();
    }

    switch ($strategy) {
        case 'html':
            // see https://www.php.net/htmlspecialchars

            // Using a static variable to avoid initializing the array
            // each time the function is called. Moving the declaration on the
            // top of the function slow downs other escaping strategies.
            static $htmlspecialcharsCharsets = [
                'ISO-8859-1' => true, 'ISO8859-1' => true,
                'ISO-8859-15' => true, 'ISO8859-15' => true,
                'utf-8' => true, 'UTF-8' => true,
                'CP866' => true, 'IBM866' => true, '866' => true,
                'CP1251' => true, 'WINDOWS-1251' => true, 'WIN-1251' => true,
                '1251' => true,
                'CP1252' => true, 'WINDOWS-1252' => true, '1252' => true,
                'KOI8-R' => true, 'KOI8-RU' => true, 'KOI8R' => true,
                'BIG5' => true, '950' => true,
                'GB2312' => true, '936' => true,
                'BIG5-HKSCS' => true,
                'SHIFT_JIS' => true, 'SJIS' => true, '932' => true,
                'EUC-JP' => true, 'EUCJP' => true,
                'ISO8859-5' => true, 'ISO-8859-5' => true, 'MACROMAN' => true,
            ];

            if (isset($htmlspecialcharsCharsets[$charset])) {
                return htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, $charset, false);
            }

            if (isset($htmlspecialcharsCharsets[strtoupper($charset)])) {
                // cache the lowercase variant for future iterations
                $htmlspecialcharsCharsets[$charset] = true;

                return htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, $charset, false);
            }

            $string = twig_convert_encoding($string, 'UTF-8', $charset);
            $string = htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8', false);

            return iconv('UTF-8', $charset, $string);

        case 'js':
            // escape all non-alphanumeric characters
            // into their \x or \uHHHH representations
            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (!preg_match('//u', $string)) {
                throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9,\._]#Su', function ($matches) {
                $char = $matches[0];

                /*
                 * A few characters have short escape sequences in JSON and JavaScript.
                 * Escape sequences supported only by JavaScript, not JSON, are omitted.
                 * \" is also supported but omitted, because the resulting string is not HTML safe.
                 */
                static $shortMap = [
                    '\\' => '\\\\',
                    '/' => '\\/',
                    "\x08" => '\b',
                    "\x0C" => '\f',
                    "\x0A" => '\n',
                    "\x0D" => '\r',
                    "\x09" => '\t',
                ];

                if (isset($shortMap[$char])) {
                    return $shortMap[$char];
                }

                $codepoint = mb_ord($char, 'UTF-8');
                if (0x10000 > $codepoint) {
                    return sprintf('\u%04X', $codepoint);
                }

                // Split characters outside the BMP into surrogate pairs
                // https://tools.ietf.org/html/rfc2781.html#section-2.1
                $u = $codepoint - 0x10000;
                $high = 0xD800 | ($u >> 10);
                $low = 0xDC00 | ($u & 0x3FF);

                return sprintf('\u%04X\u%04X', $high, $low);
            }, $string);

            if ('UTF-8' !== $charset) {
                $string = iconv('UTF-8', $charset, $string);
            }

            return $string;

        case 'css':
            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (!preg_match('//u', $string)) {
                throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9]#Su', function ($matches) {
                $char = $matches[0];

                return sprintf('\\%X ', 1 === \strlen($char) ? \ord($char) : mb_ord($char, 'UTF-8'));
            }, $string);

            if ('UTF-8' !== $charset) {
                $string = iconv('UTF-8', $charset, $string);
            }

            return $string;

        case 'html_attr':
            if ('UTF-8' !== $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (!preg_match('//u', $string)) {
                throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9,\.\-_]#Su', function ($matches) {
                /**
                 * This function is adapted from code coming from Zend Framework.
                 *
                 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (https://www.zend.com)
                 * @license   https://framework.zend.com/license/new-bsd New BSD License
                 */
                $chr = $matches[0];
                $ord = \ord($chr);

                /*
                 * The following replaces characters undefined in HTML with the
                 * hex entity for the Unicode replacement character.
                 */
                if (($ord <= 0x1f && "\t" != $chr && "\n" != $chr && "\r" != $chr) || ($ord >= 0x7f && $ord <= 0x9f)) {
                    return '&#xFFFD;';
                }

                /*
                 * Check if the current character to escape has a name entity we should
                 * replace it with while grabbing the hex value of the character.
                 */
                if (1 === \strlen($chr)) {
                    /*
                     * While HTML supports far more named entities, the lowest common denominator
                     * has become HTML5's XML Serialisation which is restricted to the those named
                     * entities that XML supports. Using HTML entities would result in this error:
                     *     XML Parsing Error: undefined entity
                     */
                    static $entityMap = [
                        34 => '&quot;', /* quotation mark */
                        38 => '&amp;',  /* ampersand */
                        60 => '&lt;',   /* less-than sign */
                        62 => '&gt;',   /* greater-than sign */
                    ];

                    if (isset($entityMap[$ord])) {
                        return $entityMap[$ord];
                    }

                    return sprintf('&#x%02X;', $ord);
                }

                /*
                 * Per OWASP recommendations, we'll use hex entities for any other
                 * characters where a named entity does not exist.
                 */
                return sprintf('&#x%04X;', mb_ord($chr, 'UTF-8'));
            }, $string);

            if ('UTF-8' !== $charset) {
                $string = iconv('UTF-8', $charset, $string);
            }

            return $string;

        case 'url':
            return rawurlencode($string);

        default:
            static $escapers;

            if (null === $escapers) {
                $escapers = $env->getExtension(EscaperExtension::class)->getEscapers();
            }

            if (isset($escapers[$strategy])) {
                return $escapers[$strategy]($env, $string, $charset);
            }

            $validStrategies = implode(', ', array_merge(['html', 'js', 'url', 'css', 'html_attr'], array_keys($escapers)));

            throw new RuntimeError(sprintf('Invalid escaping strategy "%s" (valid ones: %s).', $strategy, $validStrategies));
    }
}
// phpcs:enable
