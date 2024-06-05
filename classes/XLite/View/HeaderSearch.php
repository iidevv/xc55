<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Header search
 *
 * @ListChild (list="admin.main.page.header.right", weight="100", zone="admin")
 */
class HeaderSearch extends \XLite\View\AView
{
    /**
     * Menu items
     *
     * @var array
     */
    protected $items;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'main_center/page_container_parts/header_parts/header_search.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'main_center/page_container_parts/header_parts/header_search.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return false;
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return [
            'class' => ['header-search'],
        ];
    }

    /**
     * Get menu items
     *
     * @return array
     */
    protected function getMenuItems()
    {
        if (!isset($this->items)) {
            $this->items = [];

            $items = $this->defineMenuItems();

            $selIndex = null;

            foreach ($items as $k => $v) {
                if (\XLite\Controller\Admin\SearchRouter::isSearchCodeAllowed($v['code'])) {
                    $this->items[$k] = $v;

                    if (
                        is_null($selIndex)
                        && (
                            empty($_COOKIE['XCartAdminHeaderSearchType'])
                            || $_COOKIE['XCartAdminHeaderSearchType'] == $v['code']
                        )
                    ) {
                        $selIndex = $k;
                    }
                }
            }

            if (!is_null($selIndex)) {
                $this->items[$selIndex]['selected'] = true;
            }
        }

        return $this->items;
    }

    /**
     * Get menu items
     *
     * @return array
     */
    protected function defineMenuItems()
    {
        return [
            [
                'name'        => static::t('Products'),
                'code'        => 'product',
                'placeholder' => static::t('Products') . ' - p: key',
            ],
            [
                'name'        => static::t('Users'),
                'code'        => 'profile',
                'placeholder' => static::t('Users') . ' - u: key',
            ],
            [
                'name'        => static::t('Orders'),
                'code'        => 'order',
                'placeholder' => static::t('Orders') . ' - o: key',
            ],
        ];
    }

    /**
     * Get current item
     *
     * @return array
     */
    protected function getCurrentItem()
    {
        $item = null;
        $list = $this->getMenuItems();
        foreach ($list as $v) {
            if (!empty($v['selected'])) {
                $item = $v;
                break;
            }
        }

        return $item;
    }
}
