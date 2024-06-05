<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post;

use Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint;
use Qualiteam\SkinActKlarna\Core\Endpoints\AssemblerInterface;

class CreateSession
{
    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint           $endpoint
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\AssemblerInterface $sessionAssembler
     */
    public function __construct(
        private Endpoint           $endpoint,
        private AssemblerInterface $sessionAssembler
    ) {
        $this->sessionAssembler->assemble();
    }

    /**
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    public function getData(): array
    {
        $result = $this->endpoint->getData();
        $result['expiring_at'] = new \DateTime("tomorrow");
        return $result;
    }
}