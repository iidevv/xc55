<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\ItemsList\Model;

use QSL\ProductFeeds\Logic\FeedGenerator\AFeedGeneratror;

/**
 * Widget displaying a list of comparison shopping websites.
 */
class ProductFeed extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Translations of status codes.
     *
     * @var array
     */
    protected $feedStatusCodes = [
        \QSL\ProductFeeds\Model\ProductFeed::STATUS_DISABLED   => 'Disabled feed',
        \QSL\ProductFeeds\Model\ProductFeed::STATUS_ERROR      => 'Error in feed',
        \QSL\ProductFeeds\Model\ProductFeed::STATUS_INPROGRESS => 'Feed in progress',
        \QSL\ProductFeeds\Model\ProductFeed::STATUS_NEVER      => 'Never generated feed',
        \QSL\ProductFeeds\Model\ProductFeed::STATUS_READY      => 'Feed ready',
    ];

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * Get model objects for entities selected in the list.
     *
     * @return array
     */
    public function getSelectedEntities()
    {
        $ids = $this->getSelectedEntityIds();

        return $this->getRepository()->findByIds($ids);
    }

    /**
     * Allow items to be selected.
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Define table columns.
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Feed name'),
                static::COLUMN_LINK     => "product_feed",
                static::COLUMN_ORDERBY  => 100,
            ],
            'type' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Feed type'),
                static::COLUMN_ORDERBY  => 200,
            ],
            'path' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Feed file'),
                static::COLUMN_ORDERBY  => 300,
            ],
            'date' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Feed date'),
                static::COLUMN_TEMPLATE => 'modules/QSL/ProductFeeds/product_feeds/cell.date.twig',
                static::COLUMN_ORDERBY  => 400,
            ],
            'status' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Feed status'),
                static::COLUMN_TEMPLATE => 'modules/QSL/ProductFeeds/product_feeds/cell.status.twig',
                static::COLUMN_ORDERBY  => 500,
            ],
        ];
    }

    // /**
    //  * Return list of templates for actions shown to the right of each row.
    //  *
    //  * @return array
    //  */
    // protected function getRightActions()
    // {
    //     $list = parent::getRightActions();

    //     $list[] = 'modules/QSL/ProductFeeds/items_list/model/table/parts/settings.twig';

    //     return $list;
    // }


    /**
     * Define model repository name.
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\ProductFeeds\Model\ProductFeed';
    }

    /**
     * Get container CSS class.
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' product-feeds';
    }

    /**
     * Return parameters to use in the search method.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    /**
     * Preprocess value for the Type column.
     *
     * @param string               $data   Raw column value.
     * @param array                $column Column being displayed.
     * @param \XLite\Model\AEntity $entity Model being displayed.
     *
     * @return string
     */
    protected function preprocessType($data, array $column, \XLite\Model\AEntity $entity)
    {
        return strtoupper($data);
    }

    /**
     * Preprocess value for the Feed column.
     *
     * @param string                                           $data   Raw column value.
     * @param array                                            $column Column being displayed.
     * @param \QSL\ProductFeeds\Model\ProductFeed $entity Model being displayed.
     *
     * @return string
     */
    protected function preprocessPath($data, array $column, \QSL\ProductFeeds\Model\ProductFeed $entity)
    {
        return trim($data) ? $this->getFeedLink($entity) : $entity->getGenerator()->getFeedFilename();
    }

    /**
     * Return link to the product feed.
     *
     * @param \QSL\ProductFeeds\Model\ProductFeed $entity Feed instance.
     *
     * @return string
     */
    protected function getFeedLink(\QSL\ProductFeeds\Model\ProductFeed $entity)
    {
        $directLinks = \XLite\Core\Config::getInstance()->QSL->ProductFeeds->feed_generator_direct_links;

        $feedUrl = $directLinks
            ? \XLite::getInstance()->getShopURL(
                LC_FILES_URL . '/' . AFeedGeneratror::FEED_DIR . '/' . $entity->getFilename()
            )
            : \XLite\Core\Converter::buildURL(
                'product_feeds',
                'download',
                [
                    'id' => $entity->getId(),
                ]
            );

        return '<a href="' . $feedUrl . '">' . $entity->getFilename() . '</a>';
    }

    /**
     * Preprocess value for the Generated column.
     *
     * @param string               $data   Raw column value.
     * @param array                $column Column being displayed.
     * @param \XLite\Model\AEntity $entity Model being displayed.
     *
     * @return string
     */
    protected function preprocessDate($data, array $column, \XLite\Model\AEntity $entity)
    {
        return $data ? \XLite\Core\Converter::getInstance()->formatTime($data) : '&ndash;';
    }

    /**
     * Return value for the "Feed status" column.
     *
     * @param \XLite\Model\AEntity $entity Model being displayed.
     *
     * @return string
     */
    protected function getStatusColumnValue(\XLite\Model\AEntity $entity)
    {
        $code = $entity->getStatusCode();

        $status = $this->translateStatusCode($code);

        if ($code === \QSL\ProductFeeds\Model\ProductFeed::STATUS_INPROGRESS) {
            $status .= ' (' . floor($entity->getProgress() * 100) . '%)';
        }

        return $status;
    }

    /**
     * Check whether a feed has an error.
     *
     * @param \XLite\Model\AEntity $entity Feed instance.
     *
     * @return boolean
     */
    protected function feedHasError(\XLite\Model\AEntity $entity)
    {
        return $entity->getStatusCode() === \QSL\ProductFeeds\Model\ProductFeed::STATUS_ERROR;
    }

    /**
     * Render feed errors as HTML.
     *
     * @param \XLite\Model\AEntity $entity Feed instance.
     *
     * @return string
     */
    protected function renderFeedErrors(\XLite\Model\AEntity $entity)
    {
        $code = '<ul>';
        foreach ($entity->getErrors() as $error) {
            $code .= "<li>$error</li>";
        }
        $code .= '</ul>';

        return $code;
    }

    /**
     * Return status message for the feed status code.
     *
     * @param string $code Feed status code.
     *
     * @return string
     */
    protected function translateStatusCode($code)
    {
        $status = $this->feedStatusCodes[$code] ?? 'Error';

        return static::t($status);
    }

    /**
     * Get identifiers of items selected in the list.
     *
     * @return array
     */
    protected function getSelectedEntityIds()
    {
        $data = $this->getRequestData();
        $prefix = $this->getSelectorDataPrefix();

        $list = [];

        if (isset($data[$prefix]) && is_array($data[$prefix]) && $data[$prefix]) {
            foreach ($data[$prefix] as $id => $allow) {
                if ($allow) {
                    $list[] = $id;
                }
            }
        }

        return $list;
    }

    /**
     * Class name of the sticky panel widget.
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return 'QSL\ProductFeeds\View\StickyPanel\ProductFeeds';
    }
}
