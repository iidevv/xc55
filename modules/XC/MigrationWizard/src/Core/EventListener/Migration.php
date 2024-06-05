<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Core\EventListener;

/**
 * Migration
 */
class Migration extends \XLite\Core\EventListener\Import
{
    /**
     * Initialize step
     *
     * @return void
     */
    protected function initializeStep()
    {
        parent::initializeStep();

        // Get Migration Wizard configuration options
        $options = \XLite::getInstance()->getOptions('migration_wizard', 'migration_chunk_length');

        $this->counter = isset($options['migration_chunk_length'])
            ? ((int) $options['migration_chunk_length'])
            : static::CHUNK_LENGTH;

        if (defined('MIGRATION_WIZARD_CHUNK')) {
            $this->counter = MIGRATION_WIZARD_CHUNK;
        }
    }

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XC\MigrationWizard\Logic\Migration\Wizard::EVENT_NAME;
    }

    /**
     * Get items
     *
     * @return array
     */
    protected function getItems()
    {
        if (!isset($this->importer)) {
            $this->importer = new \XC\MigrationWizard\Logic\Import\Importer(
                $this->record['options'] ?? \XC\MigrationWizard\Logic\Migration\Wizard::$defaultMigrationOptions
            );
        }

        return $this->importer->getStep();
    }

    /**
     * Get event step length
     *
     * @return integer
     */
    protected function getLength()
    {
        return $this->getItems()->count();
    }

    /**
     * Handle event (internal, after checking)
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     *
     * @return boolean
     */
    public function handleEvent($name, array $arguments)
    {
        $start = microtime(true);

        $result = parent::handleEvent($name, $arguments);

        \XLite\Core\TmpVars::getInstance()->{\XC\MigrationWizard\Logic\Migration\Wizard::MIGRATION_TIME_KEY} += microtime(true) - $start;

        return $result;
    }
}
