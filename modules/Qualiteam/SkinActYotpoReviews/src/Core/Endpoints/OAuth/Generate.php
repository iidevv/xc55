<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface;
use XCart\Container;
use XLite\Model\AEntity;

class Generate implements EndpointInterface
{
    /**
     * @param Endpoint          $endpoint
     * @param GenerateAssembler $generateAssembler
     * @param DynamicUrl        $dynamicUrl
     */
    public function __construct(
        private Endpoint $endpoint,
        private GenerateAssembler $generateAssembler,
        private DynamicUrl $dynamicUrl
    ) {
        $this->setMethod();
    }

    /**
     * @return void
     */
    protected function setMethod(): void
    {
        $this->endpoint->setMethod(
            Container::getContainer()?->getParameter('yotpo.reviews.api.oauth.generate.method')
        );
    }

    /**
     * @throws EndpointException
     */
    public function getData(?AEntity $entity = null): array
    {
        $this->assembleRequest();

        return $this->endpoint->getData();
    }

    /**
     * @return void
     */
    protected function assembleRequest(): void
    {
        $this->setPath();
        $this->prepareAssemblePath();

        $this->generateAssembler->assemble();
    }

    /**
     * @return void
     */
    protected function prepareAssemblePath(): void
    {
        $this->generateAssembler->setPath(
            $this->dynamicUrl->getUrl()
        );
    }

    /**
     * @return void
     */
    protected function setPath(): void
    {
        $this->dynamicUrl->setPath();
    }
}
