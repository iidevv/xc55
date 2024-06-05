<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View\ItemsList;

use Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts as ExtraCouponsAndDiscountsModel;
use XLite\View\Pager\Infinity;

class ExtraCouponsAndDiscounts extends \XLite\View\ItemsList\AItemsList
{
    const WIDGET_TARGET = 'extra_coupons_and_discounts';
    const PARAM_COUPON  = 'coupon';

    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = static::WIDGET_TARGET;

        return $result;
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_COUPON => new \XLite\Model\WidgetParam\TypeObject(
                'Coupon',
                null,
                false,
                ExtraCouponsAndDiscountsModel::class,
            ),
        ];
    }

    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/items_list';
    }

    protected function getPageBodyDir()
    {
        return 'extra_coupons_and_discounts';
    }

    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET;
    }

    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->limit = [0, 1];
        $cnd->orderBy = ['e.id', 'desc'];
        $cnd->{\Qualiteam\SkinActExtraCouponsAndDiscounts\Model\Repo\ExtraCouponsAndDiscounts::P_ENABLED} = true;

        return parent::getData($cnd, $countOnly);
    }

    protected function isPagerVisible()
    {
        return 0 < $this->getItemsCount();
    }

    protected function getPagerClass()
    {
        return Infinity::class;
    }

    protected function defineRepositoryName()
    {
        return ExtraCouponsAndDiscountsModel::class;
    }

    protected function getListName()
    {
        return parent::getListName() . '.extra-coupons';
    }

    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' items-list-extra-coupons';
    }

    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    protected function isHeaderVisible()
    {
        return false;
    }

    protected function getCoupon()
    {
        return $this->getParam(static::PARAM_COUPON);
    }

    protected function hasTitle()
    {
        return $this->getCoupon() && $this->getCoupon()->getTitle();
    }

    protected function getTitle()
    {
        return $this->getCoupon()->getTitle();
    }

    protected function hasStampText1()
    {
        return $this->getCoupon() && $this->getCoupon()->getStampText1();
    }

    protected function getStampText1()
    {
        return $this->getCoupon()->getStampText1();
    }

    protected function hasStampText2()
    {
        return $this->getCoupon() && $this->getCoupon()->getStampText2();
    }

    protected function getStampText2()
    {
        return $this->getCoupon()->getStampText2();
    }

    protected function isShowCoupon()
    {
        return $this->hasStampText1() || $this->hasStampText2();
    }

    protected function getCouponCode()
    {
        return $this->getCoupon()->getCouponCode();
    }

    protected function hasDescription()
    {
        return $this->getCoupon() && $this->getCoupon()->getDescription();
    }

    protected function getDescription()
    {
        return $this->getCoupon()->getDescription();
    }

    protected function hasAdditionalContent()
    {
        return $this->getCoupon() && $this->getCoupon()->getAdditionalContent();
    }

    protected function getAdditionalContent()
    {
        return $this->getCoupon()->getAdditionalContent();
    }
}