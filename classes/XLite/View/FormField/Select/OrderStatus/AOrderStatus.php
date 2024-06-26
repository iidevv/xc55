<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\OrderStatus;

use XLite\Core\Database;

/**
 * Abstract order status selector
 */
abstract class AOrderStatus extends \XLite\View\FormField\Select\Regular
{
    /**
     * Common params
     */
    public const PARAM_ORDER = 'order';
    public const PARAM_ALL_OPTION  = 'allOption';

    /**
     * Define repository name
     *
     * @return string
     */
    abstract protected function defineRepositoryName();

    /**
     * Return "all statuses" label
     *
     * @return string
     */
    abstract protected function getAllStatusesLabel();

    /**
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepo()
    {
        return Database::getRepo($this->defineRepositoryName());
    }

    /**
     * Define widget params
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
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
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                '\XLite\Model\Order'
            ),
            self::PARAM_ALL_OPTION  => new \XLite\Model\WidgetParam\TypeBool(
                'Show "All status" option',
                false,
                false
            ),
        ];
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
    }

    /**
     * Define the options list
     *
     * @return array
     */
    protected function getOptions()
    {
        $data = $this->getRepo()->findBy(
            [],
            ['position' => 'asc']
        );
        $list = [];
        foreach ($data as $status) {
            if (
                !$this->getOrder()
                || $this->getParam(static::PARAM_ALL_OPTION)
                || $status->isAllowedToSetManually()
                || $status->getId() == $this->getValue()
            ) {
                $list[$status->getId()] = $status->getName();
            }
        }

        if ($this->getOrder() && !$this->getValue()) {
            $list = [0 => static::t('Status is not defined')] + $list;
        } elseif ($this->getParam(static::PARAM_ALL_OPTION)) {
            $list = [0 => static::t($this->getAllStatusesLabel())] + $list;
        }

        return $list;
    }
}
