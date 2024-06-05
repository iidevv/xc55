<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Form\Migration;

/**
 * Migration Wizard - Detect Transferable Data Form
 */
class DetectTransferableData extends \XC\MigrationWizard\View\Form\Migration\AForm
{
    /**
     * Return default value for the "class" parameter
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'migration-action detect-transferable-data';
    }
}
