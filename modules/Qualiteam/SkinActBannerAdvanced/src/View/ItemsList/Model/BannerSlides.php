<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\View\ItemsList\Model;

use Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy\MobilePosition;
use Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy\Position;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\View\FormField\Inline\Input\Text\Integer;

/**
 * @Extender\Mixin
 */
class BannerSlides extends \QSL\Banner\View\ItemsList\Model\BannerSlides
{
    public function __construct(array $params = [])
    {
        $this->sortByModes = [
                (string)(new Position())       => 'Position',
                (string)(new MobilePosition()) => 'Mobile position',
            ] + $this->sortByModes;
        parent::__construct($params);
    }

    protected function defineColumns()
    {
        $list = [
            'position' => [
                static::COLUMN_NAME    => static::t('SkinActBannerAdvanced web pos'),
                static::COLUMN_CLASS   => Integer::class,
                static::COLUMN_ORDERBY => 10,
                static::COLUMN_SORT => (string)(new Position())
            ],
            'mobile_position' => [
                static::COLUMN_NAME    => static::t('SkinActBannerAdvanced mobile pos'),
                static::COLUMN_CLASS   => Integer::class,
                static::COLUMN_ORDERBY => 20,
                static::COLUMN_SORT => (string)(new MobilePosition())
            ],
        ];

        return $list + parent::defineColumns();
    }

    /**
     * @return int
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }
}