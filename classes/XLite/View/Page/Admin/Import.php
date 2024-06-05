<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page\Admin;

class Import extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'import/style.less';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'import/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/page.twig';
    }

    /**
     * Get inner widget class name
     *
     * @return string
     */
    protected function getInnerWidget()
    {
        $result = 'XLite\View\Import\Begin';

        if ($this->isImportNotFinished()) {
            $result = 'XLite\View\Import\Progress';
        } elseif ($this->isImportFailed()) {
            $result = 'XLite\View\Import\Failed';
        } elseif ($this->isImportFinished()) {
            $result = 'XLite\View\Import\Completed';
        }

        return $result;
    }

    /**
     * Check - import process is not-finished or not
     *
     * @return bool
     */
    protected function isImportNotFinished()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\TmpVar');
        $state = $repo->getEventState($this->getEventName());

        return $state
            && isset($state['state'])
            && in_array($state['state'], [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS])
            && !$repo->getVar($this->getImportUserBreakFlagVarName())
            && !$repo->getVar($this->getImportCancelFlagVarName());
    }

    /**
     * Check - import process is finished
     *
     * @return bool
     */
    protected function isImportFinished()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\TmpVar');
        $state = $repo->getEventState($this->getEventName());

        return \XLite\Core\Request::getInstance()->failed
            || (
                $state
                && isset($state['state'])
                && $state['state'] == \XLite\Core\EventTask::STATE_FINISHED
                && \XLite\Core\Request::getInstance()->completed
                && (!$this->getImporter() || !$this->getImporter()->isNextStepAllowed())
                && !$repo->getVar($this->getImportCancelFlagVarName())
            );
    }

    /**
     * Check - import process is finished
     *
     * @return bool
     */
    protected function isImportFailed()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\TmpVar');
        $event = $repo->getEventState($this->getEventName());

        $result = $repo->getVar($this->getImportUserBreakFlagVarName());

        if (!$result) {
            $result = $event
                && isset($event['state'])
                && !$repo->getVar($this->getImportCancelFlagVarName())
                && \XLite\Core\Request::getInstance()->completed
                && (
                    (
                        $event['state'] == \XLite\Core\EventTask::STATE_FINISHED
                        && !$event['options']['step']
                    )
                    || $event['state'] == \XLite\Core\EventTask::STATE_ABORTED
                )
                && (
                    \XLite\Logic\Import\Importer::hasErrors()
                    || \XLite\Logic\Import\Importer::hasWarnings()
                );
        }

        if (!$result) {
            $result = \XLite\Core\Request::getInstance()->failed
                && $repo->getVar($this->getImportCancelFlagVarName());
        }

        return $result;
    }

    /**
     * Get import event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\Import\Importer::getEventName();
    }

    /**
     * Get import cancel flag name
     *
     * @return string
     */
    protected function getImportCancelFlagVarName()
    {
        return \XLite\Logic\Import\Importer::getImportCancelFlagVarName();
    }

    /**
     * Get import user break flag name
     *
     * @return string
     */
    protected function getImportUserBreakFlagVarName()
    {
        return \XLite\Logic\Import\Importer::getImportUserBreakFlagVarName();
    }

    /**
     * Get data for import commented data block
     *
     * @return array
     */
    protected function getImportCommentedData()
    {
        return [
            'importTarget' => $this->getImportTarget(),
        ];
    }
}
