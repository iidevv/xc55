<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Cart\ShippingEstimator;

/**
 * Shipping estimator destination form
 */
class Destination extends \XLite\View\Form\Cart\ShippingEstimator\AShippingEstimator
{
    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'set_destination';
    }
}
