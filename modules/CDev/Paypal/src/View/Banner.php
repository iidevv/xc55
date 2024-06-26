<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

use CDev\Paypal;

/**
 * Paypal banner
 *
 */
class Banner extends \XLite\View\Dialog
{
    public const PARAM_POSITION = 'position';
    public const PARAM_PAGE     = 'page';

    /**
     * Payment method
     *
     * @var \XLite\Model\Payment\Method
     */
    protected $method = null;

    /**
     * Get css files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Get publisher id
     *
     * @return string
     */
    protected function getPublisherId()
    {
        return $this->getSetting('publisherId');
    }

    /**
     * Get placement type
     *
     * @return string
     */
    protected function getPlacementType()
    {
        $placementType = '800x66';

        if ($this->getParam(static::PARAM_PAGE) == 'productDetailsPages') {
            $placementType = '468x60';
        }

        if ($this->getParam(static::PARAM_PAGE) == 'cartPage') {
            $placementType = '234x60';
        }

        return $placementType;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/banner';
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getCSSClasses()
    {
        return $this->getParam(static::PARAM_PAGE)
            . ' ' . ' position-' . $this->getParam(static::PARAM_POSITION);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PAGE     => new \XLite\Model\WidgetParam\TypeString('Page', ''),
            static::PARAM_POSITION => new \XLite\Model\WidgetParam\TypeString('Position', ''),
        ];
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getMethod()
    {
        if (is_null($this->method)) {
            $this->method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PC);
        }

        return $this->method;
    }

    /**
     * Get Paypal credit setting
     *
     * @param string $name Setting name
     *
     * @return string
     */
    protected function getSetting($name)
    {
        return $this->getMethod()->getSetting($name);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible()
            && (
                Paypal\Main::isPaypalCreditEnabled()
                || Paypal\Main::isPaypalWPSEnabled()
                || Paypal\Main::isPaypalCommercePlatformEnabled()
            )
            && $this->isValidTarget()
            && $this->getPublisherId()
            && $this->getSetting('agreement');

        if ($result) {
            $settingName = 'bannerOn' . ucfirst($this->getParam(static::PARAM_PAGE));

            $result = $this->getParam(static::PARAM_POSITION) == $this->getSetting($settingName);
        }

        return $result;
    }

    /**
     * Is valid target
     *
     * @return boolean
     */
    protected function isValidTarget()
    {
        $target = $this->getTarget();

        switch ($this->getParam(static::PARAM_PAGE)) {
            case 'homePage':
                $result = $target == 'main';
                break;

            case 'categoryPages':
                $result = $target == 'category';
                break;

            case 'productDetailsPages':
                $result = $target == 'product';
                break;

            case 'cartPage':
                $result = $target == 'cart';
                break;

            default:
                $result = false;
                break;
        }

        return $result;
    }
}
