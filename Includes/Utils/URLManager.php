<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

use XLite\Core\Auth;

/**
 * URLManager
 *
 */
abstract class URLManager extends \Includes\Utils\AUtils
{
    /**
     * URL output type codes
     */
    public const URL_OUTPUT_SHORT = 'short';
    public const URL_OUTPUT_FULL  = 'full';

    /**
     * @var bool https flag
     */
    protected static $isHTTPS;

    /**
     * @param      $url
     * @param null $time
     *
     * @return string
     */
    public static function addTimestampToUrl($url, $time = null)
    {
        return static::addParamToUrl(
            $url,
            't',
            $time ?: time()
        );
    }

    /**
     * @param string $url
     * @param string $paramKey
     * @param string $paramValue
     *
     * @return string
     */
    public static function addParamToUrl($url, $paramKey, $paramValue)
    {
        $query     = parse_url($url, PHP_URL_QUERY);
        $delimiter = $query ? '&' : '?';

        return "{$url}{$delimiter}{$paramKey}={$paramValue}";
    }

    /**
     * Return full URL for the resource
     *
     * @param string  $url             URL part to add           OPTIONAL
     * @param boolean $isSecure        Use HTTP or HTTPS         OPTIONAL
     * @param array   $params          URL parameters            OPTIONAL
     * @param string  $output          URL output type           OPTIONAL
     * @param boolean $isSession       Use session ID parameter  OPTIONAL
     * @param boolean $isProtoRelative Use protocol-relative URL OPTIONAL
     *
     * @return string
     */
    public static function getShopURL(
        $url = '',
        $isSecure = null,
        array $params = [],
        $output = null,
        $isSession = null,
        $isProtoRelative = false
    ) {
        $url = trim($url);
        if (!preg_match('/^https?:\/\//Ss', $url)) {
            $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);

            // We are using the protocol-relative URLs for resources
            $protocol = (
                $isSecure === true
                || ($isSecure === null && static::isHTTPS())
                || $hostDetails['force_https'] ?? false
            ) ? 'https' : 'http';

            if (!isset($output)) {
                $output = static::URL_OUTPUT_FULL;
            }

            $host      = $hostDetails[$protocol . '_host'];
            $adminHost = $hostDetails['admin_host'] ?? null;

            if (!empty($adminHost)) {
                $host = static::getHostByLocalUrl($url);

                if (str_contains($host, ':')) {
                    $hostWithoutPort = explode(':', $host, 2)[0];
                } else {
                    $hostWithoutPort = $host;
                }

                if (empty($url) && $adminHost !== $hostWithoutPort && Auth::getInstance()->isAdmin()) {
                    $params[Auth::AUTH_TOKEN] = Auth::getInstance()->getAuthToken();
                }
            }

            if ($host) {
                if (strpos($url, '/') !== 0) {
                    $url = "{$hostDetails['web_dir']}/{$url}";
                }

                foreach ($params as $name => $value) {
                    $url .= (strpos($url, '?') !== false ? '&' : '?') . $name . '=' . $value;
                }

                if ($output == static::URL_OUTPUT_FULL) {
                    if (strpos($url, '//') !== 0) {
                        $url = '//' . $host . $url;
                    }

                    $url = ($isProtoRelative ? '' : ($protocol . ':')) . $url;
                }
            }
        }

        return $url;
    }

    /**
     * @param string $url
     *
     * @return mixed|null
     */
    public static function getHostByLocalUrl(string $url = null)
    {
        $protocol = static::isHTTPS() ? 'https' : 'http';

        $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);
        $host        = $hostDetails[$protocol . '_host'];
        $adminHost   = $hostDetails['admin_host'] ?? null;
        $adminScript = \XLite::getAdminScript();
        $port        = '';

        if (str_contains($host, ':')) {
            $port = explode(':', $host, 2)[1];
        }

        if (!empty($url) && (strpos($url, 'service.php') === 0 || strpos($url, $adminScript) === 0)) {
            return !empty($port) ? $adminHost . ':' . $port : $adminHost;
        }

        return $host;
    }

    /**
     * Return protocol-relative URL for the resource
     *
     * @param string $url    URL part to add OPTIONAL
     * @param array  $params URL parameters OPTIONAL
     * @param string $output URL output type OPTIONAL
     *
     * @return string
     */
    public static function getProtoRelativeShopURL(
        $url = '',
        array $params = [],
        $output = null
    ) {
        if (!preg_match('/^https?:\/\//Ss', $url)) {
            if (!isset($output)) {
                $output = static::URL_OUTPUT_FULL;
            }
            $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);
            $host        = $hostDetails[static::isHTTPS() ? 'https_host' : 'http_host'];
            if ($host) {
                if (strpos($url, '/') !== 0) {
                    $url = "{$hostDetails['web_dir']}/{$url}";
                }

                foreach ($params as $name => $value) {
                    $url .= (strpos($url, '?') !== false ? '&' : '?') . $name . '=' . $value;
                }

                if ($output == static::URL_OUTPUT_FULL) {
                    // We are using the protocol-relative URLs for resources
                    $url = '//' . $host . $url;
                }
            }
        }

        return $url;
    }

    /**
     * Check for secure connection
     *
     * @return boolean
     */
    public static function isHTTPS()
    {
        if (static::$isHTTPS === null) {
            static::$isHTTPS = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) === 'on' || $_SERVER['HTTPS'] == '1'))
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
        }

        return static::$isHTTPS;
    }

    /**
     * Return current URI
     *
     * @return string
     */
    public static function getSelfURI()
    {
        $result = isset($_SERVER['REQUEST_URI']) ? urldecode($_SERVER['REQUEST_URI']) : null;

        if ($result && strpos($result, '/index.php') !== false) {
            $result = str_replace('/index.php', '', $result);
        }

        return $result;
    }

    /**
     * Return current URL
     *
     * @return string
     */
    public static function getCurrentURL()
    {
        return 'http' . (static::isHTTPS() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . (static::getSelfURI() ?: '');
    }

    /**
     * Return current shop URL
     *
     * @return string
     */
    public static function getCurrentShopURL()
    {
        $host = 'http' . (static::isHTTPS() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];

        $webdir = static::getWebdir()
            ? '/' . ltrim(static::getWebdir(), '/')
            : '';

        return $host . $webdir;
    }

    /**
     * Returns webdir.
     *
     * @return string
     */
    public static function getWebdir()
    {
        $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);

        return $hostDetails['web_dir'];
    }

    /**
     * Check if provided string is a valid host part of URL
     *
     * @param string $str Host string
     *
     * @return boolean
     */
    public static function isValidURLHost($str)
    {
        $urlData = parse_url('http://' . $str . '/path');
        $host    = $urlData['host'] . (isset($urlData['port']) ? (':' . $urlData['port']) : '');

        return ($host === $str);
    }

    /**
     * Get list of available shop domains
     *
     * @return array
     */
    public static function getShopDomains()
    {
        $result = [];

        $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);
        $result[]    = $hostDetails['http_host_orig'] ?: $hostDetails['http_host'];
        $result[]    = $hostDetails['https_host_orig'] ?: $hostDetails['https_host'];

        if (!empty($hostDetails['admin_host'])) {
            $result[] = $hostDetails['admin_host'];
        }

        foreach (array_filter($hostDetails['domains']) as $domain) {
            $result[] = $domain;
        }

        return array_unique($result);
    }

    /**
     * @param string $url
     * @param string $param URL parameter to be deleted from URL
     *
     * @return string
     */
    public static function getUrlWithoutParam($url, $param)
    {
        $urlParts = parse_url($url);
        parse_str($urlParts['query'] ?? '', $queryParams);

        if (array_key_exists($param, $queryParams)) {
            unset($queryParams[$param]);
        }

        $queryString = http_build_query($queryParams);

        return ($queryString === '')
            ? $urlParts['path']
            : $urlParts['path'] . '?' . $queryString;
    }

    /**
     * @param string $url
     * @param string $controllerTarget
     * @param string $affiliateId
     * @param string $installationLng
     * @param bool   $useInstallationLng OPTIONAL
     *
     * @return string
     */
    public static function getAffiliatedXCartURL(
        $url,
        $controllerTarget,
        $affiliateId,
        $installationLng,
        $useInstallationLng = true
    ) {
        if (empty($url)) {
            $url = 'https://www.x-cart.com/';
        }

        $params = [];

        if ($useInstallationLng && $installationLng) {
            $params[] = "sl={$installationLng}";
        }

        if ($controllerTarget) {
            $params[] = 'utm_source=XC5admin';
            $params[] = "utm_medium={$controllerTarget}";
            $params[] = 'utm_campaign=XC5admin';
        }

        if ($params) {
            $url .= (strpos($url, '?') ? '&' : '?') . implode('&', $params);
        }

        return $affiliateId
            ? 'https://www.x-cart.com/aff/?aff_id=' . $affiliateId . '&amp;url=' . urlencode($url)
            : $url;
    }

    /**
     * Get URL of the page where license can be purchased
     *
     * @param string $shopUrl
     * @param string $controllerTarget
     * @param string $affiliateId
     * @param string $installationLng
     * @param string $adminEmail
     * @param int    $id       OPTIONAL
     * @param array  $params   OPTIONAL
     * @param bool   $ignoreId OPTIONAL
     *
     * @return string
     */
    public static function getPurchaseURL(
        $shopUrl,
        $controllerTarget,
        $affiliateId,
        $installationLng,
        $adminEmail,
        $id = 0,
        array $params = [],
        $ignoreId = false
    ) {
        $commonParams = [
            'target'    => 'cart',
            'action'    => 'add',
            'store_url' => $shopUrl,
        ];

        if (!$ignoreId) {
            $params['xbid'] = (int) $id !== 0
                ? $id
                : 391; // XC Business Edition xbid = 391
        }

        if ($adminEmail) {
            $commonParams['email'] = $adminEmail;
        }

        $httpQuery = static::buildParamsHttpQuery(
            array_merge($commonParams, $params)
        );

        $marketplace    = \Includes\Utils\ConfigParser::getOptions(['marketplace']);
        $marketplaceUrl = $marketplace['appstore_url'] ?? 'https://market.x-cart.com/';

        return static::getAffiliatedXCartURL(
            "{$marketplaceUrl}?{$httpQuery}",
            $controllerTarget,
            $affiliateId,
            $installationLng
        );
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function buildParamsHttpQuery(array $params)
    {
        $urlParams = [];

        foreach ($params as $k => $v) {
            $urlParams[] = $k . '=' . urlencode($v);
        }

        return implode('&', $urlParams);
    }
}
