<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\TopCategories;

/**
 * List of discount links
 */
abstract class AAdditionalLinks extends \XLite\View\AView
{
    /**
     * @return array
     */
    protected function getSaleDiscounts()
    {
        $activeDiscounts = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')->findAllActive();

        $controller = \XLite::getController();
        $profile = null;

        if ($controller instanceof \XLite\Controller\Customer\ACustomer) {
            $profile = $controller->getCart(true)->getProfile()
                ?: \XLite\Core\Auth::getInstance()->getProfile();
        }

        if (!$profile) {
            $profile = new \XLite\Model\Profile();
        }

        $result = [];

        /** @var \CDev\Sale\Model\SaleDiscount $discount */
        foreach ($activeDiscounts as $discount) {
            if (
                $discount->isActive()
                && $discount->getShowInSeparateSection()
                && $discount->isApplicableForProfile($profile)
            ) {
                $result[] = $discount;
            }
        }

        return $result;
    }

    /**
     * @param \CDev\Sale\Model\SaleDiscount $saleDiscount
     * @return string
     */
    protected function getSalePageLink(\CDev\Sale\Model\SaleDiscount $saleDiscount)
    {
        return $this->buildURL('sale_discount', '', ['id' => $saleDiscount->getId()]);
    }

    /**
     * @return mixed|null
     */
    protected function getCurrentDiscountId()
    {
        $controller = \XLite::getController();
        if ($controller instanceof \CDev\Sale\Controller\Customer\SaleDiscount) {
            return $controller->getSaleDiscountId();
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Sale/top_categories/additional_links.twig';
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = $this->getCacheKeyPartsGenerator()->getMembershipPart();
        $params[] = $this->getCurrentDiscountId();

        return $params;
    }
}
