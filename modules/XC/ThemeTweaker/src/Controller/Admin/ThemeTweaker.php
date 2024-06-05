<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

/**
 * ThemeTweaker controller
 */
class ThemeTweaker extends \XLite\Controller\Admin\AAdmin
{
    protected function doActionSwitchMode()
    {
        $mode = \XLite\Core\Request::getInstance()->mode ?: null;

        \XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->setCurrentMode($mode);
        $returnUrl = \Includes\Utils\URLManager::getUrlWithoutParam($this->getReferrerURL(), 'activate_mode');
        $this->setReturnURL($returnUrl);
        $this->setHardRedirect(true);
    }

    /**
     * Disable editor
     *
     * @return void
     */
    protected function doActionDisable()
    {
        \XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->setCurrentMode(null);

        $this->set('silent', true);
        $this->suppressOutput = true;
    }

    /**
     * Panel tour flag
     *
     * @return void
     */
    protected function doActionTourShown()
    {
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
            'category' => 'XC\ThemeTweaker',
            'name'     => 'tour_shown',
            'value'    => true
        ]);

        $this->set('silent', true);
        $this->suppressOutput = true;
    }
}
