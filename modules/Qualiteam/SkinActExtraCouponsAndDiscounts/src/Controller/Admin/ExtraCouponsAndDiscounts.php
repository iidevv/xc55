<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Controller\Admin;

class ExtraCouponsAndDiscounts extends \XLite\Controller\Admin\AAdmin
{
    public function getTitle()
    {
        return static::t('SkinActExtraCouponsAndDiscounts extra coupons and discounts');
    }

    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: \Qualiteam\SkinActExtraCouponsAndDiscounts\View\ItemsList\Model\ExtraCouponsAndDiscounts::class;
    }

    /**
     * @return int|null
     */
    public function getExtraCouponId()
    {
        return $this->getExtraCoupon() ? $this->getExtraCoupon()->getId() : null;
    }

    /**
     * Returns sale discount
     *
     * @return \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts
     */
    protected function getExtraCoupon()
    {
        return $this->getModelForm()->getModelObject();
    }

    protected function getModelFormClass()
    {
        return \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts::class;
    }
}