<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\WidgetCacheManager;
use XLite\View\AResourcesContainer;

/**
 * Performance
 */
class CssJsPerformance extends \XLite\Controller\Admin\Settings
{
    /**
     * Page
     *
     * @var string
     */
    public $page = self::PERFORMANCE_PAGE;

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::PERFORMANCE_PAGE] = static::t('Performance Settings');

        return $list;
    }

    /**
     * Clean aggregation cache directory
     *
     * @return void
     */
    public function doActionCleanAggregationCache()
    {
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_CACHE_RESOURCES);

        \Less_Cache::SetCacheDir(LC_DIR_DATACACHE);
        \Less_Cache::CleanCache();
        AResourcesContainer::refreshCacheTimestamp();

        \XLite\Core\TopMessage::addInfo('Aggregation cache has been cleaned');
    }

    /**
     * Clean view cache
     *
     * @return void
     */
    public function doActionCleanViewCache()
    {
        /** @var WidgetCacheManager $widgetCache */
        $widgetCacheManager = \XCart\Container::getContainer()->get(WidgetCacheManager::class);

        if ($widgetCacheManager->deleteAll()) {
            \XLite\Core\TopMessage::addInfo('Widgets cache has been cleaned');
        } else {
            \XLite\Core\TopMessage::addWarning('Widgets cache has not been cleaned completely');
        }
    }

    /**
     * Perform some actions before redirect
     *
     * FIXME: check. Action should not be an optional param
     *
     * @param string|null $action Performed action OPTIONAL
     *
     * @return void
     */
    protected function actionPostprocess($action = null)
    {
        parent::actionPostprocess($action);

        $this->setReturnURL(
            $this->buildURL('css_js_performance')
        );
    }
}
