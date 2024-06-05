<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\ItemsList\Model;

/**
 * CreateReturnOrderItem items list
 */
class CreateReturnOrderItem extends \XLite\View\ItemsList\Model\Table
{
    use \XLite\View\Base\ViewListsFallbackTrait;

    /**
     * Widget param names
     */
    public const PARAM_ORDER = 'order';

    /**
     * Order items data (before they are changed)
     *
     * @var array
     */
    protected $orderItemsData = [];

    /**
     * Cached order object
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'items_list/model/table/order_item/style.less';

        return $list;
    }

    /**
     * Get data prefix
     *
     * @return string
     */
    public function getDataPrefix()
    {
        return 'items';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, 'XLite\Model\Order'),
        ];
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        if ($this->order === null) {
            $order = $this->getParam(static::PARAM_ORDER);

            // Get temporary order if exists otherwise get current order
            $this->order = \XLite\Controller\Admin\Order::getTemporaryOrder($order->getOrderId(), false) ?: $order;
        }

        return $this->order;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'name' => [
                static::COLUMN_NAME         => static::t('Item'),
                static::COLUMN_TEMPLATE     => 'items_list/model/table/order_item/cell.name.twig',
                static::COLUMN_PARAMS       => [
                    \XLite\View\FormField\Select\Model\OrderItemSelector::PARAM_ORDER_ID => $this->getOrder()->getOrderId(),
                ],
                static::COLUMN_MAIN         => true,
                static::COLUMN_ORDERBY      => 100,
            ],
            'amount' => [
                static::COLUMN_NAME         => static::t('Qty'),
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\Input\Text\Integer',
                static::COLUMN_ORDERBY      => 200,
                static::COLUMN_PARAMS       => [
                    \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MIN => 0,
                ],
            ],
        ];

        return $columns;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        $result = 'XLite\Model\OrderItem';

        return $result;
    }

    // {{{ Behaviors

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Is checkbox checked
     *
     * @return boolean
     */
    public function isOrderItemSelected()
    {
        $result = false;

        if (
            \XLite\Core\Request::getInstance()->page == 'create_return'
            && $this->getItemsCount() === 1
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Template for selector action definition
     *
     * @return string
     */
    protected function getSelectorActionTemplate()
    {
        return 'modules/QSL/Returns/items_list/model/table/parts/selector.twig';
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' order-items';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'QSL\Returns\View\StickyPanel\ItemsList\CreateReturn';
    }

    // {{{ Search

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
     * Return params list to use for search
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

        $result->order = $this->getOrder();

        return $result;
    }

    // }}}

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getBottomActions()
    {
        $actions = parent::getBottomActions();

        $actions[] = 'modules/QSL/Returns/order/return/body.twig';

        return $actions;
    }

    /**
     * Prepare field params for
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return array
     */
    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        $list = parent::preprocessFieldParams($column, $entity);

        if ($column['code'] === 'amount') {
            if ($entity instanceof ReturnItem && $entity->getOrderItem()) {
                $maxAmount = $entity->getOrderItem()->getAmount();
            } else {
                $maxAmount = $entity->getAmount();
            }

            $list[\XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MAX] = $maxAmount;
            $list[\XLite\View\FormField\Input\Text\Base\Numeric::PARAM_VALUE] = $entity->getAmount();
        }

        return $list;
    }

    protected function isDetachedOrderItem($entity)
    {
        return \XLite\Core\Auth::getInstance()->isVendor() && $entity->getOriginalProduct();
    }
}
