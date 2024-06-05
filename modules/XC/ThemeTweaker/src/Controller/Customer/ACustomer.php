<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XC\ThemeTweaker\Core;

/**
 * Abstract controller for Customer interface
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    protected function doNoAction()
    {
        if (Request::getInstance()->activate_mode) {
            Core\ThemeTweaker::getInstance()->setCurrentMode(Request::getInstance()->activate_mode);
        }

        parent::doNoAction();
    }


    /**
     * Get controller parameters
     * TODO - check this method
     * FIXME - backward compatibility
     *
     * @param string $exceptions Parameter keys string OPTIONAL
     *
     * @return array
     */
    public function getAllParams($exceptions = null)
    {
        $params = parent::getAllParams($exceptions);

        if (
            Request::getInstance()->activate_mode
            && Core\ThemeTweaker::getInstance()->getCurrentMode() === Request::getInstance()->activate_mode
        ) {
            unset($params['activate_mode']);
        }

        return $params;
    }

    /**
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        if (Core\ThemeTweaker::getInstance()->isPanelExpanded()) {
            $classes[] = 'tweaker_panel_expanded';
        }

        return $classes;
    }

    /**
     * @return bool
     */
    public function isInInlineEditorMode()
    {
        return Core\ThemeTweaker::getInstance()->isInInlineEditorMode();
    }

    /**
     * @return bool
     */
    public function isInLayoutMode()
    {
        return Core\ThemeTweaker::getInstance()->isInLayoutMode();
    }

    /**
     * @return bool
     */
    public function isInWebmasterMode()
    {
        return Core\ThemeTweaker::getInstance()->isInWebmasterMode();
    }

    /**
     * @return bool
     */
    public function isInLabelsMode()
    {
        return Core\ThemeTweaker::getInstance()->isInLabelsMode();
    }

    /**
     * @return bool
     */
    public function isInCustomCssMode()
    {
        return Core\ThemeTweaker::getInstance()->isInCustomCssMode();
    }
}
