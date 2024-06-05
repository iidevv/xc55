<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Get;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface;
use XCart\Container;
use XLite\Model\AEntity;
use XLite\Model\Product;

class Request implements EndpointInterface
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Get\RequestAssembler $requestAssembler
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint                      $endpoint
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Get\DynamicUrl       $dynamicUrl
     */
    public function __construct(
        private RequestAssembler $requestAssembler,
        private Endpoint $endpoint,
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
            Container::getContainer()?->getParameter('yotpo.reviews.api.products.bottomline.method')
        );
    }

    /**
     * @return void
     */
    protected function prepareAssemblePath(): void
    {
        $this->requestAssembler->setPath(
            $this->dynamicUrl->getUrl()
        );
    }

    /**
     * @param string $sku
     *
     * @return void
     */
    protected function setParam(string $sku): void
    {
        $this->dynamicUrl->setParam($sku);
    }

    /**
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    public function getData(?AEntity $entity): array
    {
        $this->assembleRequest($entity);

        return $this->endpoint->getData();
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return void
     */
    protected function assembleRequest(?Product $product): void
    {
        $this->setParam($product?->getSku());

        $this->prepareAssemblePath();
        $this->requestAssembler->assemble();
    }
}
