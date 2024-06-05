<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Controller\API;

use Symfony\Component\HttpFoundation\Response;

final class NotFoundController
{
    public function __invoke(): Response
    {
        $response = new Response(json_encode([
            'title'  => 'An error occurred',
            'detail' => 'Not found',
        ]), 404);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
