<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core;

use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post\CreateSession;
use Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post\UpdateSession;
use Qualiteam\SkinActKlarna\Core\Validators\Payments\Validator;
use XCart\Container;
use XLite\Core\Session;
use XLite\InjectLoggerTrait;

class KlarnaCheckout
{
    use InjectLoggerTrait;

    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post\CreateSession $createSession
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post\UpdateSession $updateSession
     */
    public function __construct(
        private CreateSession $createSession,
        private UpdateSession $updateSession,
    ) {
    }

    /**
     * @return array
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    public function getKlarnaSessions(): array
    {
        static $isPosted = false;

        if (!$isPosted) {
            if (Validator::hasValidKlarnaSession()) {
                $this->getUpdatedKlarnaSessionsRequest();
            } else {
                Session::getInstance()->klarna_session = $this->getKlarnaSessionsRequest();
            }

            $isPosted = true;

            return Session::getInstance()->klarna_session;
        }

        return Session::getInstance()->klarna_session ?? $this->getKlarnaSessionsRequest();
    }

    /**
     * @return void
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    protected function getUpdatedKlarnaSessionsRequest(): void
    {
        $this->updateSession->postData();
    }

    /**
     * @throws \Qualiteam\SkinActKlarna\Core\Endpoints\EndpointException
     */
    protected function getKlarnaSessionsRequest(): array
    {
        return $this->createSession->getData();
    }
}