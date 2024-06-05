<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\Order\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Model extends \XLite\View\Order\Details\Admin\Model
{
    /**
     * Define modifier form field widget
     *
     * @param array $modifier Modifier
     *
     * @return \XLite\View\FormField\Inline\AInline
     */
    protected function defineDcouponModifierWidget(array $modifier)
    {
        return $this->getWidget(
            [
                'entity'    => $modifier['object'],
                'fieldName' => $modifier['object']->getCode(),
                'name'      => $modifier['object']->getCode(),
                'namespace' => 'modifiersTotals',
            ],
            'CDev\Coupons\View\FormField\Inline\Input\Hidden\OrderModifierTotal'
        );
    }
}
