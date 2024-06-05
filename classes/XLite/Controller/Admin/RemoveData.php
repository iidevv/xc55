<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Remove data controller
 */
class RemoveData extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Remove data');
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return boolean
     */
    public function isRemoveDataNotFinished()
    {
        $eventName = \XLite\Logic\RemoveData\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
        && in_array(
            $state['state'],
            [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS]
        )
        && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getRemoveDataCancelFlagVarName());
    }

    /**
     * Export action
     *
     * @return void
     */
    protected function doActionRemoveData()
    {
        \XLite\Logic\RemoveData\Generator::run($this->assembleRemoveDataOptions());
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleRemoveDataOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        $steps = [];

        foreach ($request->delete as $step => $delete) {
            if ($delete) {
                $steps[] = ucfirst($step);
            }
        }

        return [
            'include'   => $request->section,
            'steps'     => $steps
        ];
    }

    /**
     * Cancel
     *
     * @return void
     */
    protected function doActionRemoveDataCancel()
    {
        \XLite\Logic\RemoveData\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('Data removal has been stopped.');
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->remove_data_completed) {
            \XLite\Core\TopMessage::addInfo('Data removal has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('remove_data')
            );
        } elseif ($request->remove_data_failed) {
            \XLite\Core\TopMessage::addError('Data removal has been stopped.');

            $this->setReturnURL(
                $this->buildURL('remove_data')
            );
        }
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getRemoveDataCancelFlagVarName()
    {
        return \XLite\Logic\RemoveData\Generator::getCancelFlagVarName();
    }
}
