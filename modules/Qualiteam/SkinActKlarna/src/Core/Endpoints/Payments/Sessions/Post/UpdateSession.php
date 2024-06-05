<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post;

use XLite\Core\Session;
use Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint;
use Qualiteam\SkinActKlarna\Core\Endpoints\AssemblerInterface;

class UpdateSession
{
    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint                          $endpoint
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\AssemblerInterface                $sessionAssembler
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post\DynamicUrl $dynamicUrl
     */
    public function __construct(
        private Endpoint           $endpoint,
        private AssemblerInterface $sessionAssembler,
        private DynamicUrl         $dynamicUrl,
    ) {
        $this->setSessionId();
        $this->prepareUpdateSessionUrl();

        $this->sessionAssembler->assemble();
    }

    /**
     * @return void
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    public function postData(): void
    {
        $this->endpoint->postData();
    }

    /**
     * @return void
     */
    protected function prepareUpdateSessionUrl(): void
    {
        $this->sessionAssembler->setPath(
            $this->dynamicUrl->getUrl()
        );
    }

    /**
     * @return void
     */
    protected function setSessionId(): void
    {
        $this->dynamicUrl->setParam(
            Session::getInstance()->klarna_session['session_id'] ?? ''
        );
    }
}