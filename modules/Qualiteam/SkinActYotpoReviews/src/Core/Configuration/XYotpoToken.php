<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Configuration;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth\Generate;
use XCart\Container;
use XLite\Core\Session;

class XYotpoToken
{
    /**
     * @return string
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    public function getYotpoToken(): string
    {
        static $isPosted = false;

        if ($this->isPostRequest($isPosted)) {
            Session::getInstance()->yotpo_token = $this->prepareYotpoToken();

            $isPosted = true;
        }

        return Session::getInstance()->yotpo_token;
    }

    /**
     * @param bool $isPosted
     *
     * @return bool
     */
    protected function isPostRequest(bool $isPosted): bool
    {
        return !Session::getInstance()->yotpo_token
            && !$isPosted;
    }

    /**
     * @return string
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    protected function prepareYotpoToken(): string
    {
        $request = $this->getYotpoTokenRequest();
        return $request['access_token'] ?? '';
    }

    /**
     * @return array
     * @throws \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointException
     */
    protected function getYotpoTokenRequest(): array
    {
        return $this->getYotpoAuthGenerateContainer()?->getData();
    }

    /**
     * @return \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Auth\Generate|null
     */
    protected function getYotpoAuthGenerateContainer(): ?Generate
    {
        return Container::getContainer()?->get('yotpo.reviews.service.api.auth.generate');
    }
}