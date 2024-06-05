<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList\OrderStatus;

/**
 * Payment order status selector
 */
class Payment extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Order\Status\Payment')->findBy([], ['position' => 'asc']) as $status) {
            $list[$status->getId()] = $status->getName();
        }

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('All payment statuses');

        return $list;
    }
}
