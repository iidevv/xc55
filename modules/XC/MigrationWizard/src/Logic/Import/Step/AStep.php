<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract import step
 * @Extender\Mixin
 */
abstract class AStep extends \XLite\Logic\Import\Step\AStep
{
    /**
     * Get importTickDuration TmpVar name
     *
     * @return string
     */
    protected function getImportTickDurationVarName()
    {
        return $this->getEventName() . 'TickDuration';
    }

    /**
     * Check - step is current or not
     *
     * @return boolean
     */
    public function isCurrentStep()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')
            ->getEventState($this->getEventName());

        return $state
            && !empty($state['options'])
            && isset($state['options']['step'])
            && $state['options']['step'] == $this->index;
    }

    /**
     * Check - step is current or not
     *
     * @return boolean
     */
    public function isFutureStep()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')
            ->getEventState($this->getEventName());

        return $state
            && !empty($state['options'])
            && (!isset($state['options']['step']) || $state['options']['step'] < $this->index);
    }

    /**
     * Check - step is finalized or not
     *
     * @return boolean
     */
    public function isStepFinalized()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')
            ->getEventState($this->getEventName());

        return $state
            && !empty($state['options'])
            && (
                (isset($state['options']['step']) && $state['options']['step'] > $this->index)
                || isset($state['state']) && $state['state'] == \XLite\Core\EventTask::STATE_FINISHED
            );
    }

    /**
     * Get event name
     *
     * @return string
     */
    public function getEventName()
    {
        $importer = $this->importer;

        return $importer::getEventName();
    }
}
