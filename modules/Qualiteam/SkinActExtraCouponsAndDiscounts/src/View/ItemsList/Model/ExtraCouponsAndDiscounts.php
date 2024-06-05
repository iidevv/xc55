<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\View\ItemsList\Model;

use Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo as EducationalVideoRepo;
use Qualiteam\SkinActVideoFeature\Model\Repo\VideoCategory as VideoCategoryRepo;
use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;

class ExtraCouponsAndDiscounts extends \XLite\View\ItemsList\Model\Table
{

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/items_list/style.less';

        return $list;
    }

    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog');
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('SkinActExtraCouponsAndDiscounts coupons blank');
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'extra_coupons_and_discounts';
    }

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . $this->getExtraCouponId();
    }

    protected function getExtraCouponId()
    {
        $controller = \XLite::getController();
        if ($controller instanceof \Qualiteam\SkinActExtraCouponsAndDiscounts\Controller\Admin\ExtraCouponsAndDiscounts) {
            return $controller->getExtraCouponId();
        }

        return null;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'coupon_code' => [
                static::COLUMN_NAME         => static::t('SkinActExtraCouponsAndDiscounts coupon code'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS       => ['required' => true],
                static::COLUMN_ORDERBY      => 100,
                static::COLUMN_NO_WRAP      => true,
                static::COLUMN_MAIN         => true,
                static::COLUMN_LINK         => 'extra_coupon',
            ],
            'title'     => [
                static::COLUMN_NAME         => static::t('SkinActExtraCouponsAndDiscounts tab title'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_ORDERBY      => 200,
            ],
            'stamp_text_1'     => [
                static::COLUMN_NAME         => static::t('SkinActExtraCouponsAndDiscounts stamp text line 1 field'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_ORDERBY      => 300,
            ],
            'stamp_text_2'     => [
                static::COLUMN_NAME         => static::t('SkinActExtraCouponsAndDiscounts stamp text line 2 field'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_ORDERBY      => 400,
            ],
            'value'    => [
                static::COLUMN_NAME     => static::t('SkinActExtraCouponsAndDiscounts discount'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_ORDERBY  => 500,
            ],
            'description'     => [
                static::COLUMN_NAME         => static::t('SkinActExtraCouponsAndDiscounts description field'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_ORDERBY      => 600,
            ],
        ];

        return $columns;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts::class;
    }

    protected function getCreateURL()
    {
        return $this->buildURL('extra_coupon');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return static::t('SkinActExtraCouponsAndDiscounts new discount coupon');
    }

    // {{{ Behaviors

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as switchyabvle (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    protected function preprocessValue($value, array $column, \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts $coupon)
    {
        return $coupon->isAbsolute()
            ? static::formatPrice($value)
            : round($value, 2) . '%';
    }

    // }}}

    /**
     * @inheritdoc
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' extra-coupons-and-discounts';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return \Qualiteam\SkinActExtraCouponsAndDiscounts\View\StickyPanel\ItemsList\ExtraCouponsAndDiscounts::class;
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return 'e.coupon_code';
    }
}