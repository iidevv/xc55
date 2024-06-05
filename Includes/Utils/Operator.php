<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

abstract class Operator extends \Includes\Utils\AUtils
{
    /**
     * Return length of the "dummy" buffer for flush
     *
     * @return int
     */
    protected static function getDummyBufferLength()
    {
        return 4096;
    }

    /**
     * Perform the "flush" itself
     *
     * @return void
     */
    protected static function flushBuffers()
    {
        if (ob_get_level()) {
            @ob_flush();
        }

        flush();
    }

    /**
     * Wrap message into some HTML tags (to fast output)
     *
     * @param string $message  Message to prepare
     * @param string $jsOutput JS output
     *
     * @return string
     */
    protected static function getJSMessage($message, $jsOutput)
    {
        return '<noscript>' . $message . '</noscript>'
             . '<script type="text/javascript">' . $jsOutput . '</script>';
    }


    /**
     * Redirect
     *
     * @param string $location        URL
     * @param int    $code            operation code
     * @param bool   $forceJsRedirect Force JS redirect
     *
     * @return void
     */
    public static function redirect($location, $code = 302, $forceJsRedirect = false)
    {
        $location = \Includes\Utils\Converter::removeCRLF($location);

        if (PHP_SAPI !== 'cli') {
            if ($forceJsRedirect || headers_sent()) {
                $message  = '<a href="' . $location . '">Click here to redirect</a>';
                $jsOutput = 'self.location = \'' . $location . '\';';

                static::flush($message, true, $jsOutput);
            } else {
                header('Location: ' . $location, true, $code);
            }
        }

        exit(0);
    }

    /**
     * Echo message and flush output
     *
     * @param string  $message    Text to display
     * @param boolean $dummyFlush Output extra spaces or not OPTIONAL
     * @param string  $jsOutput   Flag to quick output OPTIONAL
     *
     * @return void
     */
    public static function flush($message, $dummyFlush = false, $jsOutput = null)
    {
        if (PHP_SAPI !== 'cli') {
            // Send extra whitespace before flushing
            if ($dummyFlush) {
                static::pureEcho(str_repeat(' ', static::getDummyBufferLength()));
            }

            // Wrap message into the "<script>" tag
            if (isset($jsOutput)) {
                $message = static::getJSMessage($message, $jsOutput);
            }
        }

        // Print message
        static::pureEcho($message);

        static::flushBuffers();
    }

    /**
     * Echoes the message if it is not in AJAX mode
     *
     * @param string $message Message
     *
     * @return void
     */
    public static function pureEcho($message)
    {
        echo ($message);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public static function generateHash($length = 64)
    {
        if ($length <= 0) {
            return '';
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return mb_substr(bin2hex(openssl_random_pseudo_bytes(round($length / 2))), 0, $length);
        }

        $result = md5(microtime(true) + mt_rand(0, 1000000));

        while (mb_strlen($result) < $length) {
            $result .= md5(microtime(true) + mt_rand(0, 1000000));
        }

        return mb_substr($result, 0, $length);
    }

    /**
     * Purify link
     *
     * @param string $link String
     *
     * @return string
     */
    public static function purifyLink($link)
    {
        if (preg_match('/^{.*}$/', $link)) {
            return $link;
        }

        $html = "<a href='{$link}'>";
        $purifiedHtml = \XLite\Core\HTMLPurifier::purify($html);

        preg_match('/<a href="(.*)">/', $purifiedHtml, $matches);
        $purifiedLink = $matches[1];

        return $purifiedLink;
    }
}
