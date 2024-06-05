<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Profile;

class EnterNewPassword extends \XLite\View\Form\Profile\ForceChangePassword
{
    /**
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'recover_password';
    }

    /**
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'set_new_password';
    }
}
