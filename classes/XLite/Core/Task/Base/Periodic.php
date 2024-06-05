<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task\Base;

/**
 * Abstract periodic task
 */
abstract class Periodic extends \XLite\Core\Task\ATask
{
    public const INT_1_MIN     = 60;
    public const INT_5_MIN     = 300;
    public const INT_10_MIN    = 600;
    public const INT_15_MIN    = 900;
    public const INT_30_MIN    = 1800;
    public const INT_1_HOUR    = 3600;
    public const INT_2_HOURS   = 7200;
    public const INT_4_HOURS   = 14400;
    public const INT_6_HOURS   = 21600;
    public const INT_12_HOURS  = 43200;
    public const INT_1_DAY     = 86400;
    public const INT_2_DAYS    = 172800;
    public const INT_5_DAYS    = 432000;
    public const INT_1_WEEK    = 604800;

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    abstract protected function getPeriod();

    /**
     * Mark task as running
     *
     * @return void
     */
    protected function markAsRunning()
    {
        \XLite\Core\Lock\FileLock::getInstance()->setRunning(
            $this->getLockKey(),
            $this->getPeriod() / 4
        );
    }

    /**
     * Finalize step
     *
     * @return void
     */
    protected function finalizeStep()
    {
        parent::finalizeStep();

        $this->release();

        $this->model = \XLite\Core\Database::getEM()->merge($this->model);
        $this->model->setTriggerTime($this->startTime + $this->getPeriod());
    }
}
