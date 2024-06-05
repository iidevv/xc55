<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Ordermanagement\Orders\Post;

use Qualiteam\SkinActKlarna\Core\Endpoints\Constructor;
use Qualiteam\SkinActKlarna\Core\Endpoints\ConstructorInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\Ordermanagement\Orders\Post\Params\SetRefundedAmountInterface;
use Qualiteam\SkinActKlarna\Helpers\Converter;
use XCart\Container;
use XLite\Model\Order;
use XLite\Model\Payment\BackendTransaction;

class RefundConstructor implements ConstructorInterface, SetRefundedAmountInterface
{
    /**
     * @param \Qualiteam\SkinActKlarna\Helpers\Converter          $converter
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Constructor $constructor
     */
    public function __construct(
        private Converter $converter,
        private Constructor $constructor,
    )
    {
    }

    /**
     * Collecting a constructed body
     *
     * @return void
     */
    public function build(): void
    {
        $this->constructor->build($this);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->constructor->getBody();
    }

    /**
     * @return void
     */
    public function setRefundedAmount(): void
    {
        $this->constructor->addParam(
            self::PARAM_REFUNDED_AMOUNT,
            $this->converter->getRefundedAmount($this->getBackendTransaction())
        );
    }

    /**
     * @return \XLite\Model\Payment\BackendTransaction
     */
    public function getBackendTransaction(): BackendTransaction
    {
        return Container::getContainer()->get('Qualiteam\SkinActKlarna\Core\Endpoints\Params')->getTransaction();
    }
}