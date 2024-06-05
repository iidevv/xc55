<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XCart\Operation\Service\ViewListRefresh;
use XLite\Core\WidgetCache;

/**
 * Cache management page controller
 */
class CacheManagement extends \XLite\Controller\Admin\Settings
{
    /**
     * Values to use for $page identification
     */
    public const CACHE_MANAGEMENT_PAGE = 'CacheManagement';

    /**
     * Page
     *
     * @var string
     */
    public $page = self::CACHE_MANAGEMENT_PAGE;

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return LC_DEVELOPER_MODE
            ? array_merge(parent::defineFreeFormIdActions(), ['rebuild'])
            : parent::defineFreeFormIdActions();
    }

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::CACHE_MANAGEMENT_PAGE] = static::t('Cache management');

        return $list;
    }

    /**
     * @return \XLite\Logic\QuickData\Generator
     */
    public function getQuickDataGenerator()
    {
        return \XLite\Logic\QuickData\Generator::getInstance();
    }

    /**
     * Check - export process is not-finished or not
     *
     * @return bool
     */
    public function isQuickDataNotFinished()
    {
        $eventName = \XLite\Logic\QuickData\Generator::getEventName();
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($eventName);

        return $state
            && in_array(
                $state['state'],
                [\XLite\Core\EventTask::STATE_STANDBY, \XLite\Core\EventTask::STATE_IN_PROGRESS],
                true
            )
            && !\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getQuickDataCancelFlagVarName());
    }

    /**
     * Perform some actions before redirect
     *
     * @param string $action Performed action
     */
    protected function actionPostprocess($action)
    {
        parent::actionPostprocess($action);

        $this->setReturnURL(
            $this->buildURL('cache_management')
        );
    }

    protected function doActionQuickData()
    {
        \XLite\Logic\QuickData\Generator::run($this->assembleQuickDataOptions());

        \XLite\Core\Database::getRepo('XLite\Model\Category')->correctCategoriesStructure();
    }

    protected function doActionClearCache()
    {
        \XLite\Core\Database::getCacheDriver()->deleteAll();

        /** @var WidgetCache $widgetCache */
        $widgetCache = \XCart\Container::getContainer()->get(WidgetCache::class);
        $widgetCache->deleteAll();
    }

    protected function doActionRebuildViewLists()
    {
        $viewListRefresh = \XCart\Container::getContainer()->get(ViewListRefresh::class);
        ($viewListRefresh)();
    }

    /**
     * Assemble export options
     *
     * @return array
     */
    protected function assembleQuickDataOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        return [
            'include' => $request->section,
        ];
    }

    protected function doActionQuickDataCancel()
    {
        \XLite\Logic\QuickData\Generator::cancel();
        \XLite\Core\TopMessage::addWarning('The calculation of quick data has been stopped.');
    }

    /**
     * Preprocessor for no-action run
     */
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->quick_data_completed) {
            \XLite\Core\TopMessage::addInfo('The calculation of quick data has been completed successfully.');

            $this->setReturnURL(
                $this->buildURL('cache_management')
            );
        } elseif ($request->quick_data_failed) {
            \XLite\Core\TopMessage::addError($request->error_message ?: 'The calculation of quick data has been stopped.');

            $this->setReturnURL(
                $this->buildURL('cache_management')
            );
        }
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    protected function getQuickDataCancelFlagVarName()
    {
        return \XLite\Logic\QuickData\Generator::getCancelFlagVarName();
    }
}
