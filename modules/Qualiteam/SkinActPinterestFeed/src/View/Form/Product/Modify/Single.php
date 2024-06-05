<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestFeed\View\Form\Product\Modify;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Single extends \XLite\View\Form\Product\Modify\Single
{
    protected function setDataValidators(&$data)
    {
        parent::setDataValidators($data);

        $data->addPair('pinterest_id', new \XLite\Core\Validator\TypeInteger(), null, 'Pinterest category');
    }
}