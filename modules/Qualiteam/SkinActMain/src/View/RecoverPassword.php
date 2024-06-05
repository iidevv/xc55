<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View;

use Qualiteam\SkinActMain\AModule;
use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;

/**
 * @ListChild (list="recover.password.fields", weight="100", zone="customer")
 */
class RecoverPassword extends AView
{
    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return AModule::getModulePath() . 'modules/XC/CrispWhiteSkin/recover_password/parts/form.email.twig';
    }
}