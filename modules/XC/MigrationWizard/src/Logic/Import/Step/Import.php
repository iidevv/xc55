<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Step;

/**
 * Import step
 */
class Import extends \XLite\Logic\Import\Step\Import
{
    protected function calculateEstimatedTime()
    {
        $migrationTime = \XLite\Core\TmpVars::getInstance()->{\XC\MigrationWizard\Logic\Migration\Wizard::MIGRATION_TIME_KEY};

        $options = $this->getOptions();
        $recordsProcessed = min($options->position + 1, $options->rowsCount);
        $recordsCount = $options->rowsCount;

        $estimatedTime = ceil(($migrationTime * 1.2 / $recordsProcessed) * ($recordsCount - $recordsProcessed));

        return $estimatedTime;
    }

    protected function getEstimatedTimeString()
    {
        $estimatedTime = $this->calculateEstimatedTime();

        if ($estimatedTime > 2 * 60 * 60) {
            $result = round($estimatedTime / (60 * 60)) * (60 * 60);
        } elseif ($estimatedTime > 60 * 60) {
            $result = round($estimatedTime / (10 * 60)) * (10 * 60);
        } else {
            $result = round($estimatedTime / 60) * 60;
        }

        return \XLite\Core\Translation::formatTimePeriod($result);
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        if (!$this->getProcessor()->isMigrationProcessor()) {
            return parent::getNote();
        }

        return static::t('Migrating data');
    }

    /**
     * Get normal language label
     *
     * @return string
     */
    public function getNormalLanguageLabel()
    {
        if (!$this->getProcessor()->isMigrationProcessor()) {
            return parent::getNormalLanguageLabel();
        }

        $options = $this->getOptions();

        $label = static::t(
            'Records processed: X out of Y',
            [
                'X' => min($options->position + 1, $options->rowsCount),
                'Y' => $options->rowsCount,
            ]
        ) . ($this->getProcessor()->getProcessorMigratingTitle()
                ? ' (' . $this->getProcessor()->getProcessorMigratingTitle() . '...)'
                : ''
            );

        if ($options->rowsCount > 1000) {
            if (min($options->position + 1, $options->rowsCount) > 100) {
                $estimateTime = $this->getEstimatedTimeString();
                $label .= '<br>' . static::t('About X remaining', ['time' => $estimateTime]);
            }
        }

        return $label;
    }

    /**
     * Get error language label
     *
     * @return string
     */
    public function getErrorLanguageLabel()
    {
        if (!$this->getProcessor()->isMigrationProcessor()) {
            return parent::getErrorLanguageLabel();
        }

        $options = $this->getOptions();

        $label = static::t(
            'Records processed: X out of Y',
            [
                    'X' => min($options->position + 1, $options->rowsCount),
                    'Y' => $options->rowsCount,
                ]
        ) . ($this->getProcessor()->getProcessorMigratingTitle()
                ? ' (' . $this->getProcessor()->getProcessorMigratingTitle() . '...)'
                : ''
            );

        if ($options->rowsCount > 1000) {
            if (min($options->position + 1, $options->rowsCount) > 100) {
                $estimateTime = $this->getEstimatedTimeString();
                $label .= '<br>' . static::t('About X remaining', ['time' => $estimateTime]);
            }
        }

        return $label;
    }
}
