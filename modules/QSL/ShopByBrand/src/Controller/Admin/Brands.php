<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

class Brands extends \QSL\ShopByBrand\Controller\Admin\ABrand
{
    /**
     * Whether the brand list is editable.
     *
     * @var boolean
     */
    protected $isBrandListEditable;

    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Brands');
    }

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    // {{{ Search

    /**
     * Update list
     */
    protected function doActionUpdate()
    {
        $list = new \QSL\ShopByBrand\View\ItemsList\Model\Brand();
        $list->processQuick();
    }

    /**
     * Save search conditions
     */
    protected function doActionSearch()
    {
        $cellName = \QSL\ShopByBrand\View\ItemsList\Model\Brand::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();
        $request      = \XLite\Core\Request::getInstance();

        foreach (
            \QSL\ShopByBrand\View\ItemsList\Model\Brand::getSearchParams() as $requestParam
        ) {
            if (isset($request->$requestParam)) {
                $searchParams[$requestParam] = $request->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = \QSL\ShopByBrand\View\ItemsList\Model\Brand::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];
        }

        return $searchParams;
    }

    // }}}
}
