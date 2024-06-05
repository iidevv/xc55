<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core;

use XLite\Base\Singleton;
use XLite\Core\Database;
use XLite\Core\EventTask;
use XLite\Model\AEntity;
use XLite\Model\TmpVar;
use XPay\XPaymentsCloud\Logic\ClearCCData\Generator;

class ClearCCData extends Singleton
{
    /**
     * Check - clearing process is not-finished or not
     *
     * @return boolean
     */
    public function isClearCCDataNotFinished()
    {
        $eventName = Generator::getEventName();
        $state = Database::getRepo(TmpVar::class)->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                array(EventTask::STATE_STANDBY, EventTask::STATE_IN_PROGRESS)
            )
            && !Database::getRepo(TmpVar::class)->getVar($this->getClearCCDataCancelFlagVarName());
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getClearCCDataCancelFlagVarName()
    {
        return Generator::getCancelFlagVarName();
    }

    /**
     * Clear credit cards data
     *
     * @param AEntity $entity Entity
     *
     * @return void
     */
    public function clearCCData(AEntity $entity)
    {
        $patterns = [
            '/(\d{6})(\*{6})(\d{4})/',
            '/(\d{2})\/(\d{4})/',
        ];
        $replacements = [
            '******$2$3',
            '**/****',
        ];
        $entity->setValue(preg_replace($patterns, $replacements, $entity->getValue()));
        Database::getEM()->flush();
    }

}
