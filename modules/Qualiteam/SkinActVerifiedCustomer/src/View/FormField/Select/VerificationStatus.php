<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\View\FormField\Select;

use Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo;

/**
 * Order messages selector
 */
class VerificationStatus extends \XLite\View\FormField\Select\Regular
{
    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        //"Any status", "Not verified only" and "Verified only".
        return [
            '' => static::t('SkinActVerifiedCustomer Any status'),
            VerificationInfo::STATUS_VERIFIED => static::t('SkinActVerifiedCustomer Verified only'),
            VerificationInfo::STATUS_NOT_VERIFIED => static::t('SkinActVerifiedCustomer Not verified only'),
        ];
    }
}
