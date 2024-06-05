<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * User profile page controller
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    protected function isWishlistClicked()
    {
        return \XLite\Core\Request::getInstance()->fromURL === $this->buildURL('wishlist');
    }

    /**
     * Postprocess register action (success)
     */
    protected function postprocessActionRegisterSuccess()
    {
        parent::postprocessActionRegisterSuccess();

        $this->postprocessActionRegisterSuccessWishlistActions();
    }

    protected function postprocessActionRegisterSuccessWishlistActions()
    {
        if (
            !$this->isActionError()
            && $this->isWishlistClicked()
        ) {
            $this->setHardRedirect(false);
            $this->setSilenceClose(true);
            \XLite\Core\Event::doReloadPage();
        }
    }
}
