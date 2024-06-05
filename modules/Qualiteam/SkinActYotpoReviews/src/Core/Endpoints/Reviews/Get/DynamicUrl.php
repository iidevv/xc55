<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Reviews\Get;

use XCart\Container;

/**
 * https://api.yotpo.com/v1/apps/{app_key}/reviews
 */
class DynamicUrl
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\DynamicUrl $dynamicUrl
     */
    public function __construct(
        private \Qualiteam\SkinActYotpoReviews\Core\Endpoints\DynamicUrl $dynamicUrl
    )
    {
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->dynamicUrl->getUrl('apps');
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
        return sprintf('/%s', Container::getContainer()?->getParameter('yotpo.reviews.api.ugc.reviews.collectall'));
    }
}