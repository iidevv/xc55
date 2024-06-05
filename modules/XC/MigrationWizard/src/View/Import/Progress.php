<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Import;

/**
 * Progress section
 */
class Progress extends \XLite\View\Import\Progress
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return self::MIGRATION_WIZARD_MODULE_PATH . '/import/progress.twig';
    }

    /**
     * Get import event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XC\MigrationWizard\Logic\Import\Importer::getEventName();
    }

    /**
     * Return true if new task progress widget is available
     *
     * @return boolean
     */
    public function isNewEventTaskProgressWidget()
    {
        return defined('XLite\View\EventTaskProgress::PARAM_SHOW_CANCEL');
    }
}
