<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status;

/**
 * Order status items list
 */
abstract class AStatus extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Should itemsList be wrapped with form
     *
     * @return bool
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'order_statuses';
    }


    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            [
                'page' => $this->getPage(),
            ]
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' =>  [
                static::COLUMN_NAME => static::t('Name'),
                static::COLUMN_CLASS  => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS => ['required' => true],
                static::COLUMN_ORDERBY  => 100,
            ],
            'orders_count' => [
                static::COLUMN_NAME => static::t('Orders'),
                static::COLUMN_TEMPLATE => 'modules/XC/CustomOrderStatuses/statuses/orders_count.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
        ];
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return bool
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return parent::isAllowEntityRemove($entity)
            && !$entity->getCode()
            && !$this->getOrdersCount($entity);
    }

    protected function getPage()
    {
        return '';
    }

    /**
     * Return orders count
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return integer
     */
    protected function getOrdersCount(\XLite\Model\AEntity $entity)
    {
        $ordersCount = \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(
            function () {
                return \XLite\Core\Database::getRepo('XLite\Model\Order')->countByStatus($this->getPage());
            },
            'orderStatusItemsList' . $this->getPage()
        );

        return $ordersCount[$entity->getId()] ?? 0;
    }

    /**
     * Return orders link
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getOrdersLink(\XLite\Model\AEntity $entity)
    {
        return $this->buildURL(
            'order_list',
            'search',
            [
                $this->getPage() . 'Status' => [$entity->getId()],
                \XLite::FORM_ID             => \XLite::getFormId()
            ]
        );
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add status';
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CustomOrderStatuses/statuses/style.css';

        return $list;
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return bool
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' order_statuses';
    }

    /**
     * Add right actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        return array_merge(
            parent::getRightActions(),
            ['modules/XC/CustomOrderStatuses/statuses/tooltip.twig']
        );
    }
}
