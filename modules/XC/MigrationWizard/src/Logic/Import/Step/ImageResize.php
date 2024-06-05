<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Step;

/**
 * Resize images step
 */
class ImageResize extends \XLite\Logic\Import\Step\ImageResize
{
    /**
     * Get error language label
     *
     * @return array
     */
    public function getErrorLanguageLabel()
    {
        $options = $this->getOptions();

        return static::t(
            'Image resized: X out of Y with errors',
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
            'Image resized: X out of Y',
            [
                'X' => min($options->position + 1, $this->count()),
                'Y' => $this->count(),
            ]
        );
    }
}
