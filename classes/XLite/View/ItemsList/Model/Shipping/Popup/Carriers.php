<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping\Popup;

use XLite\View\ItemsList\Model\Shipping\CarriersTrait;

/**
 * Shipping carriers list in the popup
 */
abstract class Carriers extends \XLite\View\ItemsList\AItemsList
{
    use CarriersTrait;

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'items_list/shipping/popup_methods/style.less';

        return $list;
    }

    /**
     * Returns a list of CSS classes (separated with a space character) to be attached to the items
     * list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' methods';
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'shipping/popup_methods';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Admin\Model\Infinity';
    }

    // {{{ Search

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Shipping\Method::P_CARRIER}  = '';
        $result->{\XLite\Model\Repo\Shipping\Method::P_ORDER_BY} = ['m.position', 'ASC'];

        return $result;
    }

    /**
     * Return shipping methods
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->search($cnd, $countOnly);

        return $result;
    }

    // {{{ Content helpers

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowRemoveButton(\XLite\Model\Shipping\Method $method)
    {
        return false;
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowAddButton(\XLite\Model\Shipping\Method $method)
    {
        return false;
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowInstallButton(\XLite\Model\Shipping\Method $method)
    {
        return false;
    }
}
