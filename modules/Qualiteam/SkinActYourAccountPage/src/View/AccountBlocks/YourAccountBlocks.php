<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * @ListChild (list="center", zone="customer", weight="100")
 */
class YourAccountBlocks extends \XLite\View\AView
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'your_account';

        return $list;
    }

    public function getCSSFiles(): array
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActYourAccountPage/your_account_page.css';
        return $list;
    }

    protected function getDefaultTemplate(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/YourAccountBlocks.twig';
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [
                Auth::getInstance()->getProfile()
                    ? Auth::getInstance()->getProfile()->getProfileId()
                    : 'no_profile',
                \XLite::getFormId()
            ]
        );
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }
}
