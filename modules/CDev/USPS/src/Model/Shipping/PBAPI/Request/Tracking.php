<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\Model\Shipping\PBAPI\Request;

class Tracking extends Request
{
    public function __construct($endpoint, $token, $trackingNumber)
    {
        $urlParams = [
            'carrier'               => 'USPS',
            'packageIdentifierType' => 'TrackingNumber',
        ];

        parent::__construct(
            $endpoint . '/shippingservices/v1/tracking/' . $trackingNumber . '?' . http_build_query($urlParams, null, '&'),
            'GET',
            null,
            [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ]
        );
    }
}
