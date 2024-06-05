<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['sales_tax'])) {
            $this->relatedTargets['sales_tax'] = [];
        }

        $this->relatedTargets['sales_tax'][]      = 'tax_classes';
        $this->relatedTargets['canadian_taxes'][] = 'tax_classes';
        $this->relatedTargets['vat_tax'][]        = 'sales_tax';
        $this->relatedTargets['canadian_taxes'][] = 'sales_tax';

        parent::__construct($params);
    }
}
