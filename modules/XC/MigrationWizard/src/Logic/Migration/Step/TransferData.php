<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Migration Logic - Transfer Data
 */
class TransferData extends \XC\MigrationWizard\Logic\Migration\Step\AStep
{
    public const FIELD_USE_ENTITY_CACHE   = 'use_entity_cache';
    public const FIELD_ORDERS_START_DATE   = 'orders_start_date';

    protected $useEntityCache = true;

    protected $ordersStartDate = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState(
            \XC\MigrationWizard\Logic\Import\Importer::getEventName()
        );
    }

    /**
     * Save demo mode field value
     */
    public function saveUseEntityCache()
    {
        $request = \XLite\Core\Request::getInstance();

        $this->useEntityCache = false;
        if (isset($request->{static::FIELD_USE_ENTITY_CACHE})) {
            $this->useEntityCache = !empty($request->{static::FIELD_USE_ENTITY_CACHE});
        }
    }

    /**
     * Save orders start date field value
     */
    public function saveOrdersStartDate()
    {
        $request = \XLite\Core\Request::getInstance();

        $this->ordersStartDate = 0;
        if (isset($request->{static::FIELD_ORDERS_START_DATE})) {
            $this->ordersStartDate = \XLite\Core\Converter::parseFromJsFormat($request->{static::FIELD_ORDERS_START_DATE})
                ?: 0;
        }
    }

    /**
     * Return True if should use entity cache
     *
     * @return boolean
     */
    public function isUseEntityCache()
    {
        return $this->useEntityCache;
    }

    /**
     * Return orders start date
     *
     * @return boolean
     */
    public function getOrdersStartDate()
    {
        return $this->ordersStartDate;
    }

    /**
     * Check - import process is finished or not
     *
     * @return boolean
     */
    public function isImportRunning()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && isset($state['state'])
            && in_array($state['state'], [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS])
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getImportCancelFlagVarName());
    }

    /**
     * Check - import process is finished
     *
     * @return boolean
     */
    public function isImportFinished()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state && $state['state'] == \XLite\Core\EventTask::STATE_FINISHED
            && \XLite\Core\Request::getInstance()->completed && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getImportCancelFlagVarName());
    }

    /**
     * Check - export process is finished
     *
     * @return boolean
     */
    public function isImportFailed()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state && $state['state'] == \XLite\Core\EventTask::STATE_ABORTED
            && \XLite\Core\Request::getInstance()->failed && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getImportCancelFlagVarName());
    }

    /**
     * Check if should migrate only few entities
     *
     * @return string
     */
    public static function isDemoModeMigration()
    {
        $result = false;

        if ($transferData = \XLite::getController()->getWizard()->getStep('DetectTransferableData')) {
            $result = $transferData->isDemoMode();
        }

        return $result;
    }

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XC\MigrationWizard\Logic\Import\Importer::getEventName();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getImportCancelFlagVarName()
    {
        return \XC\MigrationWizard\Logic\Import\Importer::getImportCancelFlagVarName();
    }

    /**
     * Return list of transferable data rules
     *
     * @return array(\XC\MigrationWizard\Model\MigrationRule, ...) or false
     */
    public function getSelectedRules()
    {
        if (
            \XLite::getController()->getWizard()->getStep('DetectTransferableData')
            && ($rules = \XLite::getController()->getWizard()->getStep('DetectTransferableData')->getSelectedRules())
        ) {
            return $rules;
        }

        return false;
    }

    public function hasDemoApplicableRules()
    {
        $rules = $this->getSelectedRules();

        $demoApplicableRules = [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Products',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Users',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Orders'
        ];

        foreach ($demoApplicableRules as $rule) {
            if (in_array($rule, $rules, true)) {
                return true;
            }
        }

        return false;
    }

    public function hasRule($ruleName)
    {
        $rules = $this->getSelectedRules();

        if (in_array($ruleName, $rules, true)) {
            return true;
        }

        return false;
    }

    public function hasMigrationCache()
    {
        // TODO change to something like 'return $symfony->hasCache && $hasPrevMigrationData';
        return true;
    }

    /**
     * Get rule logic name
     *
     * @param string $logic
     *
     * @return string
     */
    public function getRuleLogicName($logic)
    {
        $rule = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRule')
            ->findOneBy(
                [
                    'logic' => $logic,
                ]
            );

        $rule->editLanguage = \XLite\Core\Session::getInstance()->getLanguage()->getCode();

        return $rule->getName();
    }

    /**
     * Get rule logic counts
     *
     * @param string $logic
     *
     * @return integer
     */
    public function getRuleLogicCounts($logic, $countAll = false)
    {
        static $importer = null;

        if (empty($importer)) {
            $importer = new \XC\MigrationWizard\Logic\Import\Importer();
        }

        $processor = new $logic($importer);

        if (
            in_array(
                $logic,
                [
                'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment',
                'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping',
                'XC\MigrationWizard\Logic\Import\Processor\XCart\Content',
                ],
                true
            )
        ) {
            $countAll = true; // Use recursive counter
        }

        if ($processor) {
            if (
                in_array(
                    $logic,
                    [
                        'XC\MigrationWizard\Logic\Import\Processor\XCart\ProductImages',
                        'XC\MigrationWizard\Logic\Import\Processor\XCart\CategoryImages',
                    ]
                )
            ) {
                return $processor::getImagesCount();
            } else {
                $counts = array_filter($processor->getRunDisplayCounts(\XC\MigrationWizard\Logic\Import\Recordset::FOR_DISPLAY_COUNT));

                if ($countAll && ($extras = $processor->getAllProcessorsWithData()) && !empty($extras)) {
                    foreach ($extras as $extra) {
                        $count = $this->getRuleLogicCounts($extra, $countAll);
                        if ($count > 0) {
                            $counts[$extra] = $count;
                        }
                    }
                }

                $subprocessors = $processor::getSubProcessorsWithData();

                return $processor::hasHeadingRow()
                    ? array_sum($counts) - count($counts) + count($subprocessors)
                    : array_sum($counts);
            }
        }

        return 0;
    }

    /**
     * Return step line title
     *
     * @return string
     */
    public static function getLineTitle()
    {
        return 'Step-Transfer';
    }
}
