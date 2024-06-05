<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations\Post;

use Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint;
use XCart\Container;

class Order
{
    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations\Post\OrderAssembler $orderAssembler
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint                                    $endpoint
     */
    public function __construct(
        private OrderAssembler $orderAssembler,
        private Endpoint       $endpoint
    ) {

        $this->orderAssembler->setPath(
            Container::getContainer()->get('klarna.service.api.payments.authorization.create.dynamic.url')->getUrl()
        );

        $this->orderAssembler->assemble();
    }

    /**
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    public function getData(): array
    {
        return $this->endpoint->getData();
    }
}