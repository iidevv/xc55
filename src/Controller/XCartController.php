<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Controller;

use Symfony\Component\HttpFoundation\Response;

class XCartController
{
    public function index()
    {
        $xc = \XLite::getInstance();
        $xc->runCustomerZone();

        $response = new Response($xc->getContent(), $xc->getStatusCode(), $xc->getHeaders());

        foreach ($xc->getCookiesForHeaders() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function admin()
    {
        $xc = \XLite::getInstance();
        $xc->run(true)->processRequest();

        $response = new Response($xc->getContent(), $xc->getStatusCode(), $xc->getHeaders());

        foreach ($xc->getCookiesForHeaders() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
