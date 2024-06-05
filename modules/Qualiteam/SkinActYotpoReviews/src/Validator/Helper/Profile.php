<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Validator\Helper;

class Profile
{
    public function __construct(
        private \Qualiteam\SkinActYotpoReviews\Helpers\Profile $helper
    )
    {
    }

    public function isValidPhoneNumber(): bool
    {
        return strlen($this->helper->getCustomerPhoneNumber()) >= 11;
    }
}