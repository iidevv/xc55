<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\XYotpoToken;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface;
use XC\ProductVariants\Model\ProductVariant;
use XCart\Container;
use XLite\Model\AEntity;

class Update implements EndpointInterface
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch\UpdateAssembler $createAssembler
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint                             $endpoint
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch\DynamicUrl      $dynamicUrl
     *
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    public function __construct(
        private UpdateAssembler $createAssembler,
        private Endpoint        $endpoint,
        private DynamicUrl      $dynamicUrl
    ) {
        $this->setHeaders();

        $this->setMethod();
    }

    /**
     * @return void
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    protected function setHeaders(): void
    {
        $this->endpoint->setHeaders([
            'X-Yotpo-Token' => (new XYotpoToken())->getYotpoToken(),
        ]);
    }

    /**
     * @return void
     */
    protected function setMethod(): void
    {
        $this->endpoint->setMethod(
            Container::getContainer()?->getParameter('yotpo.reviews.api.product.variant.update.method')
        );
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
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return void
     */
    protected function assembleRequest(?ProductVariant $variant): void
    {
        $this->setPath();
        $this->setUrlParams($variant);

        $this->prepareAssemblePath();

        $this->createAssembler->setProductVariant($variant);
        $this->createAssembler->assemble();
    }

    /**
     * @return void
     */
    protected function setPath(): void
    {
        $this->dynamicUrl->setPath();
    }

    /**
     * @param ProductVariant|null $variant
     *
     * @return void
     */
    protected function setUrlParams(?ProductVariant $variant): void
    {
        $this->dynamicUrl->setParam(
            $variant?->getProduct()->getYotpoId(),
            $variant?->getYotpoId()
        );
    }

    /**
     * @return void
     */
    protected function prepareAssemblePath(): void
    {
        $this->createAssembler->setPath(
            $this->dynamicUrl->getUrl()
        );
    }
}
