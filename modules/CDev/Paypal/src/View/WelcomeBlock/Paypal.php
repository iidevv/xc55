<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\WelcomeBlock;

use XCart\Extender\Mapping\ListChild;

/**
 * Paypal banner
 *
 * @ListChild (list="dashboard-center", zone="admin", weight="20")
 */
class Paypal extends \XLite\View\AWelcomeBlock
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['main']);
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/style.less';

        return $list;
    }

    protected function getInnerViewList()
    {
        return 'welcome-block.paypal';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/welcome_block/paypal';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isRootAccess()
            && $this->isNotHiddenByUser()
            && !$this->isHiddenByConfig()
            && $this->isTestMode();
    }

    /**
     * @return bool
     */
    protected function isHiddenByConfig()
    {
        return !\XLite\Core\Config::getInstance()->CDev->Paypal->show_admin_welcome;
    }

    protected function getBlockName()
    {
        return 'paypal';
    }

    /**
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return \CDev\Paypal\Main::getPaymentMethod(\CDev\Paypal\Main::PP_METHOD_PCP);
    }

    /**
     * @return bool
     */
    protected function isConfigured()
    {
        $method = $this->getPaymentMethod();

        return $method->getSetting('client_id')
            && $method->getSetting('client_secret');
    }

    /**
     * @return bool
     */
    protected function isTestMode()
    {
        $method = $this->getPaymentMethod();

        return $method->isTestMode($method);
    }

    /**
     * Returns paupal email
     *
     * @return string
     */
    protected function getPaypalEmail()
    {
        $method = \CDev\Paypal\Main::getPaymentMethod(\CDev\Paypal\Main::PP_METHOD_EC);

        return $method->getSetting('email');
    }
}
