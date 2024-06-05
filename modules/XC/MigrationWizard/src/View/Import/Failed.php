<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Import;

/**
 * Failed section
 */
class Failed extends \XLite\View\Import\Failed
{
    /**
     * Get import event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XC\MigrationWizard\Logic\Import\Importer::getEventName();
    }
}
