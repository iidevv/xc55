<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Button;

/**
 * Generate Selected Feeds button.
 */
class GenerateFeeds extends \XLite\View\Button\Submit
{
    /**
     * Get the button label.
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Generate selected feeds';
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . $this->getGenerateFeedsButtonClass();
    }

    /**
     * Defines CSS classes specific for the Generate Feeds button.
     *
     * @return string
     */
    protected function getGenerateFeedsButtonClass()
    {
        return ' more-action regular-main-button';
    }
}
