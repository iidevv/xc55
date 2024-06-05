<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Payment\Method\Admin;

/**
 * Predefined Gateways
 */
class PredefinedGateways extends \XLite\View\ItemsList\Payment\Method\Admin\AAdmin
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $cnd->{\XLite\Model\Repo\Payment\Method::P_PREDEFINED} = true;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}       = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        ];

        return $cnd;
    }

    /**
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' predefined-methods';
    }

    /**
     * Returns 'actions' list name (with payment method service name)
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    protected function getPredefinedMethodsActionsListName(\XLite\Model\Payment\Method $method)
    {
        $serviceName = $method->getServiceName();

        return 'actions.' . preg_replace('/[^\w]/', '_', $serviceName);
    }

    /**
     * @return boolean
     */
    protected function isPredefinedList()
    {
        return true;
    }

    /**
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    protected function getLineClass(\XLite\Model\Payment\Method $method)
    {
        $classes = parent::getLineClass($method);

        if ($this->isConfigured($method)) {
            $classes .= ' configured';
        }

        return $classes;
    }
}
