<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Logic;

/**
 * We need this empty class just to make subscription fee taxable too
 *
 * @Extender\Depend("CDev\VAT")
 */
class IncludedVAT extends \CDev\VAT\Logic\IncludedVAT
{
}
