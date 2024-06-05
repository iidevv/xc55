<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Ordermanagement\Orders\Post;

use XCart\Container;

class DynamicUrl
{
    const PARAM_END_PATH = 'refunds';

    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\DynamicUrl $dynamicUrl
     */
    public function __construct(
        private \Qualiteam\SkinActKlarna\Core\Endpoints\DynamicUrl $dynamicUrl
    )
    {
        $this->dynamicUrl->setPath(
            Container::getContainer()->getParameter('klarna.api.order.refund')
        );
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->dynamicUrl->getUrl();
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
        return sprintf('%s/%s', $value, self::PARAM_END_PATH);
    }
}