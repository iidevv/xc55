<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\View;

use XCart\Extender\Mapping\Extender;

/**
 * Warnings section widget
 * @Extender\Mixin
 */
class Warnings extends \XLite\View\Import\Warnings
{
    /**
     * Return title
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->getImportTarget() == \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY
            ? static::t(
                'The script found {{number}} errors during update inventory',
                [
                    'number' => \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->countLogs()
                ]
            )
            : parent::getTitle();
    }
}
