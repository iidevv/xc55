<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View;

use XLite\Core\Converter;
use DateTime;

/**
 * "Clear e-mail statistics before" widget.
 */
class ClearCartEmailStatistics extends \XLite\View\AView
{
    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/clear_stats.twig';
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                $this->getDir() . '/clear_stats.css',
            ]
        );
    }

    /**
     * Via this method the widget registers the JS files which it uses.
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                $this->getDir() . '/clear_stats.js',
            ]
        );
    }

    /**
     * Returns path to the directory with widget resource files.
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/AbandonedCartReminder/email_stats';
    }

    /**
     * Returns the default value for the "Select date" field.
     *
     * @return int
     */
    protected function getDefaultClearDate()
    {
        $time = new DateTime('today -1 year', Converter::getTimeZone());

        return $time->getTimestamp();
    }
}
