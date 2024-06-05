<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\ItemsList;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild(list="gdpr-activities", zone="admin", weight="1000")
 */
class Modules extends \XLite\View\AView
{
    protected function getDefaultTemplate(): string
    {
        return 'modules/XC/GDPR/activities/modules.twig';
    }

    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/XC/GDPR/modules/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/XC/GDPR/modules/controller.js';

        return $list;
    }
}
