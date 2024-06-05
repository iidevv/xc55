<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Controller\Admin;

/**
 * Migration Wizard controller
 */
class MigrationWizard extends \XLite\Controller\Admin\AAdmin
{
    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Core\Translation::t('Migration wizard');
    }

    /**
     * Get migration wizard instance
     *
     * @return \XC\MigrationWizard\Logic\Migration\Wizard
     */
    public function getWizard()
    {
        return \XC\MigrationWizard\Logic\Migration\Wizard::getInstance();
    }

    /**
     * Get importer
     *
     * @return \XC\MigrationWizard\Logic\Import\Importer
     */
    public function getImporter()
    {
        if (!isset($this->importer)) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

            $this->importer = ($state && isset($state['options']))
                ? new \XC\MigrationWizard\Logic\Import\Importer($state['options'])
                : false;
        }

        return $this->importer;
    }

    /**
     * Return current step
     *
     * @return integer
     */
    public function getCurrentStep()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return ($state && isset($state['options']) && isset($state['options']['step']))
                ? $state['options']['step'] : 0;
    }

    /**
     * Get import target
     *
     * @return string
     */
    public function getImportTarget()
    {
        return $this->getImporter() ? $this->getImporter()->getOptions()->target
                : 'migration_wizard';
    }

    /**
     * Get event name
     *
     * @return array
     */
    public function getTargetController()
    {
        return ['importTarget' => $this->getImportTarget()];
    }

    /**
     * Get event name
     *
     * @return string
     */
    public function getEventName()
    {
        return \XC\MigrationWizard\Logic\Import\Importer::getEventName();
    }

    // }}} </editor-fold>

    // {{{ Access <editor-fold desc="Access" defaultstate="collapsed">

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage import');
    }

    // }}} </editor-fold>

    // {{{ Magic <editor-fold desc="Magic" defaultstate="collapsed">

    /**
     * Use wizard context for other methods
     * @todo: avoid magic
     *
     * @param string $method Method name
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        return call_user_func_array([$this->getWizard(), $method], $args);
    }

    // }}} </editor-fold>

    // {{{ Wizard actions <editor-fold desc="Wizard actions" defaultstate="collapsed">

    /**
     * Start action
     *
     * @return void
     */
    protected function doActionStepBack()
    {
        $this->getWizard()->doStepBack();
    }

    /**
     * Start action
     *
     * @return void
     */
    protected function doActionStart()
    {
        $this->getWizard()->doStart();
    }

    /**
     * Connect action
     *
     * @return void
     */
    protected function doActionConnect()
    {
        $this->getWizard()->doConnect();
    }

    /**
     * Check requirements action
     *
     * @return void
     */
    protected function doActionCheckRequirements()
    {
        $this->getWizard()->doCheckRequirements();
    }

    /**
     * Detect transferable data action
     *
     * @return void
     */
    protected function doActionDetectTransferableData()
    {
        $this->getWizard()->doDetectTransferableData();
    }

    /**
     * Missing modules action
     *
     * @return void
     */
    protected function doActionMissingModules()
    {
        $this->getWizard()->doMissingModules();
    }

    /**
     * Transfer data action
     *
     * @return void
     */
    protected function doActionTransferData()
    {
        $this->getWizard()->doTransferData();
    }

    /**
     * Restart action
     *
     * @return void
     */
    protected function doActionRestart()
    {
        $this->getWizard()->doRestart();
    }

    // }}} </editor-fold>

    // {{{ Import actions <editor-fold desc="Import actions" defaultstate="collapsed">

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (
            $this->getImporter()
        ) {
            if (\XLite\Core\Request::getInstance()->_action == 'restart') {
                $this->doActionRestart();
            } elseif ($this->getImporter()->isNextStepAllowed()) {
                $this->getImporter()->switchToNextStep();
            } elseif (\XLite\Core\Request::getInstance()->completed) {
                $this->getWizard()->doComplete();
                $this->setReturnURL($this->buildURL($this->getImportTarget()));
            }
        }
    }

    /**
     * Proceed
     *
     * @return void
     */
    protected function doActionProceed()
    {
        if ($this->getImporter()) {
            if (!empty($this->getImporter()->getOptions()->commonData['finalize'])) {
                $this->getImporter()->getOptions()->commonData['finalize'] = null;
            }

            $this->getImporter()->getOptions()->warningsAccepted = true;

            if ($this->getImporter()->isNextStepAllowed()) {
                $this->getImporter()->switchToNextStep(['warningsAccepted' => true]);
                \XLite\Core\Database::getRepo('XLite\Model\ImportLog')->deleteByType(\XLite\Model\ImportLog::TYPE_WARNING);
            }
        }
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionCancel()
    {
        \XC\MigrationWizard\Logic\Import\Importer::cancel();
        \XLite\Core\TopMessage::addWarning('Migration has been cancelled.');
    }

    /**
     * Reset
     *
     * @return void
     */
    protected function doActionReset()
    {
        \XC\MigrationWizard\Logic\Import\Importer::cancel();

        $this->setReturnURL($this->buildURL($this->getImportTarget()));
    }

    // }}} </editor-fold>

    // {{{ Modules actions <editor-fold desc="Modules actions" defaultstate="collapsed">

    /**
     * Install modules
     */
    protected function doActionInstallModules()
    {
        if (\XLite\Core\Request::getInstance()->moduleIds) {
            $moduleIds = $states = [];
            foreach (\XLite\Core\Request::getInstance()->moduleIds as $moduleId) {
                $moduleIds[] = $moduleId;
                $states[]    = [
                    'id'      => $moduleId,
                    'enable'  => true,
                    'install' => true,
                ];
            }
            $installStr = 'mainInstall=' . $moduleIds[0];
            unset($moduleIds[0]);
            if ($moduleIds) {
                $installStr .= '&installTip[]=' . implode('&installTip[]=', $moduleIds);
            }
            // open previous step after modules install
            $this->getWizard()->registerStep(new \XC\MigrationWizard\Logic\Migration\Step\DetectTransferableData());

            $this->setReturnURL(\XLite::getInstance()->getShopURL(
                'service.php/market_module_installer?target=market_install_module&' . $installStr,
                null,
                ['returnUrl' => $this->buildFullURL('migration_wizard'), 'modules' => json_encode($states)]
            ));

            \XLite\Core\Session::getInstance()->migration_wizard_install = true;

            $this->doRedirect();
        }
    }

    /**
     * Enable modules
     *
     * @return void
     */
    protected function doActionEnableModules()
    {
        if (\XLite\Core\Request::getInstance()->switch) {
            $this->setReturnURL($this->buildURL(
                'addons_list_installed',
                'switch',
                [
                    'switch' => \XLite\Core\Request::getInstance()->switch,
                ]
            ));

            \XLite\Core\Session::getInstance()->migration_wizard_enable = true;

            $this->doRedirect();
        }
    }

    /**
     * Disable modules
     *
     * @return void
     */
    protected function doActionDisableModules()
    {
        if (\XLite\Core\Request::getInstance()->switch) {
            $this->setReturnURL($this->buildURL(
                'addons_list_installed',
                'switch',
                [
                    'switch' => \XLite\Core\Request::getInstance()->switch,
                ]
            ));

            \XLite\Core\Session::getInstance()->migration_wizard_enable = true;

            $this->doRedirect();
        }
    }

    // }}} </editor-fold>
}
