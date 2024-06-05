<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Step;

/**
 * Remove duplicate images step
 */
class RemoveDuplicateImages extends \XLite\Logic\Import\Step\AStep
{
    /**
     * Get final note
     *
     * @return string
     */
    public function getFinalNote()
    {
        return static::t('Images removed');
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return static::t('Removing duplicate images...');
    }

    /**
     * Process row
     *
     * @return boolean
     */
    public function process()
    {
        $current = $this->getRemoveDuplicateImagesGenerator()->current();
        $result  = $current ? $current->run() : false;

        if ($result) {
            if (empty($this->getOptions()->commonData['rdiProcessed'])) {
                $this->getOptions()->commonData['rdiProcessed'] = 0;
            }

            $this->getOptions()->commonData['rdiProcessed']++;
        }

        return $result;
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->getOptions()->commonData['rdiCount'])) {
            $this->getOptions()->commonData['rdiCount'] = $this->getRemoveDuplicateImagesGenerator()->count();
        }

        return $this->getOptions()->commonData['rdiCount'];
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

        $this->getRemoveDuplicateImagesGenerator()->seek($position);
    }

    /**
     * Check - allowed step or not
     *
     * @return boolean
     */
    public function isAllowed()
    {
        return parent::isAllowed() && isset($this->getOptions()->commonData['needRemoveDuplicateImages']) && $this->getOptions()->commonData['needRemoveDuplicateImages']
            && $this->count() > 0;
    }

    /**
     * Get error language label
     *
     * @return array
     */
    public function getErrorLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Removed duplicate images: X out of Y with errors',
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
     * @return array
     */
    public function getNormalLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Removed duplicate images: X out of Y',
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

        $this->getRemoveDuplicateImagesGenerator()->finalize();
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        $list = parent::getMessages();

        if (!empty($this->getOptions()->commonData['rdiProcessed'])) {
            $list[] = [
                'text' => static::t('Removed duplicate images: {{count}}', ['count' => $this->getOptions()->commonData['rdiProcessed']]),
            ];
        }

        return $list;
    }

    /**
     * Get image resize generator
     *
     * @return \XLite\Logic\ImageResize\Generator
     */
    protected function getRemoveDuplicateImagesGenerator()
    {
        if (!isset($this->removeDuplicateImagesGenerator)) {
            $this->removeDuplicateImagesGenerator = new \XC\MigrationWizard\Logic\RemoveDuplicateImages\Generator();
        }

        return $this->removeDuplicateImagesGenerator;
    }
}
