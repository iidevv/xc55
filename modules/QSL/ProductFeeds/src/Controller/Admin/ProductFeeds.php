<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Controller\Admin;

use QSL\ProductFeeds\View\ItemsList\Model\ProductFeed as ItemsList;
use QSL\ProductFeeds\Core\EventListener\GenerateFeeds;

/**
 * Controller for the Comparison Shopping Websites page.
 */
class ProductFeeds extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions working without the secure token.
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['download']);
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Feeds');
    }

    /**
     * Get search condition parameter by name.
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    /**
     * Save search conditions.
     */
    protected function doActionSearch()
    {
        $cellName = ItemsList::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            ItemsList::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions.
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = ItemsList::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];
        }

        return $searchParams;
    }

    /**
     * Update list.
     */
    protected function doActionGenerate()
    {
        $list = new ItemsList();
        $count = 0;
        foreach ($list->getSelectedEntities() as $feed) {
            $count++;
            if (!$feed->isInProgress()) {
                $feed->queue();
            }
        }

        if ($count) {
            if (!$this->isFeedGenerationStarted()) {
                \XLite\Core\EventTask::generateFeeds();
            }
            \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState(GenerateFeeds::EVENT_NAME);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo(
                static::t('Selected feeds (X) have been scheduled.', ['count' => $count])
            );

            $this->setReturnURL($this->buildURL('product_feeds'));
        } else {
            \XLite\Core\TopMessage::addError(
                static::t('No product feeds were selected.')
            );
        }
    }


    /**
     * Update list.
     */
    protected function doActionDownload()
    {
        $id = (int) \XLite\Core\Request::getInstance()->id;
        $feed = $id
            ? \XLite\Core\Database::getRepo('QSL\ProductFeeds\Model\ProductFeed')->find($id)
            : null;

        $path = $feed ? $feed->getPath() : false;

        if ($path && is_readable($path)) {
            $name = basename($path);
            header('Content-Type: ' . $this->getMimeType($feed->getType()) . '; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $name . '"; modification-date="' . date('r') . ';');
            header('Content-Length: ' . filesize($path));

            readfile($path);
            die(0);
        }

        $errorMessage = static::t('Can\'t find the feed file.');
        \XLite\Core\TopMessage::addError($errorMessage);

        $feed->setPath('');
        $feed->addErrors([$errorMessage]);
        $feed->update();

        $this->setReturnURL($this->buildURL('product_feeds'));
    }

    /**
     * Get MIME type for the feed file.
     *
     * @param string $feedType Feed type.
     *
     * @return string
     */
    protected function getMimeType($feedType)
    {
        $types = [
            \QSL\ProductFeeds\Model\ProductFeed::FEED_TYPE_CSV => 'text/csv',
            \QSL\ProductFeeds\Model\ProductFeed::FEED_TYPE_TXT => 'text/plain',
            \QSL\ProductFeeds\Model\ProductFeed::FEED_TYPE_XML => 'application/xml',
        ];

        return $types[$feedType] ?? 'text/csv';
    }

    /**
     * Check whether the feed generation process is already going.
     *
     * @return bool
     */
    protected function isFeedGenerationStarted()
    {
        return is_object(
            \XLite\Core\Database::getRepo('XLite\Model\EventTask')->findOneByName(
                \QSL\ProductFeeds\Core\EventListener\GenerateFeeds::EVENT_NAME
            )
        );
    }
}
