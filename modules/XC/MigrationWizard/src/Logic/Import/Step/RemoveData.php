<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Step;

/**
 * Remove duplicate images step
 */
class RemoveData extends \XLite\Logic\Import\Step\AStep
{
    /**
     * @var \XLite\Logic\RemoveData\Generator
     */
    protected $removeDataGenerator;

    /**
     * Get final note
     *
     * @return string
     */
    public function getFinalNote()
    {
        return static::t('Data removed');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Removing data...');
    }

    /**
     * Process row
     *
     * @return boolean
     */
    public function process()
    {
        $result = $this->getRemoveDataGenerator()->current()->run();

        if ($result) {
            if (empty($this->getOptions()->commonData['removeDataProcessed'])) {
                $this->getOptions()->commonData['removeDataProcessed'] = 0;
            }

            $this->getOptions()->commonData['removeDataProcessed']++;
        }

        return $result;
    }

    /**
     * @return integer
     */
    public function count()
    {
        if (!isset($this->getOptions()->commonData['removeDataCount'])) {
            $this->getOptions()->commonData['removeDataCount'] = $this->getRemoveDataGenerator()->count();
        }

        return $this->getOptions()->commonData['removeDataCount'];
    }

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        parent::seek($position);

        $this->getRemoveDataGenerator()->seek($position);
    }

    /**
     * Check - allowed step or not
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return parent::isAllowed()
            && $this->count() > 0;
    }

    /**
     * Get error language label
     *
     * @return string
     */
    public function getErrorLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Removed data: X out of Y with errors',
            [
                'X'      => min($options->position + 1, $this->count()),
                'Y'      => $this->count(),
                'errors' => $options->errorsCount,
                'warns'  => $options->warningsCount,
            ]
        );
    }

    /**
     * Get normal language label
     *
     * @return string
     */
    public function getNormalLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Removed data: X out of Y',
            [
                'X' => min($options->position + 1, $this->count()),
                'Y' => $this->count(),
            ]
        );
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        $this->getRemoveDataGenerator()->finalize();
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        $list = parent::getMessages();

        if (!empty($this->getOptions()->commonData['removeDataProcessed'])) {
            $list[] = [
                'text' => static::t(
                    'Removed data: {{count}}',
                    ['count' => $this->getOptions()->commonData['removeDataProcessed']]
                ),
            ];
        }

        return $list;
    }

    /**
     * Get image resize generator
     *
     * @return \XLite\Logic\RemoveData\Generator
     */
    protected function getRemoveDataGenerator()
    {
        if ($this->removeDataGenerator === null) {
            $options = [
                'steps' => $this->getRequestedSteps(),
            ];

            $this->removeDataGenerator = new \XLite\Logic\RemoveData\Generator($options);
        }

        return $this->removeDataGenerator;
    }

    /**
     * @return array
     */
    protected function getRequestedSteps()
    {
        $dataRemovers = [];
        foreach ($this->getImporter()->getProcessors() as $processor) {
            if (method_exists($processor, 'defineDataRemovers')) {
                $dataRemovers += $processor->defineDataRemovers();
            }
        }

        return array_unique($dataRemovers);
    }
}
