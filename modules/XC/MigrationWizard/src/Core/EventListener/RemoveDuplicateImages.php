<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Core\EventListener;

/**
 * RemoveDuplicateImages
 */
class RemoveDuplicateImages extends \XLite\Core\EventListener\ImageResize
{
    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XC\MigrationWizard\Logic\RemoveDuplicateImages\Generator::getEventName();
    }

    /**
     * Get items
     *
     * @return array
     */
    protected function getItems()
    {
        if (!isset($this->generator)) {
            $this->generator = new \XC\MigrationWizard\Logic\RemoveDuplicateImages\Generator($this->record['options'] ?? []);
        }

        return $this->generator;
    }
}
