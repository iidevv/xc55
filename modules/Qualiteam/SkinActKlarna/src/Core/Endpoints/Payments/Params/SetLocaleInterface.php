<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params;

interface SetLocaleInterface
{
    const PARAM_LOCALE = 'locale';

    /**
     * Set locale
     *
     * @return void
     */
    public function setLocale(): void;
}