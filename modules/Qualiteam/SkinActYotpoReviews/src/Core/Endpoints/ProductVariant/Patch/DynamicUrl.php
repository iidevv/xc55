<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch;

use XCart\Container;

/**
 * https://api.yotpo.com/core/v3/stores/{store_id}/products/{yotpo_product_id}/variants/{yotpo_variant_id}
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
     * @param string $productYotpoId
     * @param string $variantYotpoId
     *
     * @return void
     */
    public function setParam(string $productYotpoId, string $variantYotpoId): void
    {
        $this->dynamicUrl->setParam(
            $this->concatUrlParams($productYotpoId, $variantYotpoId)
        );
    }

    /**
     * @param string $productYotpoId
     * @param string $variantYotpoId
     *
     * @return string
     */
    protected function concatUrlParams(string $productYotpoId, string $variantYotpoId): string
    {
        return sprintf('/%s/%s/%s',
            $productYotpoId,
            Container::getContainer()?->getParameter('yotpo.reviews.api.products.variants'),
            $variantYotpoId
        );
    }
}
