<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\Model\Profile;

/**
 * Common operations repository
 */
class Operator extends \XLite\Base\Singleton
{
    /**
     * Token characters list
     *
     * @var array
     */
    protected $chars = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', '_', '|', '!', '^', '*', '-', '~',
    ];

    /**
     * Redirect
     *
     * @param string  $location URL
     * @param integer $code     Operation code OPTIONAL
     *
     * @return void
     */
    public static function redirect($location, $code = 302)
    {
        static::setHeaderLocation($location, $code);
        static::finish();
    }

    /**
     * Try to make HEAD request and check if resource is available. Returns headers if request is successful.
     *
     * @param string $url URL
     *
     * @return mixed
     */
    public static function checkURLAvailability($url)
    {
        $url = ltrim($url, '/');
        $result = null;

        $bouncer = new \XLite\Core\HTTP\Request($url);
        $bouncer->verb = 'HEAD';
        $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, true);
        $response = $bouncer->sendRequest();

        if ($response && $response->code == 200) {
            $result = $response->headers;
        } elseif ($response) {
            unset($bouncer);
            $bouncer = new \XLite\Core\HTTP\Request($url);
            $bouncer->verb = 'GET';
            $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, true);
            $response = $bouncer->sendRequest();

            if ($response && $response->code == 200) {
                $result = $response->headers;
            }
        }

        return $result;
    }

    /**
     * Curls URL and writes it to file
     *
     * @param string $url URL
     *
     * @return boolean|null
     * @throw \Exception
     */
    public static function writeURLContentsToFile($url, $file)
    {
        $result = null;

        $bouncer = new \XLite\Core\HTTP\Request($url);
        $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, true);
        $response = $bouncer->requestToFile($file);

        $errmsg = $bouncer->getErrorMessage();
        if ($response && (int) $response->code === 200) {
            $result = true;
        } elseif (
            $errmsg == 'Curl getaddrinfo() thread failed to start (6)'
            || stripos($errmsg, 'Curl Resolving timed out after') !== false
            || stripos($errmsg, 'Curl Connection timed out after') !== false
        ) {
            throw new \Exception($errmsg);
        }

        return $result;
    }

    /**
     * Get URL content
     *
     * @param string $url URL
     *
     * @return string|null
     */
    public static function getURLContent($url)
    {
        $result = null;

        $bouncer = new \XLite\Core\HTTP\Request($url);
        $bouncer->setAdditionalOption(CURLOPT_FOLLOWLOCATION, true);
        $response = $bouncer->sendRequest();

        if ($response && $response->code == 200) {
            $result = $response->body;
        }

        return $result;
    }

    /**
     * Calculate pagination info
     *
     * @param integer $count Items count
     * @param integer $page  Current page index OPTIONAL
     * @param integer $limit Page length limit OPTIONAL
     *
     * @return array (pages count + current page number)
     */
    public static function calculatePagination($count, $page = 1, $limit = 20)
    {
        $count = max(0, (int) $count);
        $limit = max(0, (int) $limit);

        if ($limit == 0 && $count) {
            $pages = 1;
        } else {
            $pages = $count == 0 ? 0 : ceil($count / $limit);
        }

        $page = min($pages, max(1, (int) $page));

        return [$pages, $page];
    }

    /**
     * setHeaderLocation
     *
     * @param string  $location URL
     * @param integer $code     Operation code OPTIONAL
     *
     * @return void
     */
    protected static function setHeaderLocation($location, $code = 302)
    {
        $location = \Includes\Utils\Converter::removeCRLF($location);
        if (empty($location)) {
            $location = '/';
        }

        if (headers_sent()) {
            // HTML meta tags-based redirect
            echo (
                '<script>' . "\n"
                . '<!--' . "\n"
                . 'self.location=\'' . $location . '\';' . "\n"
                . '-->' . "\n"
                . '</script>' . "\n"
                . '<noscript><a href="' . $location . '">Click here to redirect</a></noscript><br /><br />'
            );
        } elseif (\XLite\Core\Request::getInstance()->isAJAX() && $code == 200) {
            // AJAX-based redirect
            \XLite::getInstance()->setStatusCode($code);
            \XLite::getInstance()->addHeader('AJAX-Location', $location, true);
        } else {
            // HTTP-based redirect
            \XLite::getInstance()->setStatusCode($code);
            \XLite::getInstance()->addHeader('Location', $location, true);
        }
    }

    /**
     * finish
     *
     * @return void
     */
    protected static function finish()
    {
        $session = \XLite\Core\Session::getInstance();

        /** @var Profile $profile */
        $profile = Database::getRepo('XLite\Model\Profile')->find($session->get('profile_id'));

        if ($profile) {
            $session->set('salt', $profile->getSalt());
        }

        \XLite::getInstance()->sendResponse();
        \XLite::getInstance()->runPostRequestActions();
        exit(0);
    }

    /**
     * Get class name as keys list
     *
     * @param string|object $class Class name or object
     *
     * @return array
     */
    public function getClassNameAsKeys($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $parts = explode('\\', $class);

        if ($parts[0] === 'XLite') {
            $parts = array_slice($parts, 2);
        } else {
            unset($parts[2]);
        }

        return array_map('strtolower', array_values($parts));
    }

    /**
     * Generate token
     *
     * @param integer $length Length OPTIONAL
     * @param array   $chars  Characters book OPTIONAL
     *
     * @return string
     */
    public function generateToken($length = 32, array $chars = [])
    {
        if (!$chars) {
            $chars = $this->chars;
        }

        $limit = count($chars) - 1;
        $x = explode('.', uniqid('', true));
        mt_srand((int)microtime(true) + (int) hexdec($x[0]) + $x[1]);

        $password = '';
        for ($i = 0; $length > $i; $i++) {
            $password .= $chars[mt_rand(0, $limit)];
        }

        return $password;
    }
}
