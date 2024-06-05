<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View;

use XCart\Extender\Mapping\Extender;

/**
 * Invoice
 * @Extender\Mixin
 */
abstract class Invoice extends \XLite\View\Invoice
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'modules/QSL/SpecialOffersBase/invoice.js';
        // $list[static::RESOURCE_CSS][] = 'css/chosen/chosen.css';

        return $list;
    }
}
