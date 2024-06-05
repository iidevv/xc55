<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Api\Reviews;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\OAuthYotpoToken;
use XCart\Container;

class CollectAllBuilder
{
    public function __construct(
        private array $args = []
    ) {
    }

    public function getResult(): array
    {
        return Container::getContainer()?->get('yotpo.reviews.service.api.review.collectall')->getData(
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
            'utoken' => (new OAuthYotpoToken())->getYotpoToken(),
        ];
    }

    protected function prepareAdditionalParams(): array
    {
        $params = [
            'count' => $this->getCount(),
            'page'  => $this->getPage(),
        ];

        if ($this->hasSinceDate()) {
            $params['since_date'] = $this->getSinceDate();
        }

        return $params;
    }

    protected function getCount(): int
    {
        return $this->args['count'] ?? 100;
    }

    public function getPage(): int
    {
        return !empty($this->args['page']) ? (int) $this->args['page'] : 1;
    }

    protected function hasSinceDate(): bool
    {
        return isset($this->args['since_date']);
    }

    protected function getSinceDate(): int
    {
        return (int) $this->args['since_date'];
    }

    public function setCount(int $value): void
    {
        $this->args['count'] = $value;
    }

    public function setPage(int $value): void
    {
        $this->args['page'] = $value;
    }

    public function setSinceDate(int $value): void
    {
        $this->args['since_date'] = $value;
    }
}
