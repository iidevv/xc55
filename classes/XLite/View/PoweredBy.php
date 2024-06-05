<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * 'Powered by' widget
 *
 * @ListChild (list="sidebar.footer", zone="customer")
 */
class PoweredBy extends \XLite\View\AView
{
    /**
     * Phrase to use in footer
     */
    public const PHRASE = '[shopping cart software]';

    /**
     * Advertise phrases
     *
     * @var array
     */
    protected static $phrases = [
        'en' => '[Powered by X-Cart]',
        'ru' => '[Создан на базе интернет магазина X-Cart]',
    ];

    /**
     * Site urls
     *
     * @var array
     */
    protected $siteURLs = [
        'ru' => 'https://www.x-cart.ru',
    ];

    /**
     * Check - display widget as link or as box
     *
     * @return boolean
     */
    public function isLink()
    {
        return \XLite\Core\Request::getInstance()->target == \XLite::TARGET_DEFAULT;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'css/powered_by.less';

        return $list;
    }

    /**
     * Return a Powered By message
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getMessage()
    {
        $installationLng = \XLite::getInstallationLng();
        $siteURL = $installationLng && isset($this->siteURLs[$installationLng])
            ? $this->siteURLs[$installationLng]
            : '';

        $replace = $this->isLink()
                 ? ['[' => '<a href="' . \XLite::getXCartURL($siteURL, empty($siteURL)) . '" rel="nofollow" target="_blank">', ']' => '</a>',]
                 : ['[' => '', ']' => ''];

        return strtr($this->getPhrase(), $replace);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/footer/powered_by.twig';
    }

    /**
     * Get a Powered By phrase
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getPhrase()
    {
        $installationLng = \XLite::getInstallationLng();

        return static::$phrases[$installationLng] ?? static::$phrases['en'] ?? self::PHRASE;
    }

    /**
     * Get company year
     *
     * @return string
     */
    protected function getCompanyYear()
    {
        $currentYear = (int)\XLite\Core\Converter::formatDate(\XLite\Core\Converter::time(), '%Y');
        $startYear = \XLite::isAdminZone() ? 2002 : (int)\XLite\Core\Config::getInstance()->Company->start_year;

        return $startYear && $startYear < $currentYear
            ? $startYear . ' - ' . $currentYear
            : $currentYear;
    }
}
