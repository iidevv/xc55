<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View;


class AddNewCardFrame extends \XLite\View\Controller
{

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActMain/AddNewCardFrame.twig';
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/iframe_common.js';
        $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/account/script.js';

        return $list;
    }

}