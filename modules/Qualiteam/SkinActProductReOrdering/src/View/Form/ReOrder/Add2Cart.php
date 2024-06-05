<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\View\Form\ReOrder;

class Add2Cart extends \XLite\View\Form\AForm
{
    protected function getClassName()
    {
        return parent::getClassName() . ' reorder-product-details';
    }
}