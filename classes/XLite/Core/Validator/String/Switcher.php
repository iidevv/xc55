<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\String;

/**
 * Switcher
 */
class Switcher extends \XLite\Core\Validator\TypeString
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

        if ($data !== '0' && $data !== '1' && $data !== '') {
            throw $this->throwError('Not a switcher');
        }
    }

    /**
     * Sanitaize
     *
     * @param mixed $data Daa
     *
     * @return array
     */
    public function sanitize($data)
    {
        return $data === '' ? null : (bool) $data;
    }
}
