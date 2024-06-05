<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Post;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\XYotpoToken;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface;
use XCart\Container;
use XLite\Model\AEntity;
use XLite\Model\Order;

class Create implements EndpointInterface
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Post\CreateAssembler $createAssembler
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint                    $endpoint
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Post\DynamicUrl      $dynamicUrl
     *
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    public function __construct(
        private CreateAssembler $createAssembler,
        private Endpoint        $endpoint,
        private DynamicUrl      $dynamicUrl
    ) {
        $this->setHeaders();
        $this->setMethod();
    }

    /**
     * @return void
     */
    protected function setMethod(): void
    {
        $this->endpoint->setMethod(
            Container::getContainer()?->getParameter('yotpo.reviews.api.orders.create.method')
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
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    public function getData(?AEntity $entity): array
    {
        $this->assembleRequest($entity);

        return $this->endpoint->getData();
    }

    /**
     * @param \XLite\Model\Order|null $order
     *
     * @return void
     */
    protected function assembleRequest(?Order $order): void
    {
        $this->setDynamicUrlPath();
        $this->prepareAssemblePath();

        $this->createAssembler->setOrder($order);
        $this->createAssembler->assemble();
    }

    /**
     * @return void
     */
    protected function setDynamicUrlPath(): void
    {
        $this->dynamicUrl->setPath();
    }
}