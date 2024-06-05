<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Core;

use XCart\Extender\Mapping\Extender;

/**
 * EventListener
 * @Extender\Mixin
 */
class EventListener extends \XLite\Core\EventListener
{
    /**
     * Get listeners
     *
     * @return array
     */
    protected function getListeners()
    {
        return parent::getListeners() + [
            \XC\MigrationWizard\Logic\Import\Importer::getEventName() => ['XC\MigrationWizard\Core\EventListener\Migration'],
            \XC\MigrationWizard\Logic\RemoveDuplicateImages\Generator::getEventName() => ['XC\MigrationWizard\Core\EventListener\RemoveDuplicateImages'],
        ];
    }
}
