<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\ListChild;

/**
 * Tax banner page
 *
 * @ListChild (list="taxes.help.section", zone="admin", weight=10)
 */
class TaxBanner extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'tax_classes';
        $result[] = 'sales_tax';
        $result[] = 'vat_tax';
        $result[] = 'canadian_taxes';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'tax_banner/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'tax_banner/body.twig';
    }

    /**
     * Define list of help links
     *
     * @return array
     */
    protected function defineHelpLinks()
    {
        $links   = [];
        $links[] = [
            'title' => static::t('Setting up tax'),
            'url'   => static::t('https://support.x-cart.com/en/articles/4929803-tax-setup-checklist'),
        ];
        $links[] = [
            'title' => static::t('Setting up tax classes'),
            'url'   => static::t('https://support.x-cart.com/en/articles/4931700-tax-classes'),
        ];
        $links[] = [
            'title' => static::t('Setting up European / UK Taxes'),
            'url'   => static::t('https://support.x-cart.com/en/articles/4936606-european-and-uk-taxes'),
        ];
        $links[] = [
            'title' => static::t('Setting up US Taxes'),
            'url'   => static::t('https://support.x-cart.com/en/articles/4942100-us-taxes'),
        ];
        $links[] = [
            'title' => static::t('Setting up Canadian taxes'),
            'url'   => static::t('https://support.x-cart.com/en/articles/4983449-canadian-taxes'),
        ];

        return $links;
    }

    /**
     * Get list of help links
     *
     * @return array
     */
    protected function getHelpLinks()
    {
        return $this->defineHelpLinks();
    }

    /**
     * Return AvaTax Module link
     *
     * @return string
     */
    protected function getAvaTaxLink()
    {
        return Manager::getRegistry()->getModuleServiceURL('XC', 'AvaTax');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Controller\Admin\TaxClasses::isEnabled();
    }
}
