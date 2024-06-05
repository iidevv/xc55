<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Patch;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\XYotpoToken;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface;
use XCart\Container;
use XLite\Model\AEntity;
use XLite\Model\Product;

class Update implements EndpointInterface
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Patch\UpdateAssembler $updateAssembler
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint                       $endpoint
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Patch\DynamicUrl      $dynamicUrl
     *
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    public function __construct(
        private UpdateAssembler $updateAssembler,
        private Endpoint        $endpoint,
        private DynamicUrl      $dynamicUrl
    ) {
        $this->setHeaders();

        $this->setMethod();
    }

    /**
     * @return void
     */
    protected function setPath(): void
    {
        $this->dynamicUrl->setPath();
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
            Container::getContainer()?->getParameter('yotpo.reviews.api.products.update.method')
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
     * @param \XLite\Model\Product|null $product
     *
     * @return void
     */
    protected function assembleRequest(?Product $product): void
    {
        $this->setPath();
        $this->setUrlParam($product);

        $this->prepareAssemblePath();

        $this->updateAssembler->setProduct($product);
        $this->updateAssembler->assemble();
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return void
     */
    protected function setUrlParam(?Product $product): void
    {
        $this->dynamicUrl->setParam(
            $product?->getYotpoId()
        );
    }

    /**
     * @return void
     */
    protected function prepareAssemblePath(): void
    {
        $this->updateAssembler->setPath(
            $this->dynamicUrl->getUrl()
        );
    }
}