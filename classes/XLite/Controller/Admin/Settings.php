<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Settings
 * todo: FULL REFACTOR!!!
 */
class Settings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Values to use for $page identification
     */
    public const GENERAL_PAGE     = 'General';
    public const COMPANY_PAGE     = 'Company';
    public const EMAIL_PAGE       = 'Email';
    public const ENVIRONMENT_PAGE = 'Environment';
    public const PERFORMANCE_PAGE = 'Performance';
    public const UNITS_PAGE       = 'Units';
    public const LAYOUT_PAGE      = 'Layout';
    public const CLEAN_URL        = 'CleanURL';
    public const SEO_HOMEPAGE     = 'SeoHomepage';
    public const API_PAGE         = 'API';

    /**
     * Params
     *
     * @var array
     */
    protected $params = ['target', 'page'];

    /**
     * Page
     *
     * @var string
     */
    public $page = self::GENERAL_PAGE;

    /**
     * Curl response temp variable
     *
     * @var mixed
     */
    private $curlResponse;

    /**
     * @var array
     */
    protected $requirements;

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        $list = $this->getPages();
        if (isset($list[$this->page])) {
            $classes[] = 'settings-'
                . \Includes\Utils\Converter::convertFromCamelCase(preg_replace('/\W/', '', $list[$this->page]), '-');
        }

        return $classes;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $list = $this->getPages();

        return $list[$this->page] ?? '';
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        $list = $this->getPages();

        /**
         * Settings controller is available directly if the $page request variable is provided
         * if the $page is omitted, the controller must be the subclass of Settings main one.
         *
         * The inner $page variable must be in the getPages() array
         */
        return parent::checkAccess()
            && isset($list[$this->page])
            && (
                ($this instanceof \XLite\Controller\Admin\Settings && isset(\XLite\Core\Request::getInstance()->page))
                || is_subclass_of($this, '\XLite\Controller\Admin\Settings')
            );
    }

    // {{{ Pages

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list                           = parent::getPages();
        $list[static::GENERAL_PAGE]     = static::t('Cart & checkout');
        $list[static::COMPANY_PAGE]     = static::t('Store info');
        $list[static::EMAIL_PAGE]       = static::t('Email settings');
        $list[static::CLEAN_URL]        = static::t('SEO settings');
        $list[static::SEO_HOMEPAGE]     = static::t('Homepage');
        $list[static::API_PAGE]         = static::t('API Settings');
        if (!\XLite::isTrial()) {
            $list[static::ENVIRONMENT_PAGE] = static::t('Environment');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        foreach ($this->getPages() as $name => $title) {
            $list[$name] = 'settings/base.twig';
        }

        $list[static::ENVIRONMENT_PAGE] = 'settings/summary/body.twig';
        $list[static::CLEAN_URL]        = 'settings/clean_url/tabs.twig';
        $list[static::SEO_HOMEPAGE]     = 'settings/clean_url/tabs.twig';

        return $list;
    }

    /**
     * @return array
     */
    public function getCleanUrlCommentedData()
    {
        $result = [];

        if (\XLite\Core\Request::getInstance()->page == 'CleanURL') {
            $result = [
                'companyName'             => \XLite\Core\Config::getInstance()->Company->company_name,
                'companyNameLabel'        => static::t('Company name'),
                'delimiter'               => " > ",
                'productTitle'            => static::t('Product'),
                'categoryTitle'           => static::t('Category'),
                'staticTitle'             => static::t('Page'),
                'categoryNameLabel'       => static::t('Category name'),
                'parentCategoryNameLabel' => static::t('Parent category name'),
                'productNameLabel'        => static::t('Product name'),
                'staticPageNameLabel'     => static::t('Page name'),
            ];
        }

        return $result;
    }

    // }}}

    // {{{ Other

    /**
     * Get options for current tab (category)
     *
     * @param bool $getAllOptions
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions($getAllOptions = false)
    {
        $result = \XLite\Core\Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible($this->page);
        if ($getAllOptions || $this->page != static::CLEAN_URL) {
            return $result;
        }

        $not_general_opts = ['home_page_title_and_meta', 'page_404', 'regular_text_404', 'show_email_404', 'about_404_page', 'result_404_page_preview'];

        return array_filter($result, static function ($opt) use ($not_general_opts) {
            return !in_array($opt->getName(), $not_general_opts);
        });
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Settings';
    }

    // }}}

    // {{{ Additional methods

    /**
     * Defines the article URL of setting up the clean URL functionality
     *
     * @return string
     */
    public function getCleanURLArticleURL()
    {
        return static::t('https://support.x-cart.com/en/articles/4889985-seo-friendly-urls-setup');
    }

    /**
     * Defines the article URL of setting up the clean URL functionality
     *
     * @return string
     */
    public function getInstallationDirectoryHelpLink()
    {
        return static::t('https://support.x-cart.com/en/articles/5214117-moving-x-cart-to-another-location');
    }

    /**
     * Check for the GDLib extension
     *
     * @return boolean
     */
    public function isGDLibLoaded()
    {
        return extension_loaded('gd') && function_exists('gd_info');
    }

    /**
     * isOpenBasedirRestriction
     *
     * @return boolean
     */
    public function isOpenBasedirRestriction()
    {
        $res = (string) @ini_get('open_basedir');

        return ($res !== '');
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getTranslationDriver()
    {
        return \XLite\Core\Translation::getInstance()->getDriver()->getName();
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getServerDateTime()
    {
        $time = new \DateTime('now');

        return $time->format('c');
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getServerTimezone()
    {
        $time = new \DateTime('now');

        return $time->getTimezone()->getName();
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getShopDateTime()
    {
        $time = new \DateTime('now', \XLite\Core\Converter::getTimeZone());

        return $time->format('c');
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getShopTimezone()
    {
        $time = new \DateTime('now', \XLite\Core\Converter::getTimeZone());

        return $time->getTimezone()->getName();
    }

    /**
     * Returns value by request
     *
     * @param string $name Type of value
     *
     * @return string
     */
    public function get($name)
    {
        switch ($name) {
            case 'phpversion':
                $return = PHP_VERSION;
                break;

            case 'os_type':
                [$osType] = explode(' ', PHP_OS);
                $return = $osType;
                break;

            case 'mysql_server':
                $return = \XLite\Core\Database::getEM()->getConnection()->executeQuery('SELECT VERSION()')->fetchOne();
                break;

            case 'innodb_support':
                $return = false;

                $engines = \XLite\Core\Database::getEM()->getConnection()->executeQuery('SHOW ENGINES')->fetchAllAssociative();
                foreach ($engines ?? [] as $row) {
                    if (strcasecmp('InnoDB', $row['Engine']) === 0) {
                        $return = true;
                        break;
                    }
                }

                break;

            case 'root_folder':
                $return = getcwd();
                break;

            case 'web_server':
                $return = $_SERVER['SERVER_SOFTWARE'] ?? '';
                break;

            case 'xml_parser':
                $return = $this->getXMLParserValue();
                break;

            case 'gdlib':
                $return = $this->getGdlibValue();
                break;

            case 'core_version':
                $return = \XLite::getInstance()->getVersion();
                break;

            case 'libcurl':
                $return = $this->getLibcurlValue();
                break;

            case 'license_keys':
                $return = [];
                break;

            default:
                $return = parent::get($name);
        }

        return $return;
    }

    /**
     * Get XML parser value
     *
     * @return string
     */
    public function getXMLParserValue()
    {
        ob_start();
        phpinfo(INFO_MODULES);
        $phpInfo = ob_get_contents();
        ob_end_clean();

        $return = null;
        if (preg_match('/EXPAT.+>([\.\d]+)/mi', $phpInfo, $m)) {
            $return = $m[1];
        } else {
            $return = function_exists('xml_parser_create') ? 'found' : '';
        }

        return $return;
    }

    /**
     * Get Gdlib value
     *
     * @return string
     */
    public function getGdlibValue()
    {
        $return = null;

        if (!$this->is('GDLibLoaded')) {
            $return = '';
        } else {
            ob_start();

            phpinfo(INFO_MODULES);

            $phpInfo = ob_get_contents();

            ob_end_clean();

            $gdVersion = @gd_info();
            $gdVersion = (is_array($gdVersion) && isset($gdVersion['GD Version']))
                ? $gdVersion['GD Version']
                : null;

            if (!$gdVersion) {
                $isMatched = preg_match('/GD.+>([\.\d]+)/mi', $phpInfo, $m);

                $gdVersion = $isMatched
                    ? $m[1]
                    : 'unknown';
            }

            $return = 'found (' . $gdVersion . ')';
        }

        return $return;
    }

    /**
     * Get Libcurl value
     *
     * @return string
     */
    public function getLibcurlValue()
    {
        $return = null;

        if (function_exists('curl_version')) {
            $libcurlVersion = curl_version();

            if (is_array($libcurlVersion)) {
                $libcurlVersion = $libcurlVersion['version'];
            }

            $return = $libcurlVersion;
        } else {
            $return = false;
        }

        return $return;
    }

    /**
     * Try permissions
     *
     * @param string $dir     Dir to create
     * @param string $modeStr Permissions string
     *
     * @return boolean
     */
    public function tryPermissions($dir, $modeStr = null)
    {
        $perm = substr(
            sprintf('%o', @fileperms($dir)),
            -4
        );

        return $modeStr === $perm;
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['phpinfo']);
    }

    /**
     * doActionPhpinfo
     *
     * @return void
     */
    public function doActionPhpinfo()
    {
        phpinfo();
        $this->setSuppressOutput(true);
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');

        if ($this->page === static::CLEAN_URL) {
            $this->saveLabels();
        }
    }

    /**
     * Get error message header
     *
     * @return string
     */
    protected function getErrorMessageHeader()
    {
        $message = 'Clean_urls_error_message';

        return static::t($message, ['url' => $this->curlResponse->uri]);
    }

    /**
     * Get error message by code
     *
     * @param integer $code Code
     *
     * @return string
     */
    protected function getErrorMessageCodeExplanation($code)
    {
        // TODO Add some explanation
        $explanation = '';
        switch ($code) {
            case 500:
                $explanation .= 'Internal server error';
                break;
            case 404:
                $explanation .= 'Page not found';
                break;
        }

        return static::t('Error code explanation:') . ' ' . $code . ' ' . $explanation;
    }

    /**
     * getStateById
     *
     * @param mixed $stateId State Id
     *
     * @return \XLite\Model\State
     */
    public function getStateById($stateId)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\State')->find($stateId);
    }

    protected function getLabelsToUpdate()
    {
        return [
            'default_site_title'            => 'default-site-title',
            'default_site_meta_description' => 'default-meta-description',
            'default_site_meta_keywords'    => 'default-meta-keywords',
        ];
    }

    /**
     * Update lang labels, create them if necessary
     */
    protected function saveLabels()
    {
        $labelsFromDb = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->findBy(['name' => $this->getLabelsToUpdate()]) ?: [];

        $list            = [];
        $nonFilteredData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        foreach ($this->getLabelsToUpdate() as $postName => $labelName) {
            if (!isset($nonFilteredData[$postName])) {
                continue;
            }

            $matchedLabelEntity = null;
            foreach ($labelsFromDb as $dbLabel) {
                if ($dbLabel->getName() === $labelName) {
                    $matchedLabelEntity = $dbLabel;
                    $matchedLabelEntity->getLabelTranslation()->setLabel($nonFilteredData[$postName]);
                    break;
                }
            }

            if (!$matchedLabelEntity) {
                // the label was deleted in DB, so create the new one
                $matchedLabelEntity = new \XLite\Model\LanguageLabel();
                $matchedLabelEntity->setName($labelName);
                \XLite\Core\Database::getEM()->persist($matchedLabelEntity);
            }

            $list[] = $matchedLabelEntity;
        }

        if ($list) {
            \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->updateInBatch($list);
            \XLite\Core\Translation::getInstance()->reset();
        }
    }

    // }}}
}
