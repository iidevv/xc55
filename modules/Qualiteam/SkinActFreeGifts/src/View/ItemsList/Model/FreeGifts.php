<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\ItemsList\Model;

use XLite\Core\Converter;
use XLite\View\FormField\Inline\Input\Text\Price;
use XLite\View\FormField\Inline\Input\Text;
use Qualiteam\SkinActFreeGifts\Model\FreeGift;

/**
 * Free Gifts items list
 */
class FreeGifts extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['free_gifts']);
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActFreeGifts/FreeGifts.less';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'tier_name' => [
                static::COLUMN_NAME         => static::t('SkinActFreeGifts Gift tier'),
                static::COLUMN_CLASS        => Text::class,
                static::COLUMN_PARAMS       => [
                    'required' => true,
                ],
                static::COLUMN_ORDERBY      => 100,
                static::COLUMN_MAIN         => true,
            ],
            'tier_min_price' => [
                static::COLUMN_NAME         => static::t('SkinActFreeGifts Gift min price'),
                static::COLUMN_CLASS        => Price::class,
                static::COLUMN_PARAMS       => [
                    'required' => true,
                ],
                static::COLUMN_ORDERBY      => 200
            ],
            'tier_max_price' => [
                static::COLUMN_NAME         => static::t('SkinActFreeGifts Gift max price'),
                static::COLUMN_CLASS        => Price::class,
                static::COLUMN_PARAMS       => [
                    'required' => true,
                ],
                static::COLUMN_ORDERBY      => 300
            ]
        ];
    }

    protected function getEditLink()
    {
        return 'gift_tier';
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return FreeGift::class;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('SkinActFreeGifts empty item list');
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add new';
    }

    /**
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' free-gifts';
    }

    /**
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return \Qualiteam\SkinActFreeGifts\View\StickyPanel\ItemsList\FreeGift::class;
    }

    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return Converter::buildURL('free_gifts');
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return 'free_gifts';
    }

    /**
     * Insert new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    protected function insertNewEntity(\XLite\Model\AEntity $entity)
    {
        $pos = 10;
        $entity->setPosition($pos);
        parent::insertNewEntity($entity);
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = [];

        $giftTierId = \XLite\Core\Request::getInstance()->id;
        if ($giftTierId) {
            $params['id'] = $giftTierId;
        }

        return array_merge(parent::getFormParams(), $params);
    }
}
