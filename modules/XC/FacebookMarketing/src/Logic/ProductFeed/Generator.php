<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Logic\ProductFeed;

/**
 * Generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    /**
     * Flag: is export in progress (true) or no (false)
     *
     * @var boolean
     */
    protected static $inProgress = false;

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();

        \XC\FacebookMarketing\Core\ProductFeedDataWriter::getInstance()->clearGenerationDir();
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        parent::finalize();

        \XC\FacebookMarketing\Core\ProductFeedDataWriter::getInstance()->moveToDataDir();
    }

    /**
     * Set inProgress flag value
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setInProgress($value)
    {
        static::$inProgress = $value;
    }

    // {{{ Steps

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            'XC\FacebookMarketing\Logic\ProductFeed\Step\AllProducts'
        ];
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'facebookMarketingProductFeed';
    }

    // }}}
}
