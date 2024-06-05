<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

use Includes\Utils\ConfigParser;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Application singleton
 * TODO: to revise
 * TODO[SINGLETON]: lowest priority
 */
// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace,SlevomatCodingStandard.Files.TypeNameMatchesFileName.NoMatchBetweenTypeNameAndFileName
class XLite extends \XLite\Base
{
    /**
     * Core version
     */
    public const XC_VERSION = '5.5.0.29';

    /**
     * Endpoints
     */
    public const CART_SELF  = '';
    public const ADMIN_SELF = 'admin/';

    /**
     * This target will be used if the "target" params is not passed in the request
     */
    public const TARGET_DEFAULT = 'main';
    public const TARGET_404     = 'page_not_found';

    /**
     * Interfaces codes
     */
    /** @deprecated */
    public const ADMIN_INTERFACE    = 'admin';
    /** @deprecated */
    public const CUSTOMER_INTERFACE = 'customer';
    /** @deprecated */
    public const MAIL_INTERFACE     = 'mail';
    /** @deprecated */
    public const COMMON_INTERFACE   = 'common';
    /** @deprecated */
    public const PDF_INTERFACE      = 'pdf';

    /**
     * Predefined interfaces
     */
    public const INTERFACE_WEB     = 'web';
    public const INTERFACE_MAIL    = 'mail';
    public const INTERFACE_PDF     = 'pdf';

    /**
     * Predefined zones
     */
    public const ZONE_CUSTOMER = 'customer';
    public const ZONE_ADMIN    = 'admin';
    public const ZONE_COMMON   = 'common';

    /**
     * Default shop currency code (840 - US Dollar)
     */
    public const SHOP_CURRENCY_DEFAULT = 840;

    /**
     * Temporary variable name for latest cache building time
     */
    public const CACHE_TIMESTAMP = 'cache_build_timestamp';

    /**
     * Producer site URL
     */
    public const PRODUCER_SITE_URL = 'https://www.x-cart.com/';

    /**
     * Name of the form id
     */
    public const FORM_ID = 'xcart_form_id';

    /**
     * URI to check clean URLS availability
     */
    public const CLEAN_URL_CHECK_QUERY = 'check/for/clean/urls.html';

    /**
     * Parsed version.
     * Array with the following keys: major, minor, build, minorFull
     *
     * @var array
     */
    protected $parsedVersion;

    /**
     * Current area flag
     *
     * @var boolean
     */
    protected static $adminZone = false;

    /**
     * URL type flag
     *
     * @var boolean
     */
    protected static $cleanURL = false;

    /**
     * Called controller
     *
     * @var \XLite\Controller\AController
     */
    protected static $controller;

    /**
     * Flag; determines if we need to cleanup (and, as a result, to rebuild) classes and templates cache
     *
     * @var boolean
     */
    protected static $isNeedToCleanupCache = false;

    /**
     * Current currency
     *
     * @var \XLite\Model\Currency
     */
    protected $currentCurrency;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var int
     */
    protected $statusCode = Response::HTTP_OK;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param string $content
     */
    public function addContent(string $content): void
    {
        $this->content .= $content;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     */
    public function setStatusCode($statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool   $replace
     */
    public function addHeader(string $key, string $value, bool $replace = true): void
    {
        if ($replace === true || !isset($this->headers[$key])) {
            $this->headers[$key] = [$value];
        } else {
            $this->headers[$key][] = $value;
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @param array  $options
     */
    public function saveCookie(string $name, string $value, array $options = []): void
    {
        $this->cookies[] = [
            'name'    => $name,
            'value'   => $value,
            'options' => $options
        ];
    }

    /**
     * Check is admin interface
     *
     * @return boolean
     */
    public static function isAdminZone()
    {
        return static::$adminZone;
    }

    /**
     * Return filename of the admin script.
     *
     * @return string
     */
    public static function getAdminScript()
    {
        return static::ADMIN_SELF;
    }

    /**
     * Return filename of the customer script.
     *
     * @return string
     */
    public static function getCustomerScript()
    {
        return static::CART_SELF;
    }

    /**
     * Check is admin interface
     *
     * @return boolean
     */
    public static function isAdminScript()
    {
        return strpos(static::getInstance()->getRequestedScript(), self::ADMIN_SELF) !== false;
    }

    /**
     * Check is cache building
     *
     * @return boolean
     */
    public static function isCacheBuilding()
    {
        return defined('LC_CACHE_BUILDING') && constant('LC_CACHE_BUILDING');
    }

    /**
     * Check if clean URL used
     *
     * @return boolean
     */
    public static function isCleanURL()
    {
        return static::$cleanURL;
    }

    /**
     * @return \XLite\Controller\AController
     */
    public static function getController()
    {
        if (static::$controller === null) {
            $class = static::getControllerClass();

            // If mod_rewrite is disabled and ErrorDocument 404 is set
            //  on check/for/clean/urls.html uri we are trying do display 404
            // This is here to speedup check request
            if (
                \XLite\Core\Request::getInstance()->target === static::TARGET_404
                && isset($_SERVER['REQUEST_URI'])
                && strpos($_SERVER['REQUEST_URI'], static::CLEAN_URL_CHECK_QUERY) !== false
            ) {
                http_response_code(404);
                die();
            };

            if (!$class) {
                \XLite\Core\Request::getInstance()->target = static::TARGET_404;
                $class = static::getControllerClass();
            }

            static::$controller = new $class(\XLite\Core\Request::getInstance()->getData());
            static::$controller->init();
        }

        return static::$controller;
    }

    /**
     * Set controller
     * FIXME - to delete
     *
     * @param mixed $controller Controller OPTIONAL
     *
     * @return void
     */
    public static function setController($controller = null)
    {
        if ($controller instanceof \XLite\Controller\AController || $controller === null) {
            static::$controller = $controller;
        }
    }

    /**
     * Defines the installation language code
     *
     * @return string
     */
    public static function getInstallationLng()
    {
        return ConfigParser::getOptions(['installation', 'installation_lng']);
    }

    /**
     * Return affiliate ID
     *
     * @return string
     */
    public static function getAffiliateId()
    {
        return ConfigParser::getOptions(['affiliate', 'id']);
    }

    /**
     * Return affiliate URL
     *
     * @param string  $url                Url part to add OPTIONAL
     * @param boolean $useInstallationLng Use installation language or not OPTIONAL
     *
     * @return string
     */
    public static function getXCartURL($url = '', $useInstallationLng = true)
    {
        $controllerTarget = '';
        if (static::isAdminZone() && static::getController()) {
            $controllerTarget = static::getController()->getTarget();
        }

        return \Includes\Utils\URLManager::getAffiliatedXCartURL(
            $url,
            $controllerTarget,
            static::getAffiliateId(),
            static::getInstallationLng(),
            $useInstallationLng
        );
    }

    /**
     * Return current target
     *
     * @return string
     */
    protected static function getTarget()
    {
        if (empty(\XLite\Core\Request::getInstance()->target)) {
            \XLite\Core\Request::getInstance()->target = static::dispatchRequest();
        }

        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Assemble and get controller class name
     *
     * @return string
     */
    protected static function getControllerClass()
    {
        return \XLite\Core\Converter::getControllerClass(static::getTarget());
    }

    /**
     * Return current endpoint script
     *
     * @param boolean $check Check if file exists and readable (default: false)
     *
     * @return string
     */
    public function getScript($check = false)
    {
        return static::isAdminZone() ? self::ADMIN_SELF : self::CART_SELF;
    }

    /**
     * Return current endpoint script
     *
     * @return string
     */
    protected function getRequestedScript()
    {
        return trim($_SERVER['PHP_SELF'], '/');
    }

    /**
     * Return current endpoint script
     *
     * @param boolean $index Get index script
     *
     * @return string
     */
    protected function getExpectedScript($index = false)
    {
        $web_dir = ConfigParser::getOptions(['host_details', 'web_dir']);
        $script = $index
            ? 'index.php'
            : ltrim(static::getScript(true), '/');

        return trim($web_dir . '/' . $script, '/');
    }

    /**
     * Return full URL for the resource
     *
     * @param string  $url      Url part to add OPTIONAL
     * @param boolean $isSecure Use HTTP or HTTPS OPTIONAL
     * @param array   $params   Optional URL params OPTIONAL
     *
     * @return string
     */
    public function getShopURL($url = '', $isSecure = null, array $params = [])
    {
        return \XLite\Core\URLManager::getShopURL($url, $isSecure, $params);
    }

    /**
     * @param string $path
     * @param null   $isSecure
     * @param array  $params
     *
     * @return string
     */
    public function getServiceURL($path = '', $isSecure = null, array $params = [])
    {
        if (!$path) {
            $path = '#/';
        }

        if (strpos($path, '#') === 0) {
            $path = 'admin/?target=apps' . $path;
        }

        if ($params) {
            $path .= '?' . http_build_query($params);
        }

        if (strpos($path, '#/available-addons') !== false) {
            $path = str_replace('#/available-addons', '#/installed-addons', $path);
        }

        return \XLite\Core\URLManager::getShopURL($path, $isSecure);
    }

    public static function getAppStoreUrl(): string
    {
        $marketplace = ConfigParser::getOptions(['marketplace']) ?? [];
        return $marketplace['appstore_url'] ?? '';
    }

    /**
     * Return instance of the abstract factory singleton
     *
     * @return \XLite\Model\Factory
     */
    public function getFactory()
    {
        return \XLite\Model\Factory::getInstance();
    }

    /**
     * Perform an action and redirect
     *
     * @return void
     */
    public function runController()
    {
        static::getController()->handleRequest();
    }

    /**
     * Return viewer object
     *
     * @return \XLite\View\Controller|void
     */
    public function getViewer()
    {
        $this->runController();

        $viewer = static::getController()->getViewer();
        $viewer->init();

        return $viewer;
    }

    /**
     * Process request
     *
     * @return \XLite
     */
    public function processRequest()
    {
        if (!static::isAdminZone()) {
            \XLite\Core\Router::getInstance()->processCleanUrls();
        }

        $this->runController();

        static::getController()->processRequest();

        return $this;
    }

    /**
     * Run customer zone application
     */
    public function runCustomerZone()
    {
        $this->run()->processRequest();
    }

    /**
     * Run application
     *
     * @param boolean $adminZone Admin interface flag OPTIONAL
     *
     * @return \XLite
     */
    public function run($adminZone = false)
    {
        // Set current area
        static::$adminZone = (bool) $adminZone;

        // Clear some data
        static::clearDataOnStartup();

        // Initialize logger
        \XLite\Logger::getInstance();

        if (static::$adminZone === true) {
            // Set skin for admin interface
            \XLite\Core\Layout::getInstance()->setInterfaceZone(\XLite::INTERFACE_WEB, \XLite::ZONE_ADMIN);
        }

        return $this;
    }

    /**
     * Get current currency
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        if ($this->currentCurrency === null) {
            $this->currentCurrency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
                ->find(\XLite\Core\Config::getInstance()->General->shop_currency ?: static::SHOP_CURRENCY_DEFAULT);
        }

        return $this->currentCurrency;
    }

    /**
     * Return current action
     *
     * @return mixed
     */
    protected function getAction()
    {
        return \XLite\Core\Request::getInstance()->action;
    }

    /**
     * Clear some data
     *
     * @return void
     */
    protected function clearDataOnStartup()
    {
        static::$controller = null;
        \XLite\Model\CachingFactory::clearCache();
    }

    // {{{ Clean URLs support

    /**
     * Dispatch request
     *
     * @return string
     */
    protected static function dispatchRequest()
    {
        $result = static::TARGET_DEFAULT;

        if (static::$adminZone === false && isset(\XLite\Core\Request::getInstance()->url)) {
            if (static::isCheckForCleanURL()) {
                $result = null;
                // Request to detect support of clean URLs
                // Just display 'OK' and exit to speedup this checking
                die('OK');
            };

            if (LC_USE_CLEAN_URLS) {
                // Get target
                $result = static::getTargetByCleanURL();
            } else {
                $result = static::TARGET_404;
            }
        }

        return $result;
    }

    /**
     * Return target by clean URL
     *
     * @return string
     */
    protected static function getTargetByCleanURL()
    {
        $tmp = \XLite\Core\Request::getInstance();
        [$target, $params] = \XLite\Core\Converter::parseCleanUrl($tmp->url, $tmp->last, $tmp->rest, $tmp->ext);

        if ($target && $params) {
            $redirectUrl = \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->buildURL($target, $params);
            $redirectUrl = strtok($redirectUrl, '?');
            $web_dir = ConfigParser::getOptions(['host_details', 'web_dir']);
            $selfURI = substr(strtok(\Includes\Utils\URLManager::getSelfURI(), '?'), strlen($web_dir) + 1);

            if (LC_USE_CLEAN_URLS && \XLite\Core\Router::getInstance()->isUseLanguageUrls()) {
                $language = \XLite\Core\Session::getInstance()->getLanguage();

                $selfURI = strpos($selfURI, $language->getCode() . '/') === 0
                    ? substr($selfURI, 3)
                    : $selfURI;
            }

            if ($redirectUrl !== $selfURI && !\XLite\Core\Request::getInstance()->isAJAX()) {
                $ttl = 86400;
                $expiresTime = gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT';

                \XLite::getInstance()->addHeader('Cache-Control', "max-age=$ttl, must-revalidate");
                \XLite::getInstance()->addHeader('Expires', $expiresTime);

                \XLite\Core\Operator::redirect(
                    \XLite\Core\URLManager::getShopURL($redirectUrl),
                    301
                );
            }
        }

        if (!empty($target)) {
            $tmp->mapRequest($params);

            static::$cleanURL = true;
        }

        return $target;
    }

    /**
     * Return true if check for clean URLs availability was requested
     *
     * @return boolean
     */
    protected static function isCheckForCleanURL()
    {
        $tmp = \XLite\Core\Request::getInstance();
        $parts = [$tmp->rest, $tmp->last, $tmp->url . $tmp->ext];
        $query = implode('/', array_filter($parts));

        return $query == static::CLEAN_URL_CHECK_QUERY;
    }

    // }}}

    // {{{ Form Id

    /**
     * Create the form id for the widgets
     *
     * @param boolean $createNewFormId Flag: create new form id
     *
     * @return string
     */
    final public static function getFormId($createNewFormId = true)
    {
        $formIdStrategy = \XLite::getInstance()->getFormIdStrategy();

        return $formIdStrategy === 'per-session'
            ? \XLite\Core\Session::getInstance()->createFormId(false)
            : \XLite\Core\Session::getInstance()->createFormId($createNewFormId);
    }

    /**
     * Get formId strategy
     *
     * @return string
     */
    public function getFormIdStrategy()
    {
        return ConfigParser::getOptions(['other', 'csrf_strategy']);
    }

    // }}}

    // {{{ Application versions

    final protected function getParsedVersion($partName = null)
    {
        if (!isset($this->parsedVersion)) {
            $version = explode('.', $this->getVersion());
            $this->parsedVersion = [
                'major' => $version[0] . '.' . $version[1],
                'minor' => !empty($version[2]) ? $version[2] : '0',
                'build' => !empty($version[3]) ? $version[3] : '0',
            ];
            $this->parsedVersion['minorFull'] = $this->parsedVersion['minor']
                . ($this->parsedVersion['build'] ? '.' . $this->parsedVersion['build'] : '');
            $this->parsedVersion['hotfix'] = $this->parsedVersion['major'] . '.' . $this->parsedVersion['minor'];
        }

        return !is_null($partName) ? $this->parsedVersion[$partName] : $this->parsedVersion;
    }

    /**
     * Get application version
     *
     * @return string
     */
    final public function getVersion()
    {
        return static::XC_VERSION;
    }

    /**
     * Get application major version (X.X.x.x)
     *
     * @return string
     */
    final public function getMajorVersion()
    {
        return $this->getParsedVersion('major');
    }

    /**
     * Get application minor version (x.x.X.X)
     *
     * @return string
     */
    final public function getMinorVersion()
    {
        return $this->getParsedVersion('minorFull');
    }

    /**
     * Get application version build number (x.x.x.X)
     *
     * @return string
     */
    final public function getBuildVersion()
    {
        return $this->getParsedVersion('build');
    }

    /**
     * Get application minor version (x.x.X.x)
     *
     * @return string
     */
    final public function getMinorOnlyVersion()
    {
        return $this->getParsedVersion('minor');
    }

    /**
     * Get application hot-fixes branch version (X.X.X.x)
     *
     * @return string
     */
    final public function getHotfixBranchVersion()
    {
        return $this->getParsedVersion('hotfix');
    }

    /**
     * Compare a version with the major core version
     *
     * @param string $version  Version to compare
     * @param string $operator Comparison operator
     *
     * @return boolean
     */
    final public function checkVersion($version, $operator)
    {
        return version_compare($this->getMajorVersion(), $version, $operator);
    }

    /**
     * Compare a version with the minor core version
     *
     * @param string $version  Version to compare
     * @param string $operator Comparison operator
     *
     * @return boolean
     */
    final public function checkMinorVersion($version, $operator)
    {
        return version_compare($this->getMinorVersion(), $version, $operator);
    }

    /**
     * Get last cache rebuild time
     *
     * @return integer Timestamp
     */
    public static function getLastRebuildTimestamp()
    {
        return \XLite\View\AResourcesContainer::getLatestCacheTimestamp();
    }

    /**
     * @since 5.3.3.2 First appearance
     *
     * Actions to be executed after ignore_user_abort call
     */
    public function runPostRequestActions()
    {
        ignore_user_abort(true);

        while (\XLite\Core\Job\InMemoryJobRegistry::getInstance()->hasJobs()) {
            $job = \XLite\Core\Job\InMemoryJobRegistry::getInstance()->consume();
            $job->handle();
        }
    }

    /**
     * Sends response
     *
     * @return void
     */
    public function sendResponse(): void
    {
        $xLite = \XLite::getInstance();
        $response = new Response($xLite->getContent(), $xLite->getStatusCode(), $xLite->getHeaders());

        foreach ($this->getCookiesForHeaders() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        $response->send();
    }

    public function streamResponse(callable $callback): void
    {
        $xLite = \XLite::getInstance();
        $response = new StreamedResponse($callback, $xLite->getStatusCode(), $xLite->getHeaders());
        $response->send();
    }

    public function getCookiesForHeaders()
    {
        $result = [];

        foreach ($this->cookies as $c) {
            $result[] = Cookie::create($c['name'])
                ->withValue($c['value'])
                ->withPath($c['options']['path'])
                ->withExpires($c['options']['expires'])
                ->withDomain($c['options']['domain'])
                ->withHttpOnly($c['options']['httponly'])
                ->withSecure($c['options']['secure']);
        }

        return $result;
    }

    // }}}

    final public static function isTrial()
    {
        return (bool) ConfigParser::getOptions(['service', 'is_trial']);
    }

    final public static function areUpdateNotificationsEnabled()
    {
        return (bool) ConfigParser::getOptions(['service', 'display_update_notification']);
    }
}
