<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Reviews\Post;

use Qualiteam\SkinActYotpoReviews\Core\Api\Reviews\CreateBuilder;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Endpoint;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException;
use Qualiteam\SkinActYotpoReviews\Core\Factory\LoggerFactory;
use XCart\Container;

class Create
{
    public function __construct(
        private Endpoint $endpoint,
    ) {
    }

    public function getData(CreateBuilder $createBuilder): array
    {
        $this->assembleRequest($createBuilder);

        try {
            return $this->endpoint->getData();
        } catch (EndpointException $e) {
            LoggerFactory::logger()->error($e->getMessage());
        }
    }

    protected function assembleRequest(CreateBuilder $createBuilder): void
    {
        $this->setMethod();
        $this->setPath();
        $this->setBody($createBuilder);
    }

    protected function setMethod(): void
    {
        $this->endpoint->setMethod(
            Container::getContainer()?->getParameter('yotpo.reviews.api.reviews.create.method')
        );
    }

    protected function setPath(): void
    {
        $this->endpoint->setPath(
            $this->getReviewsUrl()
        );
    }

    protected function getReviewsUrl(): string
    {
        return Container::getContainer()?->getParameter('yotpo.reviews.api.ugc.reviews');
    }

    protected function setBody(CreateBuilder $createBuilder): void
    {
        $this->endpoint->setBody($createBuilder->getPreparedParams());
    }
}