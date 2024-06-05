<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\ItemsList\Model;

/**
 * Gift tier items list
 */
class GiftTier extends \XLite\View\ItemsList\Model\Table
{
    public const PARAM_GIFT_TIER_ID = 'gift_tier_id';

//    /**
//     * Get a list of CSS files required to display the widget properly
//     *
//     * @return array
//     */
//    public function getCSSFiles()
//    {
//        $list   = parent::getCSSFiles();
//        $list[] = 'modules/CDev/FeaturedProducts/f_products/style.css';
//
//        return $list;
//    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions   = parent::getTopActions();
        $actions[] = 'modules/Qualiteam/SkinActFreeGifts/gift_tier/parts/create.twig';

        return $actions;
    }

    /**
     * Define the URL for popup product selector
     *
     * @return string
     */
    protected function getRedirectURL()
    {
        return $this->buildURL(
            'gift_tier',
            'add',
            \XLite\Core\Request::getInstance()->gift_tier_id
                ? [
                'gift_tier_id' => \XLite\Core\Request::getInstance()->gift_tier_id,
            ]
                : []
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku'     => [
                static::COLUMN_NAME    => static::t('SKU'),
                static::COLUMN_ORDERBY => 100,
            ],
            'product' => [
                static::COLUMN_NAME     => static::t('Product'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActFreeGifts/gift_tier/parts/info.product.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 200,
            ],
            'price'   => [
                static::COLUMN_NAME     => static::t('Price'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActFreeGifts/gift_tier/parts/info.price.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
            'amount'  => [
                static::COLUMN_NAME    => static::t('Stock'),
                static::COLUMN_ORDERBY => 400,
            ],
        ];
    }

    /**
     * The product column displays the product name
     *
     * @param \XLite\Model\Product $product
     *
     * @return string
     */
    protected function preprocessProduct(\XLite\Model\Product $product)
    {
        return $product->getName();
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'Qualiteam\SkinActFreeGifts\Model\FreeGiftItem';
    }

    // {{{ Behaviors

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
     * @return bool
     */
    public function isCrossIcon()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_GIFT_TIER_ID => new \XLite\Model\WidgetParam\TypeInt(
                'GiftTier ID ',
                $this->getGiftTierId(),
                false
            ),
        ];
    }

    /**
     * The gift tier ID is defined from the 'id' request variable
     *
     * @return string
     */
    protected function getGiftTierId()
    {
        return \XLite\Core\Request::getInstance()->gift_tier_id;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' gift_tier';
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return true;
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'Qualiteam\SkinActFreeGifts\View\StickyPanel\ItemsList\GiftTier';
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \Qualiteam\SkinActFreeGifts\Model\Repo\FreeGiftItem::SEARCH_GIFT_TIER_ID => static::PARAM_GIFT_TIER_ID,
        ];
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams                             = parent::getCommonParams();
        $this->commonParams[static::PARAM_GIFT_TIER_ID] = \XLite\Core\Request::getInstance()->{static::PARAM_GIFT_TIER_ID};

        return $this->commonParams;
    }

    // }}}
}
