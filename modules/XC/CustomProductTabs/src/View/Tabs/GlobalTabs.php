<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class GlobalTabs extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'global_tabs';
        $list[] = 'global_tab';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'global_tabs' => [
                'weight'   => 100,
                'title'    => static::t('Listing info tabs'),
                'references' => [
                    ['target' => 'global_tab'],
                ],
                'template' => 'modules/XC/CustomProductTabs/global_tabs/list.twig',
            ]
        ];
    }

    public function getTabTemplate()
    {
        return ($this->getCurrentTarget() === 'global_tab')
            ? 'modules/XC/CustomProductTabs/global_tab/body.twig'
            : parent::getTabTemplate();
    }
}
