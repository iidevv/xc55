<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Override;

use XLite\InjectLoggerTrait;

class Microsoft extends \Stevenmaguire\OAuth2\Client\Provider\Microsoft
{
    use InjectLoggerTrait;

    /**
     * @inheritdoc
     */
    protected function checkResponse(\Psr\Http\Message\ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            $message = $response->getReasonPhrase();
            if (!empty($data['error']['message'])) {
                $message = $data['error']['message'];
            }
            if (!empty($data['error_description'])) {
                $message = $data['error_description'];
                $this->getLogger('QSL-OAuth2Client')->error('', [
                    'Error'       => $data['error'],
                    'Description' => $data['error_description'],
                    'Provider'    => 'microsoft',
                ]);
            }

            throw new \League\OAuth2\Client\Provider\Exception\IdentityProviderException(
                $message,
                $response->getStatusCode(),
                $response
            );
        }
    }
}
