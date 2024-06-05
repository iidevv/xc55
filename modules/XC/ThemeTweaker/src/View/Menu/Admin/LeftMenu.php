<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['theme_tweaker_templates'])) {
            $this->relatedTargets['theme_tweaker_templates'] = [];
        }

        $this->relatedTargets['theme_tweaker_templates'][] = 'theme_tweaker_template';
        $this->relatedTargets['theme_tweaker_templates'][] = 'custom_css';
        $this->relatedTargets['theme_tweaker_templates'][] = 'custom_js';

        if (\XLite\Core\Request::getInstance()->section === 'design') {
            $this->relatedTargets['theme_tweaker_templates'][] = 'labels';
        } else {
            $this->relatedTargets['units_formats'][] = 'labels';
        }

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (isset($items['store_design'][static::ITEM_CHILDREN])) {
            $items['store_design'][static::ITEM_CHILDREN]['theme_tweaker_templates'] = [
                static::ITEM_TITLE  => static::t('Customization'),
                static::ITEM_TARGET => 'theme_tweaker_templates',
                static::ITEM_WEIGHT => 250,
            ];
        }

        return $items;
    }
}
