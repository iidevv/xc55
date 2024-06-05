<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\Core\Task;

/**
 * Scheduled task that sends automatic cart reminders.
 */
class GenerateSitemap extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Generate sitemap');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        $generator = \CDev\XMLSitemap\Logic\SitemapGenerator::getInstance();
        $generator->generate();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return static::INT_1_DAY;
    }
}
