<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core;

class TopMessage extends \XLite\Core\TopMessage
{
    public const YOTPO_ERROR   = 'yotpo error';

    protected function __construct()
    {
        parent::__construct();

        $this->types[] = self::YOTPO_ERROR;
    }

    /**
     * Add yotpo error-type message without applying translation
     *
     * @param string $text      Label name
     * @param array  $arguments Substitution arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return boolean
     */
    public static function addYotpoRawError($text, array $arguments = [], $code = null)
    {
        return static::getInstance()->add($text, $arguments, $code, static::YOTPO_ERROR, true);
    }

    /**
     * Add yotpo error-type message with additional translation arguments
     *
     * @param string $text      Label name
     * @param array  $arguments Substitution arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return boolean
     */
    public static function addYotpoError($text, array $arguments = [], $code = null)
    {
        return static::getInstance()->add($text, $arguments, $code, static::YOTPO_ERROR);
    }
}