<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order;

use XCart\Extender\Mapping\ListChild;

/**
 * Actions  row
 *
 * @ListChild (list="order.actions", weight="200", zone="admin")
 */
class Actions extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/page/parts/action.buttons.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && 0 < count($this->defineOrderActions());
    }

    /**
     * Get order aActions
     *
     * @param \XLite\Model\Order $entity Order
     *
     * @return array
     */
    protected function getOrderActions(\XLite\Model\Order $entity)
    {
        $list = [];

        foreach ($this->defineOrderActions() as $action) {
            $arguments = [
                'order_number' => $this->getOrder()->getOrderNumber(),
            ];
            $parameters = [
                'label'    => ucfirst($action),
                'location' => \XLite\Core\Converter::buildURL('order', $action, $arguments),
            ];

            $list[] = $this->getWidget($parameters, 'XLite\View\Button\Link');
        }

        return $list;
    }

    /**
     * Define order actions
     *
     * @return array
     */
    protected function defineOrderActions()
    {
        return $this->getOrder()->getAllowedActions();
    }
}
