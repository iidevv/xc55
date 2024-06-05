<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Controller\Admin;

use Qualiteam\SkinActMagicImages\Model\Config;
use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use Qualiteam\SkinActMagicImages\View\Model\Settings;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * Magic360 settings page controller
 *
 */
class Magic360Settings extends \XLite\Controller\Admin\AAdmin
{
    use MagicImagesTrait;

    /**
     * Pages
     */
    const PAGE_DEFAULTS         = 'default';
    const PAGE_PRODUCT_SETTINGS = 'product';
    const PAGE_RESET_SETTINGS   = 'reset';

    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'page'];

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Magic 360 module settings');
    }

    /**
     * Handles the request.
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        if (!$this->isPageValid(Request::getInstance()->page)) {
            $this->setHardRedirect();
            $this->setReturnURL(
                $this->buildURL(
                    'magic360_settings',
                    '',
                    [
                        'page' => $this->getDefaultPage(),
                    ]
                )
            );
            $this->doRedirect();
        }
    }

    /**
     * Check if page is valid
     *
     * @param string $page Page to check
     *
     * @return boolean
     */
    public function isPageValid($page)
    {
        return in_array(strval($page), array_keys(self::getAllPages()));
    }

    /**
     * Get all pages
     *
     * @return array
     */
    public static function getAllPages()
    {
        return [
            static::PAGE_DEFAULTS         => static::t('Defaults'),
            static::PAGE_PRODUCT_SETTINGS => static::t('Product page'),
        ];
    }

    /**
     * Get default page
     *
     * @return string
     */
    public function getDefaultPage()
    {
        return static::PAGE_DEFAULTS;
    }

    /**
     * Get pages
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list += $this->getAllPages();

        return $list;
    }

    /**
     * Update module settings
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $page = Request::getInstance()->page;

        $this->getModelForm()->performAction('update');
    }

    /**
     * Return module options
     *
     * @return array
     */
    public function getOptions()
    {
        $profile = Request::getInstance()->page;

        return Database::getRepo(Config::class)->findByProfile($profile);
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        foreach ($this->getAllPages() as $page => $title) {
            $list[$page] = $this->getModulePath() . '/settings.twig';
        }

        return $list;
    }

    /**
     * Get class name for the form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return Settings::class;
    }
}
