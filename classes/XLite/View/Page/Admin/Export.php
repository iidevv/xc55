<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page\Admin;

class Export extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'export/style.less';

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

        $list[] = 'export/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'export/page.twig';
    }

    /**
     * Get inner widget class name
     *
     * @return string
     */
    protected function getInnerWidget()
    {
        $result = 'XLite\View\Export\Begin';

        if ($this->isExportNotFinished()) {
            $result = 'XLite\View\Export\Progress';
        } elseif ($this->isExportFinished()) {
            \XLite\Core\Request::getInstance()->page = 'last';
            $result = 'XLite\View\Export\Begin';
        } elseif ($this->isExportFailed()) {
            $result = 'XLite\View\Export\Failed';
        }

        return $this->isExportLocked() ? 'XLite\View\Export\Begin' : $result;
    }

    /**
     * Get export state
     *
     * @return bool
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return bool
     */
    protected function isExportNotFinished()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && in_array($state['state'], [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS])
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName());
    }

    /**
     * Check - export process is finished
     *
     * @return bool
     */
    protected function isExportFinished()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && $state['state'] == \XLite\Core\EventTask::STATE_FINISHED
            && \XLite\Core\Request::getInstance()->completed
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName());
    }

    /**
     * Check - export process is finished
     *
     * @return bool
     */
    protected function isExportFailed()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        return $state
            && $state['state'] == \XLite\Core\EventTask::STATE_ABORTED
            && \XLite\Core\Request::getInstance()->failed
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getExportCancelFlagVarName())
            && $this->getGenerator()
            && $this->getGenerator()->hasErrors();
    }

    /**
     * Get export event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\Export\Generator::getEventName();
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getExportCancelFlagVarName()
    {
        return \XLite\Logic\Export\Generator::getCancelFlagVarName();
    }
}
