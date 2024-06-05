<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Params;

interface SetPurchaseInterface
{
    const PARAM_PURCHASE_COUNTRY  = 'purchase_country';
    const PARAM_PURCHASE_CURRENCY = 'purchase_currency';

    /**
     * Set purchase country
     *
     * @return void
     */
    public function setPurchaseCountry(): void;

    /**
     * Set purchase currency
     *
     * @return void
     */
    public function setPurchaseCurrency(): void;
}