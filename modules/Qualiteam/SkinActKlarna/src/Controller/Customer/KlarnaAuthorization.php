<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Controller\Customer;

use XCart\Container;
use XLite\Controller\Customer\ACustomer;
use XLite\Core\Request;
use XLite\Core\Session;

class KlarnaAuthorization extends ACustomer
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess(): bool
    {
        return parent::checkAccess()
            && $this->isAJAX();
    }

    public function doActionUpdateSession(): void
    {
        Container::getContainer()->get('klarna.service.api.payments.sessions.update')->postData();
    }

    /**
     * @return void
     */
    public function doActionAuthorization(): void
    {
        $container = Container::getContainer()->get('klarna.service.api.payments.authorization');
        $data = $container->getAuthorizationParams();

        $this->printAJAX($data);
        die;
    }

    /**
     * @return void
     */
    public function doActionExpressButton(): void
    {
        Session::getInstance()->klarna_profile = Request::getInstance()->data;

        $url = $this->buildURL('checkout', '', [
            'methodId' => Container::getContainer()->get('klarna.configuration')->getMethodId()
        ]);

        $this->printAJAX(['url' => $url]);
        die;
    }
}