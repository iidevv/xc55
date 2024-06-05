<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

interface GeneratorInterface
{
    /**
     * Get tick duration flag name
     *
     * @return string
     */
    public static function getTickDurationVarName();

    /**
     * Get cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName();

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName();
}
