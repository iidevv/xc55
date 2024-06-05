<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Menu\Customer;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\ListChild;

/**
 * Footer menu
 *
 * @ListChild (list="amp.layout.main.footer", weight="200")
 */
class Footer extends \XLite\View\Menu\Customer\Footer
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/modules/CDev/SimpleCMS/footer_menu.twig';
    }

    /**
     * Get a tree representation of items structure
     *
     * @return array
     */
    protected function getItemsTree()
    {
        $tree = $parents = [];

        foreach ($this->getItems() as &$item) {
            $item['children'] = [];

            $parents = array_slice($parents, 0, $item['depth']);

            if ($item['depth'] == 0) {
                $tree[] = $item;

                $parents[] = &$tree[count($tree) - 1];
            } else {
                $parent = &$parents[count($parents) - 1];

                $parent['children'][] = $item;

                $parents[] = &$parent['children'][count($parent['children']) - 1];
            }
        }

        return $tree;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return Manager::getRegistry()->isModuleEnabled('CDev', 'SimpleCMS');
    }

    /**
     * Amp components
     *
     * @return array
     */
    protected function getAmpComponents()
    {
        return ['amp-accordion'];
    }
}
