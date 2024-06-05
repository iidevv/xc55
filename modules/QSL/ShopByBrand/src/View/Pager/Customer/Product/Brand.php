<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Pager\Customer\Product;

class Brand extends \XLite\View\Pager\Customer\Product\AProduct
{
    public const PARAM_BRAND_ID = 'brand_id';

    /**
     * Return current brand model object.
     *
     * @return \XLite\Model\Category
     */
    protected function getBrand()
    {
        return $this->getWidgetParams(self::PARAM_BRAND_ID)->getObject();
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_BRAND_ID => new \QSL\ShopByBrand\Model\WidgetParam\ObjectId\Brand(
                'Brand ID',
                null
            ),
        ];
    }

    /**
     * Define so called "request" parameters
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        if ($this->isPageIdResetRequired()) {
            unset($this->requestParams[array_search(static::PARAM_PAGE_ID, $this->requestParams)]);
        }

        $this->requestParams[] = self::PARAM_BRAND_ID;
    }

    /**
     * Check if the page_id request parameter should be reset to the first page, or not.
     *
     * @return bool
     */
    protected function isPageIdResetRequired()
    {
        $request = \XLite\Core\Request::getInstance();

        return !$this->isAJAX() && !isset($request->{static::PARAM_PAGE_ID});
    }

    /**
     * Should we use cache for pageId
     *
     * @return bool
     */
    protected function isSavedPageId()
    {
        return false;
    }
}
