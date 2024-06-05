<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Controller\Customer;

class SaleDiscount extends \XLite\Controller\Customer\ACustomer
{
    protected $id;

    /**
     * @return int|null
     */
    public function getSaleDiscountId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * @return boolean
     */
    public function isTitleVisible()
    {
        return $this->isVisible();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible()
            ? $this->getSaleDiscount()->getName()
            : static::t('Page not found');
    }

    /**
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public function getTitleObjectPart()
    {
        $discount = $this->getSaleDiscount();

        return ($discount && $discount->getMetaTitle()) ? $discount->getMetaTitle() : $this->getTitle();
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        $discount = $this->getSaleDiscount();

        return $discount ? $discount->getMetaDesc() : parent::getMetaDescription();
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        $discount = $this->getSaleDiscount();

        return $discount ? $discount->getMetaTags() : parent::getKeywords();
    }

    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct();

        $this->params[] = 'id';
        $this->id = $this->getSaleDiscountId();
    }

    /**
     * @return \CDev\Sale\Model\SaleDiscount
     */
    public function getSaleDiscount()
    {
        $discountId = $this->getSaleDiscountId();
        return $this->executeCachedRuntime(static function () use ($discountId) {
            return \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')
                    ->find($discountId);
        }, ['getSaleDiscount', $discountId]);
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        $profile = null;

        $profile = $this->getCart(true)->getProfile()
            ?: \XLite\Core\Auth::getInstance()->getProfile();

        if (!$profile) {
            $profile = new \XLite\Model\Profile();
        }

        return parent::isVisible()
            && $this->getSaleDiscount() !== null
            && $this->getSaleDiscount()->getShowInSeparateSection()
            && $this->getSaleDiscount()->isActive()
            && $this->getSaleDiscount()->isApplicableForProfile($profile);
    }
}
