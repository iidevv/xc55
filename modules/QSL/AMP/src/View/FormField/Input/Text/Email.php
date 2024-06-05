<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\FormField\Input\Text;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Email extends \XLite\View\FormField\Input\Text\Email
{
    public const FIELD_TYPE_AMP_EMAIL = 'email';

    /**
     * @inheritdoc
     */
    public function getFieldType()
    {
        return static::isAMP() ? static::FIELD_TYPE_AMP_EMAIL : parent::getFieldType();
    }
}
