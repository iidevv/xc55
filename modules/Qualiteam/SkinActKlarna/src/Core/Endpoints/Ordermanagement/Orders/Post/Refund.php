<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Ordermanagement\Orders\Post;

use Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint;
use XCart\Container;

class Refund
{
    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Ordermanagement\Orders\Post\RefundAssembler $refundAssembler
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint                                    $endpoint
     */
    public function __construct(
        private RefundAssembler $refundAssembler,
        private Endpoint       $endpoint
    ) {
        $this->refundAssembler->setPath(
            Container::getContainer()->get('klarna.service.api.ordermanagement.orders.refund.dynamic.url')->getUrl()
        );

        $this->refundAssembler->assemble();
    }

    /**
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    public function getData(): array
    {
        return $this->endpoint->getData();
    }
}