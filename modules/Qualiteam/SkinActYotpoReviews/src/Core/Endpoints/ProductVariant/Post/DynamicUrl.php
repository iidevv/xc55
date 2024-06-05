<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Post;

use XCart\Container;

/**
 * https://api.yotpo.com/core/v3/stores/{store_id}/products/{yotpo_product_id}/variants
 */
class DynamicUrl
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\DynamicUrl $dynamicUrl
     */
    public function __construct(
        private \Qualiteam\SkinActYotpoReviews\Core\Endpoints\DynamicUrl $dynamicUrl
    ) {
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->dynamicUrl->getUrl();
    }

    /**
     * @return void
     */
    public function setPath(): void
    {
        $this->dynamicUrl->setPath(
            $this->concatPath()
        );
    }

    /**
     * @return string
     */
    protected function concatPath(): string
    {
        return sprintf('/%s',
            Container::getContainer()?->getParameter('yotpo.reviews.api.products')
        );
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setParam(string $value): void
    {
        $this->dynamicUrl->setParam(
            $this->concatUrlParams($value)
        );
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function concatUrlParams(string $value): string
    {
        return sprintf('/%s/variants', $value);
    }
}
