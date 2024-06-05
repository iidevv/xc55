<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Layout manager
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    /**
     * @return string
     */
    public function getLogo()
    {
        $oldLogo = parent::getLogo();
        return dirname($oldLogo) . '/logo.svg';
    }

    /**
     * @return string
     */
    public function getMobileLogo()
    {
        $oldMobileLogo = parent::getMobileLogo();
        return dirname($oldMobileLogo) . '/mobile_logo.svg';
    }
    // /**
    //  * @return array
    //  */
    // protected function getSidebarFirstHiddenTargets()
    // {
    //     return array_merge(
    //         parent::getSidebarFirstHiddenTargets(),
    //         [
    //             'main',
    //             'search',
    //             'contact_us',
    //             'page',
    //             'order_list',
    //             'address_book',
    //             'profile',
    //             'messages',
    //             'login',
    //             'recover_password',
    //         ]
    //     );
    // }

    // /**
    //  * @return array
    //  */
    // protected function getSidebarSecondHiddenTargets()
    // {
    //     return array_merge(
    //         parent::getSidebarSecondHiddenTargets(),
    //         [
    //             'main',
    //             'search',
    //             'contact_us',
    //             'page',
    //             'order_list',
    //             'address_book',
    //             'profile',
    //             'messages',
    //             'login',
    //             'recover_password',
    //         ]
    //     );
    // }
}
