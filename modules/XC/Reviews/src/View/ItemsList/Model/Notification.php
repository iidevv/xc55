<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Notification extends \XLite\View\ItemsList\Model\Notification
{
    protected function updateEntities()
    {
        parent::updateEntities();

        foreach ($this->getPageDataForUpdate() as $notification) {
            /* @var \XLite\Model\Notification $notification */
            if ($notification->getTemplatesDirectory() === 'modules/XC/Reviews/review_key') {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                    'category' => 'XC\\Reviews',
                    'name' => 'enableCustomersFollowup',
                    'value' => (bool)$notification->getEnabledForCustomer(),
                ]);
            }
        }
    }
}
