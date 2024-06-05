<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

/**
 * Code widget
 */
class Code extends \XC\ThemeTweaker\View\Custom
{
    public const PARAM_TYPE = 'type';

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/code';
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
            static::PARAM_TYPE => new \XLite\Model\WidgetParam\TypeString(
                'Code widget type',
                null,
                false
            ),
        ];
    }

    /**
     * Returns widget type
     * @return string
     */
    protected function getType()
    {
        return $this->getParam(static::PARAM_TYPE) ?: \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Code is used or not
     *
     * @return boolean
     */
    protected function isUsed()
    {
        return (bool) \XLite\Core\Config::getInstance()->XC->ThemeTweaker->{'use_' . $this->getType()};
    }

    /**
     * Return custom text
     *
     * @return boolean
     */
    protected function getUseCustomText()
    {
        return $this->getType() == 'custom_css'
            ? static::t('Use custom css')
            : static::t('Use custom js');
    }

    /**
     * Check if db-file content is different
     * (same as View/Backup isVisible)
     *
     * @return boolean
     */
    protected function hasDifferentContent()
    {
        $backup = $this->getBackupContent();

        return $backup
            && $backup != $this->getFileContent();
    }
}
