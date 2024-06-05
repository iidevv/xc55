<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller;

use Includes\Utils\URLManager;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Exception\FatalException;
use XLite\InjectLoggerTrait;
use XLite\View\AView;
use XLite\View\CommonResources;
use XLite\Core\Request;

/**
 * Abstract controller
 */
abstract class AController extends \XLite\Core\Handler
{
    use ExecuteCachedTrait;
    use InjectLoggerTrait;

    /**
     * Controller main params
     */
    public const PARAM_TARGET = 'target';
    public const PARAM_ACTION = 'action';

    public const PARAM_REDIRECT_CODE = 'redirectCode';

    /**
     * Request param to pass URLs to return
     */
    public const RETURN_URL = 'returnURL';

    /**
     * Root category identificator (it is available in all controllers and views)
     *
     * @var integer|null
     */
    protected static $rootCategoryId = null;

    /**
     * "Is logged" flag
     *
     * @var mixed
     */
    protected static $isLogged = null;

    /**
     * Object to keep action status
     *
     * @var \XLite\Model\ActionStatus
     */
    protected $actionStatus;

    /**
     * returnURL
     *
     * @var string
     */
    protected $returnURL;

    /**
     * params
     *
     * @var string[]
     */
    protected $params = ['target'];

    /**
     * Validity flag
     * TODO - check where it's really needed
     *
     * @var bool
     */
    protected $valid = true;

    /**
     * Hard (main page redirect) redirect in AJAX request
     *
     * @var bool
     */
    protected $hardRedirect = false;

    /**
     * Internal (into popup ) redirect in AJAX request
     *
     * @var bool
     */
    protected $internalRedirect = false;

    /**
     * Redirect if the hosts do not match
     *
     * @var bool
     */
    protected $hostRedirect = true;

    /**
     * Popup silence close in AJAX request
     *
     * @var bool
     */
    protected $silenceClose = false;

    /**
     * Pure action flag in AJAX request
     * Set to true if the client does not require any action
     *
     * @var bool
     */
    protected $pureAction = false;

    /**
     * Suppress output flag
     *
     * @var bool
     */
    protected $suppressOutput = false;

    /**
     * Current viewer
     *
     * @var \XLite\View\AView
     */
    protected $viewer = null;

    /**
     * Get target by controller class name
     *
     * @return string
     */
    protected static function getTargetByClassName()
    {
        $parts = explode('\\', get_called_class());

        return \Includes\Utils\Converter::convertFromCamelCase(lcfirst(array_pop($parts)));
    }

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        return $classes;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getMainTitle()
    {
        return $this->isForceChangePassword()
            ? static::t('Change password')
            : $this->getTitle();
    }

    /**
     * Checks if the force change password needed
     *
     * @return bool
     */
    public function isForceChangePassword()
    {
        return $this->isLogged() && \XLite\Core\Session::getInstance()->forceChangePassword;
    }

    /**
     * Check if current user is logged in
     *
     * @return bool
     */
    public function isLogged()
    {
        if (is_null(static::$isLogged)) {
            static::$isLogged = \XLite\Core\Auth::getInstance()->isLogged();
        }

        return static::$isLogged;
    }

    /**
     * Check if the used is logged admin
     *
     * @return bool
     */
    public function isLoggedAdmin()
    {
        return $this->isLogged() && \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * @param mixed $data Data
     */
    protected function displayJSON($data)
    {
        $data = json_encode($data);

        \XLite::getInstance()->addHeader('Content-Type', 'application/json');
        \XLite::getInstance()->addHeader('Content-Length', strlen($data));

        print $data;
    }

    /**
     * Send specific headers and print AJAX data as JSON string
     *
     * @param array $data
     */
    protected function printAJAX($data)
    {
        // Move top messages into headers since we print data and die()
        $this->translateTopMessagesToHTTPHeaders();

        $content = json_encode($data);

        $xLite = \XLite::getInstance();
        $xLite->addHeader('Content-Type', 'application/json; charset=UTF-8');
        $xLite->addHeader('Content-Length', strlen($content));
        $xLite->addHeader('ETag', md5($content));

        $xLite->addContent($content);
        $xLite->sendResponse();
    }

    // {{{ Pages

    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        return [
            'tabletDevice' => $this->isTableDevice(),
        ];
    }

    /**
     * Defines if the device is a tablet
     *
     * @return bool
     */
    protected function isTableDevice()
    {
        return Request::isTablet();
    }

    /**
     * Get current page
     * FIXME: to revise
     *
     * @return string
     */
    public function getPage()
    {
        $page  = $this->page;
        $pages = $this->getPages();

        return $page && isset($pages[$page]) ? $page : key($pages);
    }

    /**
     * getPages
     *
     * @return array
     */
    public function getPages()
    {
        return [];
    }

    /**
     * Return list of page templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        return [];
    }

    // }}}

    // {{{ Other

    /**
     * Get number of days left before trial period will expire
     *
     * @param bool $returnDays Flag: return in days
     *
     * @return integer
     */
    public function getTrialPeriodLeft($returnDays = true)
    {
        return \XLite::getTrialPeriodLeft($returnDays);
    }

    /**
     * Return true if current user - root admin
     *
     * @return bool
     */
    public function isRootAdmin()
    {
        return \XLite::isAdminZone()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Get controller parameters
     * TODO - check this method
     * FIXME - backward compatibility
     *
     * @param string $exceptions Parameter keys string OPTIONAL
     *
     * @return array
     */
    public function getAllParams($exceptions = null)
    {
        $result = [];
        $exceptions = isset($exceptions) ? explode(',', $exceptions) : false;
        foreach ($this->get('params') as $name) {
            $value = $this->get($name);
            if (isset($value) && (!$exceptions || in_array($name, $exceptions))) {
                $result[$name] = $value;
            }
        }

        return $result;
    }

    /**
     * Is redirect needed
     *
     * @return bool
     */
    public function isRedirectNeeded()
    {
        $isRedirectNeeded = (Request::getInstance()->isPost() || $this->getReturnURL()) && !$this->silent;

        if (!$isRedirectNeeded) {
            $host = \Includes\Utils\ConfigParser::getOptions(
                [
                    'host_details',
                    $this->isHTTPS() ? 'https_host' : 'http_host'
                ]
            );

            if ($this->hostRedirect && isset($_SERVER['HTTP_HOST']) && strtolower($host) !== $_SERVER['HTTP_HOST']) {
                $isRedirectNeeded = true;
                $this->setReturnURL($this->getShopURL($this->getURL(), $this->isHTTPS()));
            }
        }

        return $isRedirectNeeded;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return Request::getInstance()->target;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return Request::getInstance()->action;
    }

    /**
     * Get session cell name for pager widget
     *
     * @return string
     */
    public function getPagerSessionCell()
    {
        return \XLite\Core\Converter::getPlainClassName($this);
    }

    /**
     * Get the secure full URL of the page
     * Example: getSecureShopURL('') = "https://domain/dir/
     *
     * @param string $url    Relative URL OPTIONAL
     * @param array  $params Optional URL params OPTIONAL
     *
     * @return string
     */
    public function getSecureShopURL($url = '', array $params = [])
    {
        return \XLite\Core\URLManager::getShopURL($url, true, $params, null, false);
    }

    /**
     * Get the full URL of the page
     * Example: getShopURL('') = "http://domain/dir/
     *
     * @param string  $url    Relative URL OPTIONAL
     * @param bool $secure Flag to use HTTPS OPTIONAL
     * @param array   $params Optional URL params OPTIONAL
     *
     * @return string
     */
    public function getShopURL($url = '', $secure = null, array $params = [])
    {
        return \XLite::getInstance()->getShopURL($url, $secure, $params);
    }

    /**
     * @param string $path
     * @param null   $secure
     * @param array  $params
     *
     * @return string
     */
    public function getServiceUrl($path = '', $secure = null, array $params = [])
    {
        return \XLite::getInstance()->getServiceURL($path, $secure, $params);
    }

    /**
     * Get the URL for storefront with assured accessibility
     *
     * @param bool $shopStatus Shop status OPTIONAL
     *
     * @return string
     */
    public function getAccessibleShopURL($shopStatus = null)
    {
        if (!is_bool($shopStatus)) {
            $shopStatus = !\XLite\Core\Auth::getInstance()->isClosedStorefront();
        }

        $params = $shopStatus
            ? []
            : ['shopKey' => \XLite\Core\Auth::getInstance()->getShopKey()];

        return $this->getShopURL('', null, $params);
    }

    /**
     * Get return URL
     *
     * @return string
     */
    public function getReturnURL()
    {
        if (!isset($this->returnURL)) {
            $this->returnURL = Request::getInstance()->{static::RETURN_URL};
        }

        if ($this->returnURL && !\XLite\Core\URLManager::isValidDomain($this->returnURL, false)) {
            $this->getLogger()->debug('Untrusted returnURL parameter passed: ' . $this->returnURL);
            $this->returnURL = $this->getShopURL();
        }

        $hostDetails = \Includes\Utils\ConfigParser::getOptions(['host_details']);

        if (
            !empty($this->returnURL)
            && !empty($hostDetails['admin_host'])
            && strpos($this->returnURL, 'http') !== 0
            && $_SERVER['HTTP_HOST'] !== URLManager::getHostByLocalUrl($this->returnURL)
        ) {
            $this->returnURL = URLManager::getShopURL($this->returnURL);
        }

        return $this->returnURL;
    }

    /**
     * Set return URL
     *
     * @param string $url URL to set
     */
    public function setReturnURL($url)
    {
        $this->returnURL = $url ? str_replace('&amp;', '&', $url) : '?';
    }

    /**
     * Get current URL with additional params
     *
     * @param array $params Query params to use
     */
    public function setReturnURLParams(array $params)
    {
        $this->setReturnURL($this->buildURL($this->getTarget(), '', $params));
    }

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     */
    public function handleRequest()
    {
        if (!$this->checkAccess()) {
            $this->markAsAccessDenied();
        } elseif (!$this->isVisible()) {
            $this->display404();
        } elseif ($this->needSecure()) {
            $this->redirectToSecure();
        } elseif (!$this->checkLanguage()) {
            $this->redirectToCurrentLanguage();
        } else {
            $this->run();
        }

        if ($this->isRedirectNeeded()) {
            $this->doRedirect();
        } elseif ($this->isAJAX()) {
            $this->translateTopMessagesToHTTPHeaders();

            \XLite\Core\Event::getInstance()->display();
            \XLite\Core\Event::getInstance()->clear();
        }
    }

    /**
     * Alias: check for an AJAX request
     *
     * @return bool
     */
    public function isAJAX()
    {
        return Request::getInstance()->isAJAX();
    }

    /**
     * Return Viewer object
     *
     * @return \XLite\View\Controller
     */
    public function getViewer()
    {
        if (!isset($this->viewer)) {
            $class = $this->getViewerClass();
            $this->viewer = new $class($this->getViewerParams(), $this->getViewerTemplate());
        }

        return $this->viewer;
    }

    /**
     * Send headers
     *
     * @param  array $additional Additional headers OPTIONAL
     */
    public static function sendHeaders($additional = [])
    {
        $xLite = \XLite::getInstance();

        // no-cache headers
        $xLite->addHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $xLite->addHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $xLite->addHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        $xLite->addHeader('Cache-Control', 'post-check=0, pre-check=0', false);
        $xLite->addHeader('Pragma', 'no-cache');
        $xLite->addHeader('Content-Type', 'text/html; charset=utf-8');

        if (\XLite::isAdminZone()) {
            $xLite->addHeader('X-Robots-Tag', 'noindex, nofollow');
        }

        foreach (\XLite\Core\URLManager::getAllowedDomains() as $domain) {
            $xLite->addHeader('Access-Control-Allow-Origin', $domain, false);
        }
        $XFrameOptions = \Includes\Utils\ConfigParser::getOptions(['other', 'x_frame_options']);
        if ($XFrameOptions !== null && $XFrameOptions !== 'disabled') {
            $xLite->addHeader('X-Frame-Options', $XFrameOptions);
        }

        $XXSSProtection = \Includes\Utils\ConfigParser::getOptions(['other', 'x_xss_protection']);
        if ($XXSSProtection !== null && $XXSSProtection !== 'disabled') {
            $xLite->addHeader('X-XSS-Protection', $XXSSProtection);
        }

        $XContentTypeOptions = \Includes\Utils\ConfigParser::getOptions(['other', 'x_content_type_options']);
        if ($XContentTypeOptions !== null && $XContentTypeOptions !== 'disabled') {
            $xLite->addHeader('X-Content-Type-Options', $XContentTypeOptions);
        }

        foreach ($additional as $header => $value) {
            $xLite->addHeader($header, $value);
        }
    }

    /**
     * Returns additional HTTP headers to be sent with response
     *
     * @return array
     */
    protected function getAdditionalHeaders()
    {
        return [];
    }

    /**
     * Process request
     */
    public function processRequest()
    {
        if (!$this->suppressOutput) {
            $viewer = $this->getViewer();
            if (!Request::getInstance()->isCLI()) {
                $additional = $this->getAdditionalHeaders();
                static::sendHeaders($additional);
            }
            $viewer->init();

            if (!$this->isAJAX()) {
                ob_start();
                $viewer->display();
                \XLite::getInstance()->addContent(ob_get_contents());
                ob_end_clean();
            } elseif (
                $this->checkAccess()
                && (
                    !$viewer->getAllowedTargets()
                    || in_array($this->getTarget(), $viewer->getAllowedTargets())
                )
            ) {
                $this->printAJAXOutput($viewer);
            }
        }
    }

    /**
     * This function called after template output
     * FIXME - may be there is a better way to handle this?
     */
    public function postprocess()
    {
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string|null
     */
    public function getTitle()
    {
        return Request::getInstance()->target == \XLite::TARGET_404
            ? static::t('Page not found')
            : null;
    }

    /**
     * @return bool
     */
    public function isCapitalizedTitle()
    {
        return true;
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return bool
     */
    public function isTitleVisible()
    {
        return true;
    }

    /**
     * Return the page title (for the <title> tag)
     *
     * @return string
     */
    public function getPageTitle()
    {
        $config = \XLite\Core\Config::getInstance();
        $title = [];

        if ($config->CleanURL->company_name) {
            $title[] = $this->getTitleCompanyNamePart();
        }

        if ($config->CleanURL->parent_category_path) {
            $title[] = $this->getTitleParentPart();
        }

        $title[] = $this->getTitleObjectPart();

        if ($config->CleanURL->object_name_in_page_title_order == true) {
            $title = array_reverse($title);
        }

        return implode(static::t('title-delimiter'), array_filter($title));
    }

    /**
     * Return the page title company name part (for the <title> tag)
     *
     * @return string
     */
    public function getTitleCompanyNamePart()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Return the page title parent category part (for the <title> tag)
     *
     * @return string
     */
    public function getTitleParentPart()
    {
        return '';
    }

    /**
     * Return the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        return $this->getMainTitle();
    }

    /**
     * Check if an error occurred
     *
     * @return bool
     */
    public function isActionError()
    {
        return isset($this->actionStatus) && $this->actionStatus->isError();
    }

    /**
     * setActionStatus
     *
     * @param integer $status  Error/success
     * @param string  $message Status info OPTIONAL
     * @param integer $code    Status code OPTIONAL
     */
    public function setActionStatus($status, $message = '', $code = 0)
    {
        $this->actionStatus = new \XLite\Model\ActionStatus($status, $message, $code);
    }

    /**
     * setActionError
     *
     * @param string  $message Status info  OPTIONAL
     * @param integer $code    Status code OPTIONAL
     */
    public function setActionError($message = '', $code = 0)
    {
        $this->setActionStatus(\XLite\Model\ActionStatus::STATUS_ERROR, $message, $code);
    }

    /**
     * setActionSuccess
     *
     * @param string  $message Status info OPTIONAL
     * @param integer $code    Status code OPTIONAL
     */
    public function setActionSuccess($message = '', $code = 0)
    {
        $this->setActionStatus(\XLite\Model\ActionStatus::STATUS_SUCCESS, $message, $code);
    }

    /**
     * Check if handler is valid
     * TODO - check where it's really needed
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Check - is secure connection or not
     *
     * @return bool
     */
    public function isHTTPS()
    {
        return Request::getInstance()->isHTTPS();
    }

    /**
     * Get access level
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return \XLite\Core\Auth::getInstance()->getCustomerAccessLevel();
    }

    /**
     * getProperties
     *
     * @return array
     */
    public function getProperties()
    {
        $result = [];

        foreach ($_REQUEST as $name => $value) {
            $result[$name] = $this->get($name);
        }

        return $result;
    }

    /**
     * getURL
     *
     * @param array $params URL parameters OPTIONAL
     *
     * @return string
     */
    public function getURL(array $params = [])
    {
        $params = array_merge($this->getAllParams(), $params);
        $target = $params['target'] ?? '';
        unset($params['target']);

        return $this->buildURL($target, '', $params);
    }

    /**
     * Get referer URL
     *
     * @return string
     */
    public function getReferrerURL()
    {
        if (Request::getInstance()->referer) {
            $url = Request::getInstance()->referer;
        } elseif (!empty($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
        } else {
            $url = $this->getURL();
        }

        return $url;
    }

    /**
     * getPageTemplate
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getPageTemplates(), $this->getPage());
    }

    /**
     * Return the array(pages) for tabber
     * FIXME - move to the Controller/Admin/Abstract.php:
     * tabber is not used in customer area
     *
     * @return array
     */
    public function getTabPages()
    {
        return $this->getPages();
    }

    /**
     * getUploadedFile
     *
     * @return string
     */
    public function getUploadedFile()
    {
        $file = null;

        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            $file = $_FILES['userfile']['tmp_name'];
        } elseif (is_readable($_POST['localfile'])) {
            $file = $_POST['localfile'];
        } else {
            throw new FatalException('FAILED: data file unspecified');
        }

        // security check
        $name = $_FILES['userfile']['name'];

        if (strstr($name, '../') || strstr($name, '..\\')) {
            throw new FatalException('ACCESS DENIED');
        }

        return $file;
    }

    /**
     * checkUploadedFile
     *
     * @return bool
     */
    public function checkUploadedFile()
    {
        $check = is_uploaded_file($_FILES['userfile']['tmp_name']) || is_readable($_POST['localfile']);

        if ($check) {
            // security check
            $name = $_FILES['userfile']['name'];

            if (strstr($name, '../') || strstr($name, '..\\')) {
                $check = false;
            }
        }

        return $check;
    }

    /**
     * Get controller charset
     *
     * @return string
     */
    public function getCharset()
    {
        return 'utf-8';
    }

    /**
     * isSecure
     *
     * @return bool
     */
    public function isSecure()
    {
        return false;
    }

    /**
     * Return the reserved ID of root category
     *
     * @return integer
     */
    public function getRootCategoryId()
    {
        if (!isset(static::$rootCategoryId)) {
            static::$rootCategoryId = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();
        }

        return static::$rootCategoryId;
    }

    /**
     * Return current category Id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return Request::getInstance()->category_id;
    }

    /**
     * Get meta title
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return null;
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return null;
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->getDefaultKeywords();
    }

    /**
     * Get default meta keywords
     *
     * @return string
     */
    public function getDefaultKeywords()
    {
        return static::t('default-meta-keywords') != 'default-meta-keywords' ? static::t('default-meta-keywords') : '';
    }

    /**
     * Return model form object
     *
     * @param array $params Form constructor params OPTIONAL
     *
     * @return \XLite\View\Model\AModel|void
     */
    public function getModelForm(array $params = [])
    {
        $result = null;
        $class  = $this->getModelFormClass();

        if (isset($class)) {
            $result = \XLite\Model\CachingFactory::getObject(
                __METHOD__ . $class . (empty($params) ? '' : md5(serialize($params))),
                $class,
                $params
            );
        }

        return $result;
    }

    /**
     * Check - current request is AJAX background request for page center or not
     *
     * @return bool
     */
    public function isAJAXCenterRequest()
    {
        return $this->isAJAX() && Request::getInstance()->only_center;
    }

    /**
     * Check if current page is accessible
     *
     * @return bool
     */
    protected function checkAccess()
    {
        return \XLite\Core\Auth::getInstance()->isAuthorized($this);
    }

    /**
     * Check all controller access controls
     *
     * @return bool
     */
    public function checkAccessControls()
    {
        $ace = $this->getAccessControlEntities();
        $acz = $this->getAccessControlZones();
        return (empty($ace) || $this->checkAccessByACE()) && (empty($acz) || $this->checkAccessByACZ()) && $this->checkAccessControlsNotEmpty();
    }

    /**
     * Check if at least one of access controls not empty
     *
     * @return bool
     */
    public function checkAccessControlsNotEmpty()
    {
        $ace = $this->getAccessControlEntities();
        $acz = $this->getAccessControlZones();
        return !empty($ace) || !empty($acz);
    }

    /**
     * Return Access control entities for controller
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlEntities()
    {
        return [];
    }

    /**
     * Check access by Access Control Entities
     *
     * @return bool
     */
    protected function checkAccessByACE()
    {
        foreach ($this->getAccessControlEntities() as $accessControlEntity) {
            if (is_object($accessControlEntity) && \XLite\Core\Auth::getInstance()->checkACEAccess($accessControlEntity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return Access control zones for controller
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlZones()
    {
        return [];
    }

    /**
     * Check access by Access Control Zones
     *
     * @return bool
     */
    protected function checkAccessByACZ()
    {
        $zones = $this->getAccessControlZones();
        foreach ($zones as $accessControlZone) {
            if (\XLite\Core\Auth::getInstance()->checkACZAccess($accessControlZone)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if form id is valid or not
     *
     * @return bool
     */
    protected function isFormIdValid()
    {
        \XLite\Core\Session::getInstance()->removeExpiredFormIds();

        if (!$this->isActionNeedFormId()) {
            return true;
        }

        $requestFormId = Request::getInstance()->{\XLite::FORM_ID};

        if (!isset($requestFormId) || !$requestFormId) {
            return false;
        }

        if (\XLite::getInstance()->getFormIdStrategy() === 'per-session') {
            $formId = \XLite\Core\Session::getInstance()->getSessionFormId();

            $result = $formId === $requestFormId;

            if (!$result && $formId) {
                $newFormId = \XLite\Core\Session::getInstance()->resetFormId();

                if ($this->isAJAX()) {
                    $value = json_encode([
                        'old'   => $requestFormId,
                        'name'  => \XLite::FORM_ID,
                        'value' => $newFormId,
                    ]);
                    \XLite::getInstance()->addHeader('event-update-csrf', $value);
                }
            }
        } else {
            $form = null;

            foreach (\XLite\Core\Session::getInstance()->getFormIds() as $formId => $expiry) {
                if ($formId === $requestFormId) {
                    $form = $formId;
                }
            }

            $result = isset($form);

            if ($form) {
                \XLite\Core\Session::getInstance()->removeFormId($formId);
                $newFormId = \XLite\Core\Session::getInstance()->createFormId(true);

                if ($this->isAJAX()) {
                    $value = json_encode([
                        'old'   => $requestFormId,
                        'name'  => \XLite::FORM_ID,
                        'value' => $newFormId,
                    ]);
                    \XLite::getInstance()->addHeader('event-update-csrf', $value);
                }
            }
        }

        return $result;
    }

    /**
     * Restore form id
     */
    protected function restoreFormId()
    {
        if ($this->isActionNeedFormId()) {
            \XLite\Core\Session::getInstance()->restoreFormId();
        }
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return [];
    }

    /**
     * Check if the form ID validation is needed
     *
     * @return bool
     */
    protected function isActionNeedFormId()
    {
        return $this->getAction() && !in_array($this->getAction(), static::defineFreeFormIdActions());
    }

    /**
     * Check form id
     *
     * @return bool
     */
    public function checkFormId()
    {
        return $this->executeCachedRuntime(function () {
            $result = !static::needFormId()
                || (
                    static::needFormId()
                    && (
                        ($this->getTarget() && $this->isIgnoredTarget())
                        || ($this->isFormIdValid())
                    )
                );

            if (!$result) {
                \XLite\Core\TopMessage::addWarning('The form could not be identified as a form generated by X-Cart');

                $this->getLogger()->warning(
                    'Form ID checking failure',
                    [
                        'target' => $this->getTarget(),
                        'action' => $this->getAction()
                    ]
                );
            }

            return $result;
        }, ['checkFormId', $this->getTarget(), Request::getInstance()->{\XLite::FORM_ID}]);
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return bool
     */
    public static function needFormId()
    {
        return false;
    }

    /**
     * Return true if promo block with specified ID is visible
     * (used in promo.twig)
     *
     * @param string $blockId Promo block unique ID
     *
     * @return bool
     */
    public function isPromoBlockVisible($blockId)
    {
        $cookie = Request::getInstance()->getCookieData();

        return empty($cookie[$blockId . 'PromoBlock']);
    }

    /**
     * Check - current target and action is ignored (form id validation is disabled) or not
     *
     * @return bool
     */
    protected function isIgnoredTarget()
    {
        $result = false;

        if ($this->isRuleExists($this->defineIgnoredTargets())) {
            $result = true;
        } else {
            $request = Request::getInstance();

            if (
                $this->isRuleExists($this->defineSpecialIgnoredTargets())
                && isset($request->login)
                && isset($request->password)
                && \XLite\Core\Auth::getInstance()->isLogged()
                && \XLite\Core\Auth::getInstance()->getProfile()->getLogin() == $request->login
            ) {
                $postLogin = $request->login;
                $postPassword = $request->password;

                if (!empty($postLogin) && !empty($postPassword)) {
                    $postPassword = \XLite\Core\Auth::getInstance()->encryptPassword($postPassword);
                    $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                        ->findByLoginPassword($postLogin, $postPassword, 0);

                    if (isset($profile)) {
                        $profile->detach();
                        if ($profile->isEnabled() && \XLite\Core\Auth::getInstance()->isAdmin($profile)) {
                            $result = true;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Define common ignored targets
     *
     * @return array
     */
    protected function defineIgnoredTargets()
    {
        return [
            'callback'       => '*',
            'payment_method' => 'callback',
        ];
    }

    /**
     * Define special ignored targets
     *
     * @return array
     */
    protected function defineSpecialIgnoredTargets()
    {
        return [
            'files'          => ['tar', 'tar_skins', 'untar_skins'],
        ];
    }

    /**
     * Check - rule is exists with current target and action or not
     *
     * @param array $rules Rules
     *
     * @return bool
     */
    protected function isRuleExists(array $rules)
    {
        $request = Request::getInstance();

        return isset($rules[$request->target])
            && (
                $rules[$request->target] == '*'
                || (
                    is_array($rules[$request->target])
                    && (isset($request->action) && in_array($request->action, $rules[$request->target]))
                )
            );
    }

    /**
     * Return default redirect code
     *
     * @return integer
     */
    protected function getDefaultRedirectCode()
    {
        return $this->isAJAX() ? 200 : 302;
    }

    /**
     * Default URL to redirect
     *
     * @return string
     */
    protected function getDefaultReturnURL()
    {
        return null;
    }

    /**
     * Perform redirect
     *
     * @param string $url Redirect URL OPTIONAL
     *
     * @param null   $code
     */
    protected function redirect($url = null, $code = null)
    {
        $location = $this->getReturnURL();

        if (!isset($location)) {
            $location = $url ?? $this->getURL();
        }

        // filter FORM ID from redirect url
        // FIXME - check if it's really needed

        //$action = $this->get('action');
        //
        //if (empty($action)) {
        //    $location = $this->filterXliteFormID($location);
        //}

        if ($this->isAJAX()) {
            \XLite\Core\Event::getInstance()->display();
            \XLite\Core\Event::getInstance()->clear();
        }

        if (
            LC_USE_CLEAN_URLS
            && \XLite\Core\Router::getInstance()->isUseLanguageUrls()
            && !\XLite::isAdminZone()
        ) {
            $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']);
            if ($webDir && strpos($location, $webDir) !== 0 && strpos($location, 'http') !== 0) {
                $location = $webDir . '/' . $location;
            }
        }

        \XLite\Core\Operator::redirect(
            $location,
            $code ?: $this->getParam(static::PARAM_REDIRECT_CODE)
        );
    }

    /**
     * Select template to use
     *
     * @return string
     */
    protected function getViewerTemplate()
    {
        return 'main.twig';
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_REDIRECT_CODE => new \XLite\Model\WidgetParam\TypeInt('Redirect code', $this->getDefaultRedirectCode()),
        ];
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|null
     */
    protected function getModelFormClass()
    {
        return null;
    }

    /**
     * Perform some actions before redirect
     *
     * @param string $action Performed action
     */
    protected function actionPostprocess($action)
    {
        $method = __FUNCTION__ . \Includes\Utils\Converter::convertToUpperCamelCase($action);

        if (method_exists($this, $method)) {
            // Call action method
            $this->$method();
        }
    }

    /**
     * Call controller action
     */
    protected function callAction()
    {
        $action = $this->getAction();
        $method = 'doAction' . \Includes\Utils\Converter::convertToUpperCamelCase($action);

        if (method_exists($this, $method)) {
            // Call method doAction<action-name-in-camel-case>
            $this->$method();
        } else {
            $this->getLogger()->debug(
                'Handler for the action "' . $action . '" is not defined for the "' . get_class($this) . '" class'
            );
        }

        $this->actionPostprocess($action);
    }

    /**
     * Run controller
     */
    protected function run()
    {
        if ($this->getAction() && $this->isValid()) {
            $this->callAction();
        } else {
            $this->doNoAction();
        }

        if (!$this->isValid()) {
            $this->restoreFormId();
        }
    }

    /**
     * Do redirect
     */
    protected function doRedirect()
    {
        if ($this->isAJAX()) {
            if (!$this->hardRedirect) {
                $this->translateTopMessagesToHTTPHeaders();
            }
            $this->assignAJAXResponseStatus();
        }

        $this->redirect();
    }

    /**
     * Translate top messages to HTTP headers (AJAX)
     */
    protected function translateTopMessagesToHTTPHeaders()
    {
        $messages = \XLite\Core\TopMessage::getInstance()->getAJAXMessages();
        $processed = array_map(
            static function ($message) {
                return [
                        'type'    => $message[\XLite\Core\TopMessage::FIELD_TYPE],
                        'message' => $message[\XLite\Core\TopMessage::FIELD_TEXT],
                ];
            },
            $messages
        );

        \XLite::getInstance()->addHeader('event-messages', json_encode($processed));

        \XLite\Core\TopMessage::getInstance()->clearAJAX();
    }

    /**
     * Assign AJAX response status to HTTP header(s)
     */
    protected function assignAJAXResponseStatus()
    {
        if (!$this->isValid()) {
            // AXAX-based - cancel redirect
            \XLite::getInstance()->addHeader('ajax-response-status', '0');
            \XLite::getInstance()->addHeader('not-valid', '1');
        } elseif ($this->hardRedirect) {
            // Main page redirect
            \XLite::getInstance()->addHeader('ajax-response-status', '278');
        } elseif ($this->internalRedirect) {
            // Popup internal redirect
            \XLite::getInstance()->addHeader('ajax-response-status', '279');
        } elseif ($this->silenceClose) {
            // Popup silence close
            \XLite::getInstance()->addHeader('ajax-response-status', '277');
        } elseif ($this->pureAction) {
            // Pure action
            \XLite::getInstance()->addHeader('ajax-response-status', '276');
        } else {
            \XLite::getInstance()->addHeader('ajax-response-status', '270');
        }
    }

    /**
     * Preprocessor for no-action run
     */
    protected function doNoAction()
    {
    }

    /**
     * Check controller visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Display 404 page
     */
    protected function display404()
    {
        Request::getInstance()->target = \XLite::TARGET_404;
        Request::getInstance()->action = '';
        $this->headerStatus(404);
    }

    /**
     * Set internal popup redirect
     *
     * @param bool $flag Internal redirect status OPTIONAL
     */
    protected function setInternalRedirect($flag = true)
    {
        if ($this->isAJAX()) {
            $this->internalRedirect = (bool) $flag;
        }
    }

    /**
     * Set hard (main page redirect) redirect
     *
     * @param bool $flag Internal redirect status OPTIONAL
     */
    protected function setHardRedirect($flag = true)
    {
        if ($this->isAJAX()) {
            $this->hardRedirect = (bool) $flag;
        }
    }

    /**
     * Set silence close popup
     *
     * @param bool $flag Silence close status OPTIONAL
     */
    protected function setSilenceClose($flag = true)
    {
        if ($this->isAJAX()) {
            $this->silenceClose = (bool) $flag;
        }
    }

    /**
     * Set pure action flag
     *
     * @param bool $flag Flag OPTIONAL
     */
    protected function setPureAction($flag = false)
    {
        if ($this->isAJAX()) {
            $this->pureAction = (bool) $flag;
        }
    }

    /**
     * Set suppress output flag
     *
     * @param bool $suppressOutput Flag
     */
    protected function setSuppressOutput($suppressOutput)
    {
        $this->suppressOutput = (bool)$suppressOutput;
    }

    /**
     * Check if current viewer is for an AJAX request
     *
     * @return bool
     */
    protected function isAJAXViewer()
    {
        return $this->isAJAX() && Request::getInstance()->widget;
    }

    /**
     * Return class of current viewer
     *
     * @return string
     */
    protected function getViewerClass()
    {
        return $this->isAJAXViewer()
            ? Request::getInstance()->widget
            : 'XLite\View\Controller';
    }

    /**
     * Retrieve AJAX output content from viewer
     *
     * @param mixed $viewer Viewer to display in AJAX
     *
     * @return string
     */
    protected function getAJAXOutputContent($viewer)
    {
        // WARNING: getContent() call must be done before printAJAXResources()
        // because actual resources are registered during getContent() call
        $content = $viewer->getContent();

        return $this->printAJAXResources() . PHP_EOL
            . $this->printAJAXPreloadedLabels() . PHP_EOL
            . $content;
    }

    /**
     * Print AJAX request output
     *
     * @param mixed $viewer Viewer to display in AJAX
     */
    protected function printAJAXOutput($viewer)
    {
        $content = $this->getAJAXOutputContent($viewer);

        $class = 'ajax-container-loadable'
            . ' ctrl-' . implode('-', \XLite\Core\Operator::getInstance()->getClassNameAsKeys(get_called_class()))
            . ' widget-' . implode('-', \XLite\Core\Operator::getInstance()->getClassNameAsKeys($viewer));

        \XLite::getInstance()->addContent(
            '<div'
            . ' id="' . Request::getInstance()->getUniqueIdentifier() . '"'
            . ' class="' . $class . '"'
            . ' title="' . func_htmlspecialchars(static::t($this->getTitle())) . '"'
            . ' ' . $this->printAJAXAttributes() . ' >' . PHP_EOL
            . $content
            . '</div>'
        );
    }

    /**
     * Returns AJAX output attributes of container box.
     * @return string
     */
    protected function printAJAXAttributes()
    {
        return '';
    }

    /**
     * Print AJAX resources output
     *
     * @return string
     */
    protected function printAJAXResources()
    {
        \XLite\Core\Layout::getInstance()->registerResources([
            AView::RESOURCE_CSS => CommonResources::getInstance()->getCommonLessFiles(),
        ], 100, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON, 'getCommonFiles');

        if (
            Request::getInstance()->zone !== \XLite::ZONE_CUSTOMER
            && \XLite\Core\Layout::getInstance()->getZone() !== \XLite::ZONE_CUSTOMER
        ) {
            \XLite\Core\Layout::getInstance()->registerResources([
                AView::RESOURCE_CSS => CommonResources::getInstance()->getCSSFiles()
            ], 10, null, null, 'getCSSFiles');
        }

        $resources = \XLite\Core\Layout::getInstance()->getRegisteredPreparedResources();

        $resContainer = [
            'widget' => $this->getViewerClass(),
        ];

        $js = $this->prepareResourcesList($resources[\XLite\View\AView::RESOURCE_JS]);
        $css = $this->prepareResourcesList($resources[\XLite\View\AView::RESOURCE_CSS]);

        if ($css || $js) {
            $resContainer = array_merge(
                $resContainer,
                [
                    'css' => $css,
                    'js' => $js,
                ]
            );
        }

        $resJson = json_encode($resContainer);

        $code = <<<RES
<script type='application/json' data-resource>
    $resJson
</script>
RES;

        return $code;
    }

    /**
     * Print AJAX preloaded labels output
     *
     * @return string
     */
    protected function printAJAXPreloadedLabels()
    {
        $labels = \XLite\Core\PreloadedLabels\Registrar::getInstance()->getRegistered();

        $labelsContainer = [
            'widget' => $this->getViewerClass(),
            'labels' => $labels
        ];

        $labelsJson = json_encode(
            $labelsContainer,
            JSON_UNESCAPED_UNICODE
        );

        $code = "<script type='application/json' data-preloaded-labels>$labelsJson</script>";

        return $code;
    }

    /**
     * Print AJAX request output
     *
     * @param array $list Resources list
     *
     * @return array
     */
    protected function prepareResourcesList($list)
    {
        return array_map(
            static function ($item) {
                return $item['url'];
            },
            array_values($list)
        );
    }

    /**
     * Mark controller run thread as access denied
     */
    protected function markAsAccessDenied()
    {
        $this->params = ['target'];
        $this->set('target', 'access_denied');
        Request::getInstance()->target = 'access_denied';
        $this->headerStatus(403);
    }

    /**
     * Header status
     *
     * @param integer $code Code
     */
    protected function headerStatus($code)
    {
        $xLite = \XLite::getInstance();

        switch ($code) {
            case 400:
                $xLite->setStatusCode(400);
                $xLite->addHeader('Status', '400 Bad Request');
                break;

            case 403:
                $xLite->setStatusCode(403);
                $xLite->addHeader('Status', '403 Forbidden');
                $xLite->addHeader('X-Robots-Tag', 'noindex, nofollow');
                break;

            case 404:
                $xLite->setStatusCode(404);
                $xLite->addHeader('Status', '404 Not Found');
                $xLite->addHeader('X-Robots-Tag', 'noindex, nofollow');
                break;

            case 500:
                $xLite->setStatusCode(500);
                $xLite->addHeader('Status', '500 Internal Server Error');
                break;

            default:
        }
    }

    /**
     * startDownload
     *
     * @param string $filename    File name
     * @param string $contentType Content type OPTIONAL
     */
    protected function startDownload($filename, $contentType = 'application/force-download')
    {
        @set_time_limit(0);
        \XLite::getInstance()->addHeader('Content-type', $contentType);
        \XLite::getInstance()->addHeader('Content-disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * startImage
     */
    protected function startImage()
    {
        \XLite::getInstance()->addHeader('Content-type', 'image/gif');
        $this->set('silent', true);
    }

    /**
     * Filter XLite form ID
     *
     * @param string $url URL
     *
     * @return string
     */
    protected function filterXliteFormID($url)
    {
        if (preg_match('/(\?|&)(' . \XLite::FORM_ID . '=[a-zA-Z0-9]+)(&.+)?$/', $url, $matches)) {
            if ($matches[1] == '&') {
                $param = $matches[1] . $matches[2];
            } elseif (empty($matches[3])) {
                $param = $matches[1] . $matches[2];
            } else {
                $param = $matches[2] . '&';
            }

            $url = str_replace($param, '', $url);
        }

        return $url;
    }

    /**
     * @return array
     */
    protected function getViewerParams()
    {
        // FIXME: is it really needed?
        $params = [
            static::PARAM_SILENT => $this->get(static::PARAM_SILENT)
        ];

        if ($this->isAJAXViewer()) {
            $data = Request::getInstance()->getData();

            unset($data['target']);
            unset($data['action']);
            unset($data['template']);

            $params += $data;
        }

        return $params;
    }

    /**
     * Get current logged user profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

    /**
     * Check - need use secure protocol or not
     *
     * @return bool
     */
    public function needSecure()
    {
        return $this->isSecure()
            && !$this->isHTTPS()
            && !Request::getInstance()->isCLI()
            && Request::getInstance()->isGet();
    }

    /**
     * Redirect to secure protocol
     */
    protected function redirectToSecure()
    {
        $this->setHardRedirect();
        $this->assignAJAXResponseStatus();

        $url = \XLite::isAdminZone() ? \XLite::ADMIN_SELF : '';
        $this->redirect($this->getSecureShopURL($url, $this->getAllParams()), 301);
    }

    /**
     * Check - need to redirect
     *
     * @return bool
     */
    public function checkLanguage()
    {
        if (
            !LC_USE_CLEAN_URLS
            || !Request::getInstance()->isGet()
            || $this->isAJAX()
            || \XLite::isAdminZone()
            || !\XLite\Core\Router::getInstance()->isUseLanguageUrls()
        ) {
            return true;
        }

        $language = \XLite\Core\Session::getInstance()->getLanguage();

        $newLangCode = Request::getInstance()->getLanguageCode();
        if (
            $newLangCode &&
            $newLangCode !== $language->getCode()
        ) {
            \XLite\Core\Session::getInstance()->setLanguage($newLangCode);
        }

        if (
            $newLangCode === \XLite\Core\Config::getInstance()->General->default_language
            && $newLangCode !== $language->getCode()
        ) {
            return false;
        }

        return !(
            !$language->getDefaultAuth() &&
            $newLangCode !== $language->getCode()
        );
    }

    /**
     * Redirect to current language protocol
     */
    protected function redirectToCurrentLanguage()
    {
        $this->setHardRedirect();
        $this->assignAJAXResponseStatus();

        \XLite::getInstance()->addHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        \XLite::getInstance()->addHeader('Pragma', 'no-cache');

        $this->redirect($this->getShopURL($this->getURL()));
    }

    // }}}

    // {{{ Language-related routines

    /**
     * Get current language code
     *
     * @return string
     */
    public function getCurrentLanguage()
    {
        return \XLite\Core\Session::getInstance()->getLanguage()->getCode();
    }

    /**
     * Change current language
     */
    protected function doActionChangeLanguage()
    {
        $request = Request::getInstance();
        $code = (string)$request->language;

        $referrerUrl = $this->getReferrerURL();

        if (!empty($code)) {
            $language = \XLite\Core\Database::getRepo('XLite\Model\Language')->findOneByCode($code);

            if (isset($language) && $language->getEnabled()) {
                $session = \XLite\Core\Session::getInstance();
                $auth = \XLite\Core\Auth::getInstance();

                $pattern = '#^[/]*(' . $session->getCurrentLanguage() . ')(?:/|$)#i';
                $langCode = $language->getCode();

                $session->setLanguage($langCode);
                $request->setLanguageCode($langCode);

                if ($auth->isLogged()) {
                    $auth->getProfile()->setLanguage($langCode);
                    \XLite\Core\Database::getEM()->flush();
                }

                if (\XLite\Core\Router::getInstance()->isUseLanguageUrls()) {
                    $subReferrerUrl = substr($referrerUrl, strlen(\Includes\Utils\URLManager::getCurrentShopURL()));

                    if (preg_match($pattern, $subReferrerUrl, $matches)) {
                        $referrerUrl = substr_replace(
                            $referrerUrl,
                            $language->getDefaultAuth() ? '' : $langCode,
                            strlen(\Includes\Utils\URLManager::getCurrentShopURL()) + 1,
                            min($language->getDefaultAuth() ? 3 : 2, strlen($subReferrerUrl))
                        );
                    }
                }
            }
        }

        $this->setReturnURL($referrerUrl ?: $this->buildFullURL());
    }

    // }}}
}
