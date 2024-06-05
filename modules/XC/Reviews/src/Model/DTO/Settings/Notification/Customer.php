<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model\DTO\Settings\Notification;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Customer extends \XLite\Model\DTO\Settings\Notification\Customer
{
    protected function init($object)
    {
        /* @var Notification $object */
        parent::init($object);

        if ($object->getTemplatesDirectory() === 'modules/XC/Reviews/review_key') {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\\Reviews',
                'name' => 'enableCustomersFollowup',
                'value' => (bool)$this->settings->status,
            ]);
        }
    }
}
