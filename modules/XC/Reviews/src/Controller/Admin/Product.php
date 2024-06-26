<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Product modify controller
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if ($this->isDisplayReviewsTab()) {
            $list['product_reviews'] = static::t('Product reviews');
        }

        return $list;
    }

    /**
     * Handles the request
     *
     * @return void
     */
    public function handleRequest()
    {
        $cellName = \XC\Reviews\View\ItemsList\Model\Review::getSessionCellName();
        \XLite\Core\Session::getInstance()->$cellName = [
            \XC\Reviews\Model\Repo\Review::SEARCH_PRODUCT => $this->getProductId(),
        ];

        parent::handleRequest();
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if ($this->isDisplayReviewsTab()) {
            $list['product_reviews'] = 'modules/XC/Reviews/product/reviews.twig';
        }

        return $list;
    }

    protected function isDisplayReviewsTab()
    {
        return !$this->isNew()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage reviews');
    }
}
