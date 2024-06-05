<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Api\Reviews;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use XCart\Container;

class CreateBuilder
{
    public function __construct(
        private array $args
    ) {
    }

    public function getResult(): array
    {
        return Container::getContainer()?->get('yotpo.reviews.service.api.review.create')?->getData(
            $this
        );
    }

    public function getPreparedParams(): array
    {
        return array_merge($this->prepareRequiredParams(), $this->prepareAdditionalParams());
    }

    protected function prepareRequiredParams(): array
    {
        return [
            'appkey'         => $this->getAppKey(),
            'sku'            => $this->getProductSku(),
            'product_title'  => $this->getProductTitle(),
            'product_url'    => $this->getProductUrl(),
            'display_name'   => $this->getDisplayName(),
            'email'          => $this->getEmail(),
            'review_content' => $this->getReviewContent(),
            'review_title'   => $this->getReviewTitle(),
            'review_score'   => $this->getReviewScore(),
        ];
    }

    protected function getAppKey(): string
    {
        return $this->getConfiguration()->getAppKey();
    }

    protected function getConfiguration(): Configuration
    {
        return Container::getContainer()?->get('yotpo.reviews.configuration');
    }

    protected function getProductSku(): string
    {
        return $this->args['sku'];
    }

    protected function getProductTitle(): string
    {
        return $this->args['product_title'];
    }

    protected function getProductUrl(): string
    {
        return $this->args['product_url'];
    }

    protected function getDisplayName(): string
    {
        return $this->args['display_name'];
    }

    protected function getEmail(): string
    {
        return $this->args['email'];
    }

    protected function getReviewContent(): string
    {
        return $this->args['review_content'];
    }

    protected function getReviewTitle(): string
    {
        return $this->args['review_title'];
    }

    protected function getReviewScore(): int
    {
        return $this->args['review_score'];
    }

    // TODO additional params
    protected function prepareAdditionalParams(): array
    {
        return [];
    }
}