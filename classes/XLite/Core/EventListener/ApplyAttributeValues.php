<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventListener;

class ApplyAttributeValues extends \XLite\Core\EventListener\Base\Countable
{
    public const CHUNK_LENGTH = 100;

    /**
     * Generator
     *
     * @var \XLite\Logic\ApplyAttributeValues\Generator
     */
    protected $generator;

    /**
     * Time mark
     *
     * @var integer
     */
    protected $timeMark = 0;

    /**
     * Service time
     *
     * @var integer
     */
    protected $serviceTime = 0;

    /**
     * Counter
     *
     * @var integer
     */
    protected $counter = self::CHUNK_LENGTH;

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return 'applyAttributeValues';
    }

    /**
     * Process item
     *
     * @param mixed $item Item
     *
     * @return boolean
     */
    protected function processItem($item)
    {
        $this->serviceTime += (microtime(true) - $this->timeMark);

        $result = $item->run();

        $this->timeMark = microtime(true);

        if (!$this->getItems()->valid()) {
            $result = false;
            foreach ($this->getItems()->getErrors() as $error) {
                $this->errors[] = $error['title'];
            }
        }

        return $result;
    }

    /**
     * Check step valid state
     *
     * @return boolean
     */
    protected function isStepValid()
    {
        return parent::isStepValid()
            && $this->getItems()->valid();
    }

    /**
     * Get items list length
     *
     * @return integer
     */
    protected function getLength()
    {
        return $this->getItems()->count();
    }

    /**
     * Get items
     *
     * @return \XLite\Logic\ApplyAttributeValues\Generator
     */
    protected function getItems()
    {
        if (!isset($this->generator)) {
            $this->generator = new \XLite\Logic\ApplyAttributeValues\Generator(
                $this->record['options'] ?? []
            );
        }

        return $this->generator;
    }

    /**
     * Initialize step
     *
     * @return void
     */
    protected function initializeStep()
    {
        $this->timeMark = microtime(true);

        set_time_limit(0);
        $this->counter = static::CHUNK_LENGTH;

        parent::initializeStep();
    }

    /**
     * Finish step
     *
     * @return void
     */
    protected function finishStep()
    {
        $generator = $this->getItems();

        $this->serviceTime += (microtime(true) - $this->timeMark);
        $generator->getOptions()->time += $this->serviceTime;

        $this->record['options'] = $generator->getOptions()->getArrayCopy();

        parent::finishStep();
    }

    /**
     * Finish task
     *
     * @return void
     */
    protected function finishTask()
    {
        $this->record['options'] = $this->getItems()->getOptions()->getArrayCopy();

        parent::finishTask();

        $this->getItems()->finalize();
    }

    /**
     * Writes some data into $this->record['touchData'] after step/task finish.
     */
    protected function compileTouchData()
    {
        $timeLabel = \XLite\Core\Translation::formatTimePeriod($this->getItems()->getTimeRemain());
        $this->record['touchData'] = [];
        if ($timeLabel) {
            $this->record['touchData']['message'] = static::t('About X remaining', ['time' => $timeLabel]);
        }
    }

    /**
     * Check - step is success or not
     *
     * @return boolean
     */
    protected function isStepSuccess()
    {
        return parent::isStepSuccess() && !$this->getItems()->hasErrors();
    }

    /**
     * Check - continue cycle or not
     *
     * @param mixed $item Item
     *
     * @return boolean
     */
    protected function isContinue($item)
    {
        $this->counter--;

        return parent::isContinue($item) && 0 < $this->counter && empty($this->errors);
    }

    /**
     * Fail task
     *
     * @return void
     */
    protected function failTask()
    {
        parent::failTask();

        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState($this->getEventName());
    }
}
