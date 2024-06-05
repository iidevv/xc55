<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

use XCart\Extender\Mapping\ListChild;

/**
 * "Continue shopping" button
 * @ListChild (list="center.top", weight="2000")
 */
class ContinueShopping extends \XLite\View\Button\Link
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ["cart"]);
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Continue shopping';
    }

    /**
     * getClass
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' action continue');
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        $urlParams = $this->getContinueShoppingParams(\XLite\Core\Session::getInstance()->continueShoppingURL);

        $url = $urlParams
            ? \XLite::getController()->getURL($urlParams)
            : '';

        return \XLite::getInstance()->getShopURL($url);
    }

    /**
     * Get continue shopping params
     *
     * @param array $params URL params
     *
     * @return array
     */
    protected function getContinueShoppingParams($params)
    {
        return isset($params['target']) && in_array($params['target'], $this->getAllowedContinueShoppingTargets(), true)
            ? $params
            : null;
    }

    /**
     * Returns allowed continue shopping targets
     *
     * @return array
     */
    protected function getAllowedContinueShoppingTargets()
    {
        return ['product', 'category', 'search'];
    }
}
