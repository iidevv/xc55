<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration;

/**
 * Migration - Wizard
 */
class Wizard extends \XLite\Base\Singleton
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    /**
     * Name of TmpVar
     */
    public const CELL_NAME = 'migrationWizard';

    /**
     * Name of Event
     */
    public const EVENT_NAME = 'migration';

    /**
     * Keep step flag
     */
    public const KEEP_STEP = 'keep';

    /**
     * Replace step flag
     */
    public const REPLACE_STEP = 'replace';

    /**
     * Name of session cell counting migration errors
     */
    public const ERRORS_CELL_NAME = 'migrationWizardErrors';

    public const MIGRATION_TIME_KEY = 'mig_wizard_migration_time';

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    /**
     * Default migration options
     *
     * @var array
     */
    public static $defaultMigrationOptions = [
        'ignoreFileChecking' => true,
        'warningsAccepted' => true,
    ];

    protected static $stepsList = [
        'Start',
        'Connect',
        'CheckRequirements',
        'DetectTransferableData',
        'MissingModules',
        'TransferData',
        'Complete',
    ];

    /**
     * Migration steps
     *
     * @var array
     */
    protected $steps = [];

    /**
     * Migration index
     *
     * @var string
     */
    protected $index = '';

    // }}} </editor-fold>

    // {{{ Magic methods <editor-fold desc="Magic methods" defaultstate="collapsed">

    /**
     * Protected constructor
     */
    protected function __construct()
    {
        parent::__construct();

        if (!empty(\XLite\Core\TmpVars::getInstance()->{self::CELL_NAME})) {
            [
                $this->steps,
                $this->index
            ] = \XLite\Core\TmpVars::getInstance()->{self::CELL_NAME};
        }

        if (empty($this->steps)) {
            $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\Start());
        }
    }

    /**
     * Use step context for not defined methods
     *
     * @param string $method Method name
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        return call_user_func_array([$this->getLastStep(), $method], $args);
    }

    /**
     * Protected destructor
     *
     * @return void
     */
    public function __destruct()
    {
        \XLite\Core\TmpVars::getInstance()->{self::CELL_NAME} = [
            $this->steps,
            $this->index
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get last step
     *
     * @return \XC\MigrationWizard\Logic\Migration\Step\AStep
     */
    public function getLastStep()
    {
        if (
            $this->index === 'MissingModules'
            && !$this->steps['DetectTransferableData']->hasMissingModules()
        ) {
            unset($this->steps[$this->index]);

            if (isset($this->steps['TransferData'])) {
                $this->index = 'TransferData';
            } else {
                $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\TransferData());
            }
        }

        return $this->steps[$this->index];
    }

    /**
     * Get prev step
     *
     * @return \XC\MigrationWizard\Logic\Migration\Step\AStep
     */
    public function getPrevStep()
    {
        $keys = static::$stepsList;

        foreach ($keys as $key) {
            if ($key === $this->index) {
                break;
            }
            if (isset($this->steps[$key])) {
                $prev = $key;
            }
        }

        if ($this->index === 'TransferData') {
            unset($this->steps[$this->index]);
            $prev = 'DetectTransferableData';
        }

        if ($this->index === 'Complete') {
            unset($this->steps[$this->index]);
            $prev = 'TransferData';
        }

        return $this->steps[$prev];
    }

    /**
     * Get step by name
     *
     * @param string $name
     *
     * @return \XC\MigrationWizard\Logic\Migration\Step\AStep|boolean
     */
    public function getStep($name)
    {
        if (!empty($this->steps[$name])) {
            return $this->steps[$name];
        }

        return false;
    }

    /**
     * List of allowed enabled modules
     *
     * @return array
     */
    protected function getAllowedModules()
    {
        $modules = [ 'XC\MigrationWizard' ];

        return array_merge(
            $modules,
            [
                'XC\Trial',
                'XC\CrispWhiteSkin',
                'XC\OrdersImport',
                'XC\FroalaEditor',
                'XC\ThemeTweaker',
                'CDev\SimpleCMS',
                'CDev\TinyMCE',
                'CDev\RuTranslation',
                'XC\Development',
            ]
        );
    }

    /**
     * List of modules (links)
     *
     * @param array $list
     *
     * @return string
     */
    protected function getModuleLinks(array $list)
    {
        $modulesList = [];
        $switchList = [];

        foreach ($list as $item) {
            $moduleUrl = \XLite\Core\Converter::buildURL('addons_list_installed', '', ['substring' => $item->moduleName, 'state' => 'E']);
            $modulesList[] = "<li><a href=\"{$moduleUrl}\" class=\"error\" target=\"_blank\">{$item->moduleName} (by {$item->authorName})</a></li>";
            $switchList[$item->moduleId] = ['old' => 1];
        }

        $disableUrl = \XLite\Core\Converter::buildURL('migration_wizard', 'disable_modules', [
            \XLite::FORM_ID => \XLite::getFormId(),
            'switch' => $switchList,
        ]);

        return '<br><br><ul>' . implode('', $modulesList) . "</ul><br><a href=\"{$disableUrl}\" class=\"error\">" . static::t('Disable modules and continue') . '</a>';
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * Checks current step
     *
     * @return boolean
     */
    public function isCurrentStep($object)
    {
        return $this->getLastStep()->hasView($object);
    }

    /**
     * Check action
     *
     * @return boolean
     */
    public function isAvailableAction($object)
    {
        return $this->getLastStep()->hasAction($object);
    }

    /**
     * Register step in steps list
     *
     * @param \XC\MigrationWizard\Logic\Migration\Step\AStep $step
     * @parem string $replace
     *
     * @return boolean
     */
    public function registerStep(\XC\MigrationWizard\Logic\Migration\Step\AStep $step, $action = self::KEEP_STEP)
    {
        if ($step instanceof \XC\MigrationWizard\Logic\Migration\Step\MissingModules) {
            if (($modules_list = $step::getMissingModulesIds()) && empty($step::getMissingModules())) {
                \XLite\Core\TopMessage::addError(static::t('Cannot find the following addons in the marketplace', ['modules_list' => implode('<br />', $modules_list)]));

                return false;
            }
        }

        $className = $step->getStepName();
        $this->index = $className;

        if (
            !isset($this->steps[$className])
            || $action === self::REPLACE_STEP
        ) {
            $this->steps[$className] = $step;

            return true;
        }

        return false;
    }

    /**
     * Return the list of unallowed modules
     *
     * @return array
     */
    protected function hasUnallowedModules()
    {
        $result = [];

        // todo: allow only xc, cdev, qsl modules
        //$list = $this->getEnabledModules();
        //$allowed = $this->getAllowedModules();
        //
        //foreach ($list as $item) {
        //    if (!in_array($item->author . '\\' . $item->name, $allowed)) {
        //        $result[] = $item;
        //    }
        //}

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Actions <editor-fold desc="Actions" defaultstate="collapsed">

    /**
     * Step back
     *
     * @return boolean
     */
    public function doStepBack()
    {
        $step = $this->getPrevStep();

        if ($step) {
            $className = $step->getStepName();
            $this->index = $className;
        }

        return !empty($step);
    }

    /**
     * Start
     *
     * @return void
     */
    public function doStart()
    {
        if ($list = $this->hasUnallowedModules()) {
            \XLite\Core\TopMessage::addError(
                'Please disable all unallowed modules',
                ['list' => $this->getModuleLinks($list)]
            );
        } else {
            //$generator = new \XLite\Logic\RemoveData\Generator();
            //if ($generator->getStepsCount() > 0) {
            //    \XLite\Core\TopMessage::addWarning('Please remove data before migration');
            //}
            $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\Connect());
        }
    }

    /**
     * Connect
     *
     * @return void
     */
    public function doConnect()
    {
        $this->getLastStep()->saveData();

        if ($this->getLastStep()->isValid()) {
            $checkRequirements = new \XC\MigrationWizard\Logic\Migration\Step\CheckRequirements();
            switch ($checkRequirements->isSupported()) {
                case true:
                    if (
                        $checkRequirements->isDecryptable()
                        || (
                            ($options = \XLite::getInstance()->getOptions('migration_wizard', 'disable_secret_check'))
                            && !empty($options['disable_secret_check'])
                        )
                    ) {
                        $this->registerStep($checkRequirements, self::REPLACE_STEP);
                    }
                    break;
                case false:
                    $this->registerStep($checkRequirements, self::REPLACE_STEP);
                    break;
            }
        }
    }

    /**
     * Check requirements
     *
     * @return void
     */
    public function doCheckRequirements()
    {
        if ($this->getLastStep()->isSupported()) {
            $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\DetectTransferableData());
        }
    }

    /**
     * Detect transferable data
     *
     * @return void
     */
    public function doDetectTransferableData()
    {
        $detectTransferableDataStep = $this->getStep('DetectTransferableData');
        $detectTransferableDataStep->saveDemoMode();
        $detectTransferableDataStep->saveSelectedRules();

        if ($detectTransferableDataStep->isDemoMode()) {
            $detectTransferableDataStep->collectDemoIds();
        }

        if ($detectTransferableDataStep->hasSelectedRules()) {
            if (!$detectTransferableDataStep->hasMissingModules()) {
                $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\TransferData());
            } else {
                $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\MissingModules());
            }
        }
    }

    /**
     * Missing modules
     *
     * @return void
     */
    public function doMissingModules()
    {
        if (!$this->getLastStep()->hasMissingModules()) {
            $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\TransferData());
        }
    }

    /**
     * Transfer data
     *
     * @return void
     */
    public function doTransferData()
    {
        \XLite\Core\Session::getInstance()->{static::ERRORS_CELL_NAME} = 0;

        if ($this->steps['DetectTransferableData']->hasMissingModules()) {
            $this->doStepBack();
            $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\MissingModules());
        } else {
            $this->getLastStep()->saveUseEntityCache();
            $this->getLastStep()->saveOrdersStartDate();

            \XLite\Core\TmpVars::getInstance()->{\XC\MigrationWizard\Logic\Migration\Wizard::MIGRATION_TIME_KEY} = 5;

            $importOptions = array_merge(
                \XC\MigrationWizard\Logic\Import\Importer::assembleImportOptions(),
                \XC\MigrationWizard\Logic\Migration\Wizard::$defaultMigrationOptions
            );
            \XC\MigrationWizard\Logic\Import\Importer::run($importOptions);
        }
    }

    /**
     * Complete
     *
     * @return void
     */
    public function doComplete()
    {
        $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\Complete());
    }

    /**
     * Restart
     *
     * @return void
     */
    public function doRestart()
    {
        //$this->steps = array();

        //$this->index = '';

        $registry = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRegistry');
        $entries = $registry->findAll();

        if ($entries) {
            foreach ($entries as $entry) {
                $registry->delete($entry, false);
            }
            \XLite\Core\Database::getEM()->flush();
        }
        \XC\MigrationWizard\Logic\Import\Processor\AProcessor::clearAllCache();

        $this->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\Start());
    }

    // }}} </editor-fold>

    /**
     * Register transfer data error in errors counter
     */
    public static function registerTransferDataError()
    {
        $cellName = static::ERRORS_CELL_NAME;
        $errorsCount = \XLite\Core\Session::getInstance()->{$cellName};
        \XLite\Core\Session::getInstance()->{$cellName} = $errorsCount + 1;
    }

    /**
     * Check if has errors during data transfer
     *
     * @return bool
     */
    public static function hasTransferDataErrors()
    {
        $cellName = static::ERRORS_CELL_NAME;
        $errorsCount = \XLite\Core\Session::getInstance()->{$cellName};

        return !empty($errorsCount);
    }

    public function isDemoMode()
    {
        $detectTransferableDataStep = $this->getStep('DetectTransferableData');

        return $detectTransferableDataStep && $detectTransferableDataStep->isDemoMode();
    }

    public function getDemoCategoryId()
    {
        $detectTransferableDataStep = $this->getStep('DetectTransferableData');

        return $detectTransferableDataStep ? $detectTransferableDataStep->getDemoCategoryId() : null;
    }
}
