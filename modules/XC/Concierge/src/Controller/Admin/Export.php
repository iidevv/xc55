<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\Track;

/**
 * Export controller
 * @Extender\Mixin
 */
class Export extends \XLite\Controller\Admin\Export
{
    /**
     * Export action
     *
     * @return void
     */
    protected function doActionExport()
    {
        $request = \XLite\Core\Request::getInstance();

        if (in_array('XLite\Logic\Export\Step\Products', $request->section)) {
            $props = [
                'export-as'        => $request->options['files'] ?? '',
                'export-data'      => $request->options['attrs'] ?? '',
                'export-charset'   => $request->options['charset'] ?? '',
                'export-delimiter' => $request->options['delimiter'] ?? '',
            ];

            Mediator::getInstance()->addMessage(
                new Track('Export products', $props)
            );
        }

        parent::doActionExport();
    }
}
