<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\XC\MailChimp\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\MailChimp")
 */
abstract class Main extends \XLite\View\Model\Profile\Main
{
    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        if (isset($schema[\XC\MailChimp\Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME])) {
            $schema[\XC\MailChimp\Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME][self::SCHEMA_IS_CHECKED] = !\XLite\Core\Auth::getInstance()->isUserFromGdprCountry();
        }

        return parent::getFieldsBySchema($schema);
    }
}
