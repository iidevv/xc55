<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\View\ItemsList\Model\Product\Admin;

use Qualiteam\SkinActGoogleFeedAdvanced\Main;
use Qualiteam\SkinActGoogleFeedAdvanced\View\FormField\Inline\Input\Checkbox\Switcher\AddToGoogleFeedCheckbox;
use XCart\Extender\Mapping\Extender;
use XLite\View\FormField\Inline\Input\Text;

/**
 * @Extender\Mixin
 */
class Search extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        foreach (Main::getGoogleAttributes() as $key => $googleAttribute) {
            if (Main::hasGoogleAttribute($googleAttribute)) {
                $this->sortByModes += [
                    $googleAttribute  => $googleAttribute,
                ];
            }
        }
    }

    protected function defineColumns()
    {
        $list = parent::defineColumns();

        $list['add_to_google_feed'] = [
            static::COLUMN_NAME    => static::t('SkinActGoogleFeedAdvanced add to google product feed'),
            static::COLUMN_CLASS   => AddToGoogleFeedCheckbox::class,
            static::COLUMN_ORDERBY => 10000,
        ];

        foreach (Main::getGoogleAttributes() as $key => $googleAttribute) {
            if (Main::hasGoogleAttribute($googleAttribute)) {
                $list[$googleAttribute] = [
                    static::COLUMN_NAME    => $this->getGoogleAttributeLabelTranslate($googleAttribute),
                    static::COLUMN_CLASS   => Text::class,
                    static::COLUMN_SORT    => $googleAttribute,
                    static::COLUMN_ORDERBY => $list['add_to_google_feed'][static::COLUMN_ORDERBY] + 1000 + 10 * $key,
                ];
            }
        }

        return $list;
    }

    protected function getGoogleAttributeLabelTranslate(string $attribute): string
    {
        return Main::getGoogleAttributeLabelTranslate($attribute);
    }
}
