<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * ____description____
 */
class WidgetsCollection extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Do "get" action
     *
     * @return void
     */
    protected function doActionGet()
    {
    }

    /**
     * Print AJAX request output
     *
     * @param mixed $viewer Viewer to display in AJAX
     *
     * @return void
     */
    protected function printAJAXOutput($viewer)
    {
        $output = [];
        $params = $this->getViewerParams();
        foreach (array_unique($viewer->getWidgetsCollection()) as $viewClass) {
            $view = new $viewClass($params);

            $output[] = [
                'view' => $view->getFingerprint(),
                'content' => $view->getContent(),
            ];
        }

        echo (json_encode($output));
    }

    /**
     * Get cart fingerprint exclude keys
     *
     * @return array
     */
    protected function getCartFingerprintExclude()
    {
        return [
            'shippingMethodsHash',
            'paymentMethodsHash',
            'shippingMethodId',
            'paymentMethodId',
            'shippingTotal'
        ];
    }
}
