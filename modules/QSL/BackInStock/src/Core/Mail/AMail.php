<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core\Mail;

/**
 * AMail
 */
class AMail extends \XLite\Core\Mail\AMail
{
    public const MESSAGE_DIR = 'modules/QSL/BackInStock';

    /**
     * @return string
     */
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    /**
     * @return string
     */
    public static function getDir()
    {
        return static::MESSAGE_DIR;
    }

    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'product_name'          => static::t('Product name'),
            'product_url'           => static::t('Product URL'),
            'product_link'          => static::t('Product link'),
            'product_image'         => static::t('Product image'),
        ]);
    }

    /**
     * Unique hash each product
     *
     * @return array
     */
    protected function getHashData()
    {
        return array_merge(parent::getHashData(), [$this->getVariable('product_name')]);
    }
}
