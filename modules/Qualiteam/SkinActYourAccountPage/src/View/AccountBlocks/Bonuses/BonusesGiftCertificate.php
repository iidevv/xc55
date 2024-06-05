<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\Bonuses;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("RedSqui\GiftCertificates")
 */
class BonusesGiftCertificate extends Bonuses
{
    /**
     * Get bonuses gift certificate url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('gift_certs'),
                    'text' => static::t('SkinActYourAccountPage gift certificate'),
                    'is_count' => false,
                    'position' => 1
                ]
            ]
        );
    }
}