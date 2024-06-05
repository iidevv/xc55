<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * APage
 * @Extender\Mixin
 */
class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/CustomProductTabs/product/controller.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CustomProductTabs/product/style.css';

        return $list;
    }

    /**
     * Define tabs
     *
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        foreach ($this->getProduct()->getTabs() as $tab) {
            $this->processTab($list, $tab);
        }

        return $list;
    }

    /**
     * Process tab addition into list
     *
     * @param                                                      $list
     * @param \XC\CustomProductTabs\Model\Product\Tab $tab
     */
    protected function processTab(&$list, $tab)
    {
        if ($tab->isAvailable()) {
            if ($tab->isGlobalStatic()) {
                $this->processGlobalTab($list, $tab);
            } else {
                $id = 'tab' . $tab->getId();
                $list[$id] = [
                    'widget'     => '\XC\CustomProductTabs\View\Product\Tabs\Tab',
                    'parameters' => [
                        'tab' => $tab,
                    ],
                    'name'       => $tab->getName(),
                    'weight'     => $tab->getPosition(),
                    'alt_id'     => preg_replace('/\W+/Ss', '-', strtolower($id)),
                ];

                if ($tab->getGlobalTab() && $tab->getGlobalTab()->getLink()) {
                    $list[$id]['id'] = $tab->getGlobalTab()->getLink();
                } elseif ($tab->getLink()) {
                    $list[$id]['id'] = $tab->getLink();
                } else {
                    unset($list[$id]['alt_id']);
                }
            }
        }
    }

    /**
     * Returns list of tabs brief info [tab_link => info]
     *
     * @return array
     */
    public function getTabsBriefInfo()
    {
        $result = [];

        foreach ($this->getProduct()->getTabs() as $tab) {
            if ($tab->getEnabled() && $tab->getBriefInfo()) {
                if ($tab->getGlobalTab() && $tab->getGlobalTab()->getLink()) {
                    $link = $tab->getGlobalTab()->getLink();
                } elseif ($tab->getLink()) {
                    $link = $tab->getLink();
                } else {
                    $link = preg_replace('/\W+/Ss', '-', strtolower('tab' . $tab->getId()));
                }

                $result['product-details-tab-' . $link] = [
                    'brief_info' => $tab->getBriefInfo(),
                    'title'      => $tab->getName(),
                ];
            }
        }

        return $result;
    }

    /**
     * Returns list of tabs brief info [tab_link => info]
     *
     * @return bool
     */
    public function hasTabsBriefInfo()
    {
        return count($this->getTabsBriefInfo()) > 0;
    }
}
