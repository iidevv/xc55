<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Module\QSL\AdvancedContactUs\View\Page\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;
use QSL\reCAPTCHA\Main;

/**
 * @Extender\Mixin
 * @Extender\Depend ("QSL\AdvancedContactUs")
 */
class AdvancedContactUs extends \QSL\AdvancedContactUs\View\Page\Customer\AdvancedContactUs
{
    public function getFields()
    {
        $fields = parent::getFields();

        if (
            Main::isACUIntegrated()
            && Validator::getInstance()->isRequiredForContactForm()
        ) {
            $fields[] = new \QSL\reCAPTCHA\View\FormField\ReCAPTCHA();
        }

        return $fields;
    }
}
