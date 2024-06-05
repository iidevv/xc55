<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use XCart\Container;
use XLite\Model\Profile;
use XLite\Model\Role\Permission;

/**
 * Current session
 */
class Session extends \XLite\Base\Singleton
{
    use FormIdsTrait;

    /**
     * Maximum admin session TTL (12 hours)
     */
    public const MAX_ADMIN_TTL = 43200;

    /**
     * Public session id argument name
     */
    public const ARGUMENT_NAME = 'xid';

    /**
     * Referer cookie name
     */
    public const LC_REFERER_COOKIE_NAME = 'LCRefererCookie';

    /**
     * Name of the cell to store the cURL error code value
     */
    public const CURL_CODE_ERROR = 'curl_code_error_in_session';

    /**
     * Name of the cell to store the cURL error code value
     */
    public const CURL_CODE_ERROR_MESSAGE = 'curl_error_message_in_session';

    /**
     * Session
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Language (cache)
     *
     * @var \XLite\Model\Language
     */
    protected $language;

    /**
     * Get session TTL (seconds)
     *
     * @return integer
     */
    public static function getTTL(): int
    {
        return 0;
    }

    /**
     * Getter
     *
     * @param string $name Session cell name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->session->get($name);
    }

    /**
     * Setter
     *
     * @param string $name  Session cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function __set(string $name, $value)
    {
        $this->session->set($name, $value);
    }

    /**
     * Check session cell availability
     *
     * @param string $name Session cell name
     *
     * @return boolean
     */
    public function __isset(string $name)
    {
        return $this->session->has($name);
    }

    /**
     * Remove session cell
     *
     * @param string $name Session cell name
     *
     * @return void
     */
    public function __unset(string $name)
    {
        $this->session->remove($name);
    }

    /**
     * Getter
     * DEPRECATE
     *
     * @param string $name Session cell name
     *
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->session->get($name);
    }

    /**
     * Setter
     * DEPRECATE
     *
     * @param string $name  Session cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function set(string $name, $value)
    {
        $this->session->set($name, $value);
    }

    /**
     * Unset in batch mode
     *
     * @return void
     */
    public function unsetBatch()
    {
        foreach (func_get_args() as $name) {
            $this->session->remove($name);
        }
    }

    public function setProfile(Profile $profile)
    {
        $this->__set('profile_id', $profile->getProfileId());

        if ($profile->isAdmin() && $profile->isPermissionAllowed(Permission::ROOT_ACCESS)) {
            $this->set('isAdmin', true);
            $this->setCookie();
        }
    }

    /**
     * Stores the cURL error code into session
     *
     * @param integer $code cURL error
     *
     * @return void
     */
    public function storeCURLError($code)
    {
        $this->{static::CURL_CODE_ERROR} = $code;
    }

    /**
     * Returns the cURL error code from session
     *
     * @return integer
     */
    public function getCURLError()
    {
        $result = $this->{static::CURL_CODE_ERROR};
        $this->storeCURLError(null);

        return $result;
    }

    /**
     * Stores the cURL error message into session
     *
     * @param string $msg cURL error message
     *
     * @return void
     */
    public function storeCURLErrorMessage($msg)
    {
        $this->{static::CURL_CODE_ERROR_MESSAGE} = $msg;
    }

    /**
     * Returns the cURL error message from session
     *
     * @return string
     */
    public function getCURLErrorMessage()
    {
        $result = $this->{static::CURL_CODE_ERROR_MESSAGE};
        $this->storeCURLErrorMessage(null);

        return $result;
    }

    /**
     * Get public session id argument name
     *
     * @return string
     */
    public function getName()
    {
        return self::ARGUMENT_NAME;
    }

    /**
     * Get public session id
     *
     * @return string
     */
    public function getID()
    {
        return $this->session->getId();
    }

    /**
     * Load session by public session id
     *
     * @param string $sid Public session id
     *
     * @return void
     */
    public function loadBySid(string $sid): void
    {
        if ($this->session->isStarted()) {
            $this->session->save();
        }

        $this->session->setId($sid);
    }

    /**
     * Session ID for forms
     *
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->session->getId();
    }

    /**
     * Invalidate session
     *
     * @return bool
     */
    public function invalidate(): bool
    {
        return $this->session->invalidate();
    }

    /**
     * @return bool
     */
    public function isAdminSessionExpired(): bool
    {
        return time() - $this->session->getMetadataBag()->getLastUsed() > static::MAX_ADMIN_TTL;
    }

    /**
     * Get language
     *
     * @return \XLite\Model\Language
     */
    public function getLanguage()
    {
        if (!isset($this->language)) {
            $this->language = \XLite\Core\Database::getRepo('XLite\Model\Language')
                ->findOneByCode($this->getCurrentLanguage());
        }

        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language Language code
     * @param string $zone     Admin/customer zone OPTIONAL
     *
     * @return void
     */
    public function setLanguage($language, $zone = null)
    {
        $code = $this->__get('language');

        if (!isset($zone)) {
            $zone = \XLite::isAdminZone() ? 'admin' : 'customer';
        }

        if (!is_array($code)) {
            $code = [];
        }

        if (!isset($code[$zone]) || $code[$zone] !== $language) {
            $code[$zone] = $language;

            $this->__set('language', $code);
            $this->language = null;
        }
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        /** @var SessionInterface $session */
        $this->session = !$this->useDumpSession()
            ? Container::getServiceLocator()->getSession()
            : new SessionDump();

        $this->runCronTasks();

        $this->setCookie();
    }

    /**
     * Set cookie
     *
     * @return void
     */
    protected function setCookie()
    {
        if (
            PHP_SAPI !== 'cli'
            && !headers_sent()
            && (
                \XLite\Core\Request::getInstance()->isHTTPS()
                || !\XLite\Core\Config::getInstance()->Security->customer_security
            )
        ) {
            if (!$this->useDumpSession()) {
                $this->setLCRefererCookie();
            }
        }
    }

    /**
     * Set referer cookie (this is stored when user register new profile)
     *
     * @return void
     */
    protected function setLCRefererCookie()
    {
        if (!isset($_COOKIE[static::LC_REFERER_COOKIE_NAME]) && isset($_SERVER['HTTP_REFERER'])) {
            $referer = parse_url($_SERVER['HTTP_REFERER']);

            if (isset($referer['host']) && $referer['host'] != $_SERVER['HTTP_HOST']) {
                \XLite\Core\Request::getInstance()->setCookie(
                    static::LC_REFERER_COOKIE_NAME,
                    $_SERVER['HTTP_REFERER'],
                    $this->getLCRefererCookieTTL()
                );
            }
        }
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

        return strstr($url['host'], '.') === false ? false : $url['host'];
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
     * Get referer cookie TTL (seconds)
     *
     * @return integer
     */
    protected function getLCRefererCookieTTL()
    {
        return 3600 * 24 * 180; // TTL is 180 days
    }

    /**
     * Get current language
     *
     * @param string $zone Store zone OPTIONAL
     * @return string Language code
     */
    public function getCurrentLanguage($zone = null)
    {
        $code = $this->__get('language');
        if (!$zone) {
            $zone = \XLite::isAdminZone() ? 'admin' : 'customer';
        }

        if (!is_array($code)) {
            $code = [];
        }

        $useCleanUrls = defined('LC_USE_CLEAN_URLS') && LC_USE_CLEAN_URLS == true;

        $languageCodeFromRequest = \XLite\Core\Request::getInstance()->getLanguageCode();

        if ($useCleanUrls && \XLite\Core\Router::getInstance()->isUseLanguageUrls() && $languageCodeFromRequest) {
            $code = array_merge($code, ['customer' => $languageCodeFromRequest]);
        }

        if (!empty($code[$zone])) {
            $language = \XLite\Core\Database::getRepo('XLite\Model\Language')->findOneByCode($code[$zone]);

            if (!isset($language) || !$language->getAdded() || !$language->getEnabled()) {
                unset($code[$zone]);
            } elseif ($useCleanUrls && \XLite\Core\Router::getInstance()->isUseLanguageUrls() && $languageCodeFromRequest) {
                $lang = $this->__get('language') ?: [];
                $lang['customer'] = $languageCodeFromRequest;
                $this->__set('language', $lang);
            }
        }

        if (empty($code[$zone])) {
            $this->setLanguage($this->defineCurrentLanguage());
            $code = $this->__get('language');
        }

        return $code[$zone];
    }

    /**
     * Define current language
     *
     * @return string Language code
     */
    protected function defineCurrentLanguage()
    {
        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();
        if (!\XLite::isAdminZone() && !empty($languages)) {
            $language = isset(\XLite\Core\Config::getInstance()->General)
                ? \XLite\Core\Config::getInstance()->General->default_language
                : 'en';

            $result = \Includes\Utils\ArrayManager::searchInObjectsArray(
                $languages,
                'getCode',
                $language
            );
        }

        return isset($result) ? $result->getCode() : static::getDefaultLanguage();
    }

    /**
     * Use dump session or not
     *
     * @return boolean
     */
    protected function useDumpSession(): bool
    {
        return PHP_SAPI === 'cli' || \XLite\Core\Request::getInstance()->isBot();
    }

    // {{{ Cron tasks

    /**
     * Run cron tasks
     *
     * @return void
     */
    protected function runCronTasks()
    {
        if ($this->isCronActive()) {
            foreach ($this->getCronTasks() as $method) {
                $this->$method();
            }
        }
    }

    /**
     * Return true if cron tasks should be run
     *
     * @return boolean
     */
    protected function isCronActive(): bool
    {
        // Run cron tasks with ~ 1% probability
        return !\XLite\Core\Request::getInstance()->isCLI()
            && \XLite\Core\Config::getInstance()->General
            && \XLite\Core\Config::getInstance()->General->internal_cron_enabled
            && $this->session !== null
            && mt_rand(0, 10000) % 100 === 0;
    }

    /**
     * Get list of cron tasks
     *
     * @return array
     */
    protected function getCronTasks(): array
    {
        return [
            'runGarbageCollectOrders',
        ];
    }

    /**
     * Run cron task Garbage collect orders
     *
     * @return void
     */
    protected function runGarbageCollectOrders(): void
    {
        \XLite\Core\Database::getRepo('XLite\Model\Order')->collectGarbage();
    }

    // }}}

    /**
     * Return array of \XLite\Model\AccessControlCell belongs to this session
     *
     * @return \XLite\Model\AccessControlCell[]
     */
    public function getAccessControlCells(): array
    {
        $cells = [];
        $hashes = $this->access_control_cells;

        if (!empty($hashes)) {
            $cells = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlCell')->findByHashes($hashes);

            foreach ($cells as $key => $cell) {
                if (!is_object($cell)) {
                    unset($cells[$key]);
                }
            }
        }

        return $cells;
    }

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function addAccessControlCellHash(string $hash): Session
    {
        $hashes = $this->access_control_cells;
        $hashes[] = $hash;
        $this->access_control_cells = array_unique($hashes);

        return $this;
    }
}
