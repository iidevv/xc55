<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\View\ItemsList\Model;

use Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy\MobilePosition;
use Qualiteam\SkinActBannerAdvanced\Model\Repo\OrderBy\Position;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Banner extends \QSL\Banner\View\ItemsList\Model\Banner
{
    public function __construct(array $params = [])
    {
        $this->sortByModes = [
                (string)(new Position())       => 'Position',
                (string)(new MobilePosition()) => 'Mobile position',
            ] + $this->sortByModes;
        parent::__construct($params);
    }

    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    protected function defineColumns()
    {
        $list = [
            'position'        => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('SkinActBannerAdvanced web pos'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Input\Text\Integer',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT => (string)(new Position())
            ],
            'mobile_position' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('SkinActBannerAdvanced mobile pos'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Input\Text\Integer',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT => (string)(new MobilePosition()),
            ],
        ];

        $result = parent::defineColumns();

        $result['title'][static::COLUMN_MAIN] = true;

        return array_merge($list, $result);
    }

    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_ASC;
    }
}