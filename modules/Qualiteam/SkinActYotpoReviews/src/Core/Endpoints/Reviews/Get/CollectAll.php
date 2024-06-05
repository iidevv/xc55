<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Reviews\Get;

use Qualiteam\SkinActYotpoReviews\Core\Api\Reviews\CollectAllBuilder;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException;
use Qualiteam\SkinActYotpoReviews\Core\Factory\LoggerFactory;
use XCart\Container;

class CollectAll
{
    public function __construct(
        private Endpoint            $endpoint,
        private DynamicUrl          $dynamicUrl,
    ) {
    }

    public function getData(CollectAllBuilder $collectAllBuilder): array
    {
        $this->assembleRequest($collectAllBuilder);

        try {
            return $this->endpoint->getData();
        } catch (EndpointException $e) {
            LoggerFactory::logger()->error($e->getMessage());
        }
    }

    protected function setMethod(): void
    {
        $this->endpoint->setMethod(
            Container::getContainer()?->getParameter('yotpo.reviews.api.reviews.collectall.method')
        );
    }

    protected function assembleRequest(CollectAllBuilder $collectAllBuilder): void
    {
        $this->setQuery($collectAllBuilder);
        $this->preparePath();
        $this->setPath();
        $this->setMethod();
    }

    protected function setQuery(CollectAllBuilder $collectAllBuilder): void
    {
        $this->endpoint->setQuery($collectAllBuilder->getPreparedParams());
    }

    protected function setPath(): void
    {
        $this->endpoint->setPath(
            $this->dynamicUrl->getUrl()
        );
    }

    protected function preparePath(): void
    {
        $this->dynamicUrl->setPath();
    }
}