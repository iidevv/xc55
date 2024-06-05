<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\FormField\Input;

use XLite\Core\Database;
use XLite\Core\Skin;

/**
 * Logo
 */
class MobileLogo extends \CDev\SimpleCMS\View\FormField\Input\AImage
{
    /**
     * @return boolean
     */
    protected function hasAlt()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getFieldLabelTemplate()
    {
        return 'form_field/label/logo_label.twig';
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $mobileLogoSettings = Database::getRepo(\XLite\Model\ImageSettings::class)->findOneByRecord(
            [
                'code' => 'Mobile',
                'model' => 'XLite\Model\Image\Common\Logo',
                'moduleName' => Skin::getInstance()->getCurrentSkinModuleId()
            ]
        );

        if ($mobileLogoSettings) {
            $this->widgetParams[static::PARAM_HELP]->setValue(static::t(
                'Current logo sizes (mobile): XхY px',
                [
                    'X' => $mobileLogoSettings->getWidth(),
                    'Y' => $mobileLogoSettings->getHeight()
                ]
            ));
        }
    }

    /**
     * @return boolean
     */
    protected function isViaUrlAllowed()
    {
        return false;
    }
}
