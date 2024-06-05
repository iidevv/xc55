<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\FormField\Select\OrderStatus;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Shipping order status selector
 */
class Shipping extends \XLite\View\FormField\Select\OrderStatus\AOrderStatus
{
    use ExecuteCachedTrait;

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return !\XLite\Core\Request::getInstance()->isPost() && $this->getOrder() && $this->getOrder()->getShippingStatus()
            ? $this->getOrder()->getShippingStatus()->getId()
            : parent::getValue();
    }

    protected function assembleClasses(array $classes)
    {
        return array_merge(parent::assembleClasses($classes), [
            'order-shipping-status',
        ]);
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Model\Order\Status\Shipping';
    }

    /**
     * Return "all statuses" label
     *
     * @return string
     */
    protected function getAllStatusesLabel()
    {
        return 'All shipping statuses';
    }

    /**
     * @inheritdoc
     */
    protected function getFieldTemplate()
    {
        return 'modules/QSL/CustomerSatisfaction/form_field/select.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }
}
