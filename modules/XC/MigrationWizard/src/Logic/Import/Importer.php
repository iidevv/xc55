<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import;

/**
 * Importer
 */
class Importer extends \XLite\Logic\Import\Importer
{
    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->options['importMode'] = \XLite\View\Import\Begin::MODE_UPDATE_AND_CREATE;
    }


    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            //'XC\MigrationWizard\Logic\Import\Step\RemoveData',
            'XC\MigrationWizard\Logic\Import\Step\Import',
            'XC\MigrationWizard\Logic\Import\Step\QuickData',
            'XC\MigrationWizard\Logic\Import\Step\CategoriesStructure',
            'XC\MigrationWizard\Logic\Import\Step\RemoveDuplicateImages',
            //'XC\MigrationWizard\Logic\Import\Step\ImageResize',
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get default target
     *
     * @return string
     */
    public static function getDefaultTarget()
    {
        return \XLite\Core\Converter::convertFromCamelCase(
            \XC\MigrationWizard\Logic\Migration\Wizard::CELL_NAME
        );
    }

    /**
     * Get processors
     *
     * @return array
     */
    public function getProcessors()
    {
        if (empty($this->processors)) {
            $wizard = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance();

            if (
                $wizard
                && $wizard->getStep('DetectTransferableData')
                && ($rules = $wizard->getStep('DetectTransferableData')->getSelectedRules())
            ) {
                $this->processors = $rules;
            } else {
                $this->processors = [];
            }

            $this->prepareProcessors();
        }

        return $this->processors;
    }

    /**
     * Get processors with data
     *
     * @return array
     */
    public function getProcessorsWithData()
    {
        $result = [];

        $processors = $this->getProcessors();

        foreach ($processors as $processor) {
            if ($processor::hasTransferableData()) {
                $result = array_merge(
                    $result,
                    [get_class($processor)]
                );
            }
        }

        return $result;
    }

    public function setMigrationCache($cacheName, $key, $value)
    {
        $this->getOptions()->migrationCache[$cacheName][$key] = $value;
    }

    public function hasMigrationCache($cacheName, $key)
    {
        return isset($this->getOptions()->migrationCache[$cacheName][$key])
            && $this->getOptions()->migrationCache[$cacheName][$key] !== null;
    }

    public function getMigrationCache($cacheName, $key)
    {
        return $this->getOptions()->migrationCache[$cacheName][$key] ?? null;
    }

    // }}} </editor-fold>

    // {{{ Initialize <editor-fold desc="Initialize" defaultstate="collapsed">

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
        if (!property_exists($this->getOptions(), 'migrationCache')) {
            $this->getOptions()->migrationCache = [];
        }

        \XLite\Core\TmpVars::getInstance()->mig_wizard_import_time = 0;

        // Preprocess import files
        $this->preprocessFiles();

        // Delete all logs
        \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->clearAll();

        // Preprocess import data
        $this->preprocessImport();

        // Save import options if they were changed
        $record = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState(static::getEventName());
        // $record['state'] = \XLite\Core\EventTask::STATE_IN_PROGRESS;
        $record['options']                = $this->getOptions()->getArrayCopy();
        $record['options']['initialized'] = true;
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setEventState(static::getEventName(), $record);
    }

    // }}} </editor-fold>

    // {{{ Processors <editor-fold desc="Processors" defaultstate="collapsed">

    /**
     * Prepare processors
     *
     * @return void
     */
    protected function prepareProcessors()
    {
        $processors = [];

        foreach ($this->processors as $processor) {
            if ($processor::hasPreProcessors()) {
                $processors = array_merge($processors, $processor::getPreProcessors());
            }

            $processors[] = $processor;

            if ($processor::hasPostProcessors()) {
                $processors = array_merge($processors, $processor::getPostProcessors());
            }
        }

        $this->processors = array_unique($processors);

        parent::prepareProcessors();
    }

    // }}} </editor-fold>

    // {{{ Pre-processors <editor-fold desc="Pre-processors" defaultstate="collapsed">

    /**
     * Preprocess import files
     *
     * @return void
     */
    protected function preprocessFiles()
    {
        return null;
    }

    /**
     * Preprocess import data.
     *
     * @return boolean
     */
    protected function preprocessImport()
    {
        return false;
    }

    // }}} </editor-fold>

    // {{{ Run <editor-fold desc="Run" defaultstate="collapsed">

    /**
     * Run
     *
     * @param array $options Options
     *
     * @return void
     */
    public static function run(array $options)
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getImportCancelFlagVarName(), false);
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getImportUserBreakFlagVarName(), false);

        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState(
            static::getEventName(),
            ['options' => $options]
        );

        call_user_func(['XLite\Core\EventTask', static::getEventName()]);
    }

    // }}} </editor-fold>

    // {{{ Service variable names <editor-fold desc="Service variable names" defaultstate="collapsed">

    /**
     * Get import cancel flag name
     *
     * @return string
     */
    public static function getImportCancelFlagVarName()
    {
        return static::getEventName() . 'CancelFlag';
    }

    /**
     * Get import user break flag name
     *
     * @return string
     */
    public static function getImportUserBreakFlagVarName()
    {
        return static::getEventName() . 'UserBreak';
    }

    /**
     * Get import event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return \XC\MigrationWizard\Logic\Migration\Wizard::EVENT_NAME;
    }

    // }}} </editor-fold>
}
