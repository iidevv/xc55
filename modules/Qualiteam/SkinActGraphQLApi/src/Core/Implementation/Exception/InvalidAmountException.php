<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception;

class InvalidAmountException extends CartServiceException
{
    private $availableAmount;

    public function __construct($message = "", $availableAmount)
    {
        parent::__construct($message);
        $this->message = $message;
        $this->availableAmount = $availableAmount;
    }

    /**
     * @return mixed
     */
    public function getAvailableAmount()
    {
        return $this->availableAmount;
    }
}
