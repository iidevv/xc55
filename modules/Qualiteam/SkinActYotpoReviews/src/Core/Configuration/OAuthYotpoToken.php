<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Configuration;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\OAuth\Generate;
use XCart\Container;
use XLite\Core\Session;

class OAuthYotpoToken
{
    public function getYotpoToken(): string
    {
        static $isPosted = false;

        if ($this->isPostRequest($isPosted)) {
            Session::getInstance()->oauth_yotpo_token = $this->prepareOAuthYotpoToken();

            $isPosted = true;
        }

        return Session::getInstance()->oauth_yotpo_token;
    }

    protected function isPostRequest(bool $isPosted): bool
    {
        return !Session::getInstance()->oauth_yotpo_token
            && !$isPosted;
    }

    protected function prepareOAuthYotpoToken(): string
    {
        $request = $this->getOAuthYotpoTokenRequest();
        return $request['access_token'] ?? '';
    }

    protected function getOAuthYotpoTokenRequest(): array
    {
        return $this->getOAuthYotpoAuthGenerateContainer()?->getData();
    }

    protected function getOAuthYotpoAuthGenerateContainer(): ?Generate
    {
        return Container::getContainer()?->get('yotpo.reviews.service.api.oauth.generate');
    }
}
