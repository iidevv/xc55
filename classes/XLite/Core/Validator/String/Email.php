<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\String;

/**
 * E-mail
 */
class Email extends \XLite\Core\Validator\TypeString
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

        if (0 < strlen($data) && filter_var($data, FILTER_VALIDATE_EMAIL) === false) {
            throw $this->throwError('Not an email address');
        }
    }
}
