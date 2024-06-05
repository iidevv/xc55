<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface;
use XCart\Container;
use XLite\Model\AEntity;

class Generate implements EndpointInterface
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint               $endpoint
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth\GenerateAssembler $generateAssembler
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth\DynamicUrl        $dynamicUrl
     */
    public function __construct(
        private Endpoint          $endpoint,
        private GenerateAssembler $generateAssembler,
        private DynamicUrl        $dynamicUrl
    ) {
        $this->setMethod();
    }

    /**
     * @return void
     */
    protected function setMethod(): void
    {
        $this->endpoint->setMethod(
            Container::getContainer()?->getParameter('yotpo.reviews.api.auth.generate.method')
        );
    }

    /**
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
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