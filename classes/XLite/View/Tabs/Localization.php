<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to localization
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Localization extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'units_formats';
        $list[] = 'currency';
        $list[] = 'countries';
        $list[] = 'states';
        $list[] = 'zones';
        $list[] = 'languages';
        $list[] = 'labels';

        return $list;
    }

    /**
     * Check if zone details page should be displayed
     *
     * @return bool
     */
    public function isDisplayZoneDetails()
    {
        return \XLite\Core\Request::getInstance()->mode === 'add' || $this->getZone();
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'units_formats' => [
                'weight'   => 100,
                'title'    => static::t('Units & Formats'),
                'template' => 'settings/body.twig',
            ],
            'currency'      => [
                'weight'   => 200,
                'title'    => static::t('Currency'),
                'template' => 'currency.twig',
            ],
            'countries'     => [
                'weight' => 300,
                'title'  => static::t('Countries'),
                'widget' => 'XLite\View\ItemsList\Model\Country',
            ],
            'states'        => [
                'weight' => 400,
                'title'  => static::t('States'),
                'widget' => 'XLite\View\ItemsList\Model\State',
            ],
            'zones'         => [
                'weight'   => 500,
                'title'    => static::t('Zones'),
                'template' => 'zones/body.twig',
                'jsFiles'  => 'zones/details/controller.js',
            ],
            'languages'     => [
                'weight' => 600,
                'title'  => static::t('Languages'),
                'widget' => 'XLite\View\LanguagesModify\Languages',
            ],
            'labels'     => [
                'weight' => 700,
                'title'  => static::t('Labels'),
                'url_params' => [
                    'section'      => 'store',
                ],
                'widget' => 'XLite\View\LanguagesModify\Labels',
            ],
        ];
    }

    /**
     * Disable city masks field in the interface
     *
     * @return bool
     */
    protected function isCityMasksEditEnabled()
    {
        return true;
    }

    /**
     * Disable address masks field in the interface
     *
     * @return bool
     */
    protected function isAddressMasksEditEnabled()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Request::getInstance()->section !== 'design';
    }
}
