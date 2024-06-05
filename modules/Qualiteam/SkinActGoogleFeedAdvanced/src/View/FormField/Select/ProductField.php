<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\View\FormField\Select;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Repo\Attribute as AttributeRepo;
use XLite\Model\Attribute as AttributeModel;

/**
 * @Extender\Mixin
 */
class ProductField extends \QSL\ProductFeeds\View\FormField\Select\ProductField
{
    protected function getSearchConditions()
    {
        $cnd = parent::getSearchConditions();

        $cnd->{AttributeRepo::SEARCH_TYPE} = [
            AttributeModel::TYPE_HIDDEN,
            AttributeModel::TYPE_SELECT,
        ];

        return $cnd;
    }
}
