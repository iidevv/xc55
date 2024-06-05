<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Import;

/**
 * Titles section
 */
class Titles extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'import/parts/titles.twig';
    }

    /**
     * Return titles
     *
     * @return array
     */
    protected function getTitles()
    {
        $result = [];

        foreach ($this->getImporter()->getSteps() as $i => $step) {
            if ($step->isFutureStep() || $step->isAllowed()) {
                $result[$i] = [
                    'text'    => $step->getFinalNote(),
                    'current' => $step->getNote(),
                ];
            }
        }

        return $result;
    }

    /**
     * Return current titles
     *
     * @return array
     */
    protected function getCurrentTitles()
    {
        $result = $this->getTitles();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

        $step = $state && $state['state'] == \XLite\Core\EventTask::STATE_FINISHED
            ? 9999
            : $this->getCurrentStep();

        foreach ($result as $k => $v) {
            if ($k == $step) {
                $result[$k]['class'] = $step == $k ? '' : 'completed';
                $result[$k]['text'] = $v[$step == $k ? 'current' : 'text'];
            } else {
                unset($result[$k]);
            }
        }

        return $result;
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible();

        if ($result) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());

            $result = false;

            if ($state && $state['state'] == \XLite\Core\EventTask::STATE_FINISHED) {
                $data = $state['options']['columnsMetaData'];

                if ($data) {
                    foreach (\XLite\Logic\Import\Importer::getProcessorList() as $processor) {
                        $addCount = $data[$processor]['addCount'] ?? 0;
                        $updateCount = $data[$processor]['updateCount'] ?? 0;

                        if (isset($data[$processor]) && 0 < $addCount + $updateCount) {
                            $result = true;
                            break;
                        }
                    }
                }
            } elseif ($state) {
                $result = true;
            }
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
}
