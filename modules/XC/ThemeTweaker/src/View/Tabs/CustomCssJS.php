<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to shipping
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class CustomCssJS extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'theme_tweaker_templates';
        $list[] = 'custom_css';
        $list[] = 'custom_js';
        $list[] = 'labels';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'theme_tweaker_templates' => [
                'weight' => 100,
                'title'  => static::t('Edited templates'),
                'widget' => 'XC\ThemeTweaker\View\ThemeTweakerTemplates',
            ],
            'custom_css' => [
                'weight' => 200,
                'title'  => static::t('Custom CSS'),
                'widget' => 'XC\ThemeTweaker\View\Page\CustomCSS',
            ],
            'custom_js'  => [
                'weight' => 300,
                'title'  => static::t('Custom JavaScript'),
                'widget' => 'XC\ThemeTweaker\View\Page\CustomJS',
            ],
            'labels'  => [
                'weight' => 400,
                'title'  => static::t('Edit labels'),
                'url_params' => [
                    'section'      => 'design',
                ],
                'widget' => 'XLite\View\LanguagesModify\Labels',
            ],
        ];
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Request::getInstance()->section !== 'store';
    }
}
