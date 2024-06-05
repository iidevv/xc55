<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core\EventListener;

use XLite\Core\EventListener\Base\Countable;
use XPay\XPaymentsCloud\Logic\ClearCCData\Generator;

class ClearCCData extends Countable
{
    const CHUNK_LENGTH = 25;

    /**
     * Generator
     *
     * @var Generator
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
     * @inheritdoc
     */
    protected function getEventName()
    {
        return 'clearCreditCardsData';
    }

    /**
     * @inheritdoc
     */
    protected function getLength()
    {
        return $this->getItems()->count();
    }

    /**
     * @inheritdoc
     */
    protected function getItems()
    {
        if (!isset($this->generator)) {
            $this->generator = new Generator(
                isset($this->record['options']) ? $this->record['options'] : []
            );
        }

        return $this->generator;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function isStepValid()
    {
        return parent::isStepValid()
            && $this->getItems()->valid();
    }

    /**
     * @inheritdoc
     */
    protected function initializeStep()
    {
        $this->timeMark = microtime(true);

        set_time_limit(0);
        $this->counter = static::CHUNK_LENGTH;

        parent::initializeStep();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function finishTask()
    {
        $this->record['options'] = $this->getItems()->getOptions()->getArrayCopy();

        parent::finishTask();

        $this->getItems()->finalize();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function isStepSuccess()
    {
        return parent::isStepSuccess() && !$this->getItems()->hasErrors();
    }

    /**
     * @inheritdoc
     */
    protected function isContinue($item)
    {
        $this->counter--;

        return parent::isContinue($item) && 0 < $this->counter && empty($this->errors);
    }

    /**
     * @inheritdoc
     */
    protected function failTask()
    {
        parent::failTask();

        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState($this->getEventName());
    }



}
