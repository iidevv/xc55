<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\InjectLoggerTrait;

/**
 * Request
 */
class Request extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    /**
     * Current method
     */
    public const METHOD_CLI = 'cli';

    /**
     * Current request method
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * Request data (filtered)
     *
     * @var array
     */
    protected $data = [];

    /**
     * Request data (non-filtered)
     *
     * @var array
     */
    protected $nonFilteredData = [];

    /**
     * Request raw data (parsed manually from php://input)
     *
     * @var array
     */
    protected $rawData = [];

    /**
     * Cache for mobile device flag
     *
     * @var null|boolean
     */
    protected static $isMobileDeviceFlag;

    /**
     * current language ISO 639-1 code
     *
     * @var string
     */
    protected $languageCode = '';

    /**
     * @var string
     */
    protected $identifier;

    /**
     * Current request identifier
     *
     * @return string
     */
    public function getUniqueIdentifier()
    {
        if ($this->identifier === null) {
            $this->identifier = hash('md5', uniqid('', true));
        }

        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param $languageCode
     *
     * @return $this
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

        return $this;
    }


    /**
     * Detect Mobile version
     *
     * @return boolean
     */
    public static function isMobileDevice()
    {
        if (static::$isMobileDeviceFlag === null) {
            static::$isMobileDeviceFlag = static::detectMobileDevice();
        }

        return static::$isMobileDeviceFlag;
    }

    /**
     * Defines if the device is a tablet
     *
     * @return boolean
     */
    public static function isTablet()
    {
        return \XLite\Core\MobileDetect::getInstance()->isTablet();
    }

    /**
     * Detect if browser is mobile device
     *
     * @return boolean
     */
    public static function detectMobileDevice()
    {
        return \XLite\Core\MobileDetect::getInstance()->isMobile();
    }

    /**
     * Map request data
     *
     * @param array $data Custom data OPTIONAL
     *
     * @return void
     */
    public function mapRequest(array $data = [])
    {
        if (empty($data)) {
            if ($this->isCLI()) {
                for ($i = 1; count($_SERVER['argv']) > $i; $i++) {
                    $pair = explode('=', $_SERVER['argv'][$i], 2);
                    $data[preg_replace('/^-+/S', '', $pair[0])] = isset($pair[1]) ? trim($pair[1]) : true;
                }
            } else {
                $data = array_merge($this->getCookieData(false), $this->getGetData(false), $this->getPostData(false));
            }
        }

        $this->nonFilteredData = array_replace_recursive($this->data, $this->prepare($data));

        $this->data = $this->normalizeRequestData($this->filterData($this->nonFilteredData));
    }

    /**
     * Return all data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return all non-filtered data
     *
     * @return array
     */
    public function getNonFilteredData()
    {
        return $this->nonFilteredData;
    }

    /**
     * Get manually parsed post data
     */
    public function getRawPostData()
    {
        if (empty($this->rawData)) {
            $this->rawData = [];
            $raw_post_array = explode('&', file_get_contents('php://input'));
            foreach ($raw_post_array as $keyval) {
                $keyval = explode('=', $keyval);
                if (count($keyval) == 2) {
                    $name = urldecode($keyval[0]);
                    $value = urldecode($keyval[1]);
                    $this->rawData[$name] = $value;
                }
            }
        }
        return $this->rawData;
    }

    /**
     * Return parsed request body with handy format
     * This is needed because of names like 'transaction[0].status' which will not be parsed
     * by standart technics
     *
     * @return array
     */
    public function getPostDataWithArrayValues()
    {
        $rawData = $this->getRawPostData();
        $output  = [];
        $pattern = '/(?>([\w]*)\[(\d)\]\.)(\w*?)$/';

        foreach ($rawData as $name => $value) {
            preg_match($pattern, $name, $matches);

            if (count($matches) > 3) {
                [, $name, $index, $subname] = $matches;
                $output[$name][$index][$subname] = $value;
            } else {
                $output[$name] = $value;
            }
        }

        return $output;
    }

    /**
     * Return data from the $_GET global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getGetData($prepare = true)
    {
        return $prepare ? $this->prepare($_GET) : (array)$_GET;
    }

    /**
     * Return data from the $_POST global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getPostData($prepare = true)
    {
        return $prepare ? $this->prepare($_POST) : (array)$_POST;
    }

    /**
     * Return data from the $_COOKIE global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getCookieData($prepare = true)
    {
        return $prepare ? $this->prepare($_COOKIE) : (array)$_COOKIE;
    }

    /**
     * Return data from the $_SERVER global variable
     *
     * @param boolean $prepare Flag OPTIONAL
     *
     * @return array
     */
    public function getServerData($prepare = true)
    {
        return $prepare ? $this->prepare($_SERVER) : (array)$_SERVER;
    }

    /**
     * Return current request method
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Set request method
     *
     * @param string $method New request method
     *
     * @return void
     */
    public function setRequestMethod($method)
    {
        $this->requestMethod = $method;
    }

    /**
     * Check if current request method is "GET"
     *
     * @return boolean
     */
    public function isGet()
    {
        return $this->requestMethod === 'GET';
    }

    /**
     * Check if current request method is "POST"
     *
     * @return boolean
     */
    public function isPost()
    {
        return $this->requestMethod === 'POST';
    }

    /**
     * Check if current request method is "HEAD"
     *
     * @return boolean
     */
    public function isHead()
    {
        return $this->requestMethod === 'HEAD';
    }

    /**
     * Check - is AJAX request or not
     *
     * @return boolean
     */
    public function isAJAX()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * @return string
     */
    public function getAjaxRefererTarget()
    {
        return $_SERVER['HTTP_AJAXREFERERTARGET'] ?? null;
    }

    /**
     * Check for secure connection
     *
     * @return boolean
     */
    public function isHTTPS()
    {
        return \XLite\Core\URLManager::isHTTPS();
    }

    /**
     * Check - is command line interface or not
     *
     * @return boolean
     */
    public function isCLI()
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Getter
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Setter
     *
     * @param string $name  Property name
     * @param mixed  $value Property value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $this->prepare($value);
    }

    /**
     * Check property accessability
     *
     * @param string $name Property name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Un-setter
     *
     * @param string $name Property name
     *
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? self::METHOD_CLI;
        $this->mapRequest();
    }

    /**
     * Unescape single value
     *
     * @param string $value Value to sanitize
     *
     * @return string
     */
    protected function doUnescapeSingle($value)
    {
        return stripslashes($value);
    }

    /**
     * Remove automatically added escaping
     *
     * @param mixed $data Data to sanitize
     *
     * @return mixed
     */
    protected function doUnescape($data)
    {
        return is_array($data)
            ? array_map([$this, __FUNCTION__], $data)
            : $this->doUnescapeSingle($data);
    }

    /**
     * Normalize request data
     *
     * @param mixed $request Request data
     *
     * @return mixed
     */
    protected function normalizeRequestData($request)
    {
        if (is_array($request)) {
            array_walk_recursive($request, static function (&$item) {
                $item = \XLite\Core\Converter::filterCurlyBrackets($item);
            });
        } else {
            $request = \XLite\Core\Converter::filterCurlyBrackets($request);
        }

        return $request;
    }

    /**
     * Wrapper for sanitize()
     *
     * @param mixed $data Data to sanitize
     *
     * @return mixed
     */
    protected function prepare($data)
    {
        if (is_array($data)) {
            if (isset($data['target']) && !$this->checkControlArgument($data['target'], 'Target')) {
                $data['target'] = \XLite::TARGET_404;
                $data['action'] = null;
            }

            if (isset($data['action']) && !$this->checkControlArgument($data['action'], 'Action')) {
                unset($data['action']);
            }
        }

        return $this->normalizeRequestData($data);
    }

    /**
     * Filter data
     *
     * @param array $data Array of non-filtered data
     *
     * @return array
     */
    protected function filterData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $value = $this->filterData($value);
                } elseif (!empty($value) && !is_numeric($value)) {
                    $value = strip_tags($value);

                    if (
                        strpos($key, \XLite\Core\View\DynamicWidgetRenderer::DYNAMIC_WIDGET_PLACEHOLDER) !== false
                        || strpos($value, \XLite\Core\View\DynamicWidgetRenderer::DYNAMIC_WIDGET_PLACEHOLDER) !== false
                    ) {
                        unset($data[$key]);
                        continue;
                    }
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Check control argument (like target)
     *
     * @param mixed  $value Argument value
     * @param string $name  Argument name
     *
     * @return boolean
     */
    protected function checkControlArgument($value, $name)
    {
        $result = true;

        if (!is_string($value)) {
            $this->logPostponed(LOG_DEBUG, $name . ' has a wrong type', ['trace' => debug_backtrace()]);
            $result = false;
        } elseif (!preg_match('/^[a-z0-9_]*$/Si', $value)) {
            $this->logPostponed(LOG_DEBUG, $name . ' has a wrong type', ['trace' => debug_backtrace()]);
            $result = false;
        }

        return $result;
    }

    // {{{ Cookie

    /**
     * Set cookie
     *
     * @param string  $name  Name
     * @param string  $value Value
     * @param integer $ttl   TTL OPTIONAL
     *
     * @return boolean
     */
    public function setCookie($name, $value, $ttl = 0)
    {
        $result = true;

        $secure = $this->getCookieSecure();
        $ttl = $ttl != 0 ? \XLite\Core\Converter::time() + $ttl : 0;

        if ($secure) {
            $domain = $this->getCookieDomain(true);
            $result = $this->setcookieWrapper(
                $name,
                $value,
                $ttl,
                $this->getCookiePath(false),
                $domain,
                true,
                true
            );
        } else {
            $httpDomain = $this->getCookieDomain(false);
            $result = $this->setcookieWrapper(
                $name,
                $value,
                $ttl,
                $this->getCookiePath(false),
                $httpDomain,
                false,
                true
            ) && $result;

            $httpsDomain = $this->getCookieDomain(true);

            if ($httpDomain != $httpsDomain) {
                $result = $this->setcookieWrapper(
                    $name,
                    $value,
                    $ttl,
                    $this->getCookiePath(true),
                    $httpsDomain,
                    true,
                    true
                ) && $result;
            }
        }

        return $result;
    }

    public function unsetCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }

        $secure = $this->getCookieSecure();

        if ($secure) {
            $domain = $this->getCookieDomain(true);
            $this->setcookieWrapper(
                $name,
                null,
                -1,
                $this->getCookiePath(false),
                $domain,
                true,
                true
            );
        } else {
            $httpDomain = $this->getCookieDomain(false);
            $this->setcookieWrapper(
                $name,
                null,
                -1,
                $this->getCookiePath(false),
                $httpDomain,
                false,
                true
            );

            $httpsDomain = $this->getCookieDomain(true);
            if ($httpDomain != $httpsDomain) {
                $this->setcookieWrapper(
                    $name,
                    null,
                    -1,
                    $this->getCookiePath(true),
                    $httpsDomain,
                    true,
                    true
                );
            }
        }
    }

    /**
     * Wrapper for setcookie PHP function
     */
    public function setcookieWrapper($name, $value = '', $expires = 0, $path = '', $domain = '', $secure = false, $httponly = false, $samesite = null)
    {
        $samesite = $samesite ?: $this->getCookieSameSiteOptionValue();
        $domain = $this->getCookieHostOnlyOptionValue() ? '' : $domain;

        if (PHP_VERSION_ID >= 70300) {
            $options = [
                'expires' => $expires,
                'path'    => $path,
                'domain'  => $domain,
                'secure'  => $secure,
                'httponly' => $httponly,
            ];

            if ($samesite) {
                $options['samesite'] = $samesite;
            }

            \XLite::getInstance()->saveCookie($name, (string)$value, $options);
        } else {
            if ($samesite) {
                // Use a bug in PHP prior to v7.3.0 to pass 'samesite' option into cookie
                $path .= '; samesite=' . $this->getCookieSameSiteOptionValue() . ';';
            }

            @setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
        }
    }

    /**
     * Get host / domain for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected function getCookieDomain($secure = false)
    {
        $url = $this->getCookieURL($secure);

        return strpos($url['host'], '.') === false ? false : $url['host'];
    }

    /**
     * Get URL path for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected function getCookiePath($secure = false)
    {
        $url = $this->getCookieURL($secure);

        return $url['path'] ?? '/';
    }

    /**
     * Get samesite option value
     *
     * @return string
     */
    protected function getCookieSameSiteOptionValue()
    {
        return \Includes\Utils\ConfigParser::getOptions(['other', 'cookie_samesite']);
    }

    /**
     * Get hostonly option value
     *
     * @return string
     */
    protected function getCookieHostOnlyOptionValue()
    {
        return \Includes\Utils\ConfigParser::getOptions(['other', 'cookie_hostonly']);
    }

    /**
     * Get parsed URL for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return array
     */
    protected function getCookieURL($secure = false)
    {
        $url = $secure
            ? 'https://' .  \Includes\Utils\ConfigParser::getOptions(['host_details', 'https_host'])
            : 'http://' . \Includes\Utils\ConfigParser::getOptions(['host_details', 'http_host']);

        $url .= \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']);

        return parse_url($url);
    }

    protected function getCookieSecure()
    {
        if (\XLite::isAdminZone()) {
            return \XLite\Core\Config::getInstance()->Security->admin_security;
        } else {
            return \XLite\Core\Config::getInstance()->Security->customer_security;
        }
    }

    // }}}

    // {{{ Bot detected

    /**
     * Cookie cell for robot mark
     */
    public const COOKIE_ROBOT = 'is_robot';

    /**
     * Bot flag
     *
     * @var   boolean
     */
    protected $botFlag;

    /**
     * Bot signatures
     *
     * @var array
     */
    protected $botSignatures =  [
        'X-Cart info'    => [
            'X-Cart info',
            'X-Cart Catalog Generator',
        ],
        'Google'        => [
            'Mediapartners-Google',
            'Google Page Speed Insights',
            'Google-Adwords-Instant',
        ],
        'Yandex'         => [
            'YandexImages',
            'YandexMetrika',
            'YandexImageResizer',
            'YandexVideoParser',
        ],
        'Yahoo'            => [
            'Slurp',
            'YahooSeeker',
        ],
        'Ask'            => [
            'Teoma',
        ],
        'Baidu'          => [
            'baiduspider',
        ],
        'Qihoo' => [
            'Qihoo',
        ],
        'Tencent' => [
            'Sosospider',
        ],
        'Entireweb.com' => [
            'Speedy Spider',
        ],
        'WuKong' => [
            'WukongSpider',
        ],
        'NHN Corporation' => [
            'Yeti',
        ],
        'other' => [
            'special_archiver',
            'SEOkicks',
            'idmarch',
            'Qwantify',
            'TagVisit',
            'FastCrawler',
            'Pandalytics',
            'Veooz',
            'Mappy',
            'Nmap Scripting Engine',
            'linkfluence.com',
        ],
    ];

    /**
     * Check - current user is bot or not
     *
     * @return boolean
     */
    public function isBot()
    {
        if ($this->botFlag === null) {
            $this->botFlag = false;

            if (!$this->isCLI() && !empty($_SERVER['HTTP_USER_AGENT'])) {
                if (isset($_COOKIE[static::COOKIE_ROBOT])) {
                    if (substr($_COOKIE[static::COOKIE_ROBOT], 0, -1) == hash('md4', $_SERVER['HTTP_USER_AGENT'])) {
                        $this->botFlag = (bool)substr($_COOKIE[static::COOKIE_ROBOT], -1);
                    } else {
                        $this->unsetCookie(static::COOKIE_ROBOT);
                    }
                } else {
                    $this->botFlag = $this->detectBot();
                    if ($this->botFlag) {
                        $this->setCookie(static::COOKIE_ROBOT, hash('md4', $_SERVER['HTTP_USER_AGENT']) . ((int) $this->botFlag));
                    }
                }
            }
        }

        return $this->botFlag;
    }

    /**
     * Detect bot
     *
     * @return boolean
     */
    protected function detectBot()
    {
        $result = false;

        if (stristr($_SERVER['HTTP_USER_AGENT'], 'bot') !== false) {
            return true;
        }

        foreach ($this->botSignatures as $name => $signatures) {
            foreach ($signatures as $signature) {
                if (stristr($_SERVER['HTTP_USER_AGENT'], $signature) !== false) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    // }}}

    /**
     * Returns customer ip
     * https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-For
     *
     * @return string
     */
    public function getClientIp()
    {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ipAddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipAddress = 'UNKNOWN';
        }

        $ipAddress = explode(',', $ipAddress);

        return trim(reset($ipAddress));
    }

    /**
     * Return true if current userAgent is IE
     *
     * @return boolean
     */
    public function isIE()
    {
        $result = false;

        // Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; NP06; rv:11.0) like Gecko
        $ua = htmlentities($this->getClientUserAgent(), ENT_QUOTES, 'UTF-8');
        if (preg_match('~MSIE|Internet Explorer~i', $ua) || preg_match('~Trident/7\.0;(?:.*) rv:11\.0~i', $ua)) {
            $result = true;
        }

        return $result;
    }

    /**
     * Returns customer user agent
     *
     * @return string
     */
    public function getClientUserAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }

        return 'UNKNOWN';
    }
}
