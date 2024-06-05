<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\String;

/**
 * IP4 address
 */
class IP4 extends \XLite\Core\Validator\TypeString
{
    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    public function validate($data)
    {
        parent::validate($data);

        if (filter_var($data, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV4]) === false) {
            throw $this->throwError('Not an IPv4 address');
        }
    }
}
