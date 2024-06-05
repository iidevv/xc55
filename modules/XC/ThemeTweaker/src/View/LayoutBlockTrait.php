<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

/**
 * Using class MUST implement LayoutBlockInterface. Used for widget that accepts view list overrides in layout editor mode.
 */
trait LayoutBlockTrait
{
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'modules/XC/ThemeTweaker/layout_block/layout_block_controller.js',
            ]
        );
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
            static::PARAM_DISPLAY_GROUP         => new \XLite\Model\WidgetParam\TypeString('Widget display group', ''),
            static::PARAM_DISPLAY_NAME          => new \XLite\Model\WidgetParam\TypeString('Widget display name', ucwords($this->getDefaultDisplayName())),
            static::PARAM_LAYOUT_SETTINGS_LINK  => new \XLite\Model\WidgetParam\TypeString('Widget settings link', $this->getDefaultLayoutSettingsLink()),
            static::PARAM_LAYOUT_HELP_MESSAGE   => new \XLite\Model\WidgetParam\TypeString('Widget help message', $this->getDefaultLayoutHelpMessage()),
            static::PARAM_LAYOUT_BODY_ENTITY_ID => new \XLite\Model\WidgetParam\TypeString('Widget body data', $this->getDefaultLayoutBodyEntityId()),
            static::PARAM_LAYOUT_REMOVE_ID      => new \XLite\Model\WidgetParam\TypeString('Widget remove id', $this->getDefaultLayoutRemoveId()),
            static::PARAM_LAYOUT_LAZY_LOAD      => new \XLite\Model\WidgetParam\TypeBool('Widget lazy load', $this->getDefaultLayoutLazyLoad()),
            static::PARAM_IS_RELOADED_WIDGET    => new \XLite\Model\WidgetParam\TypeBool('Is reloaded widget', $this->getIsReloadedWidget())
        ];
    }

    protected function getDisplayGroup()
    {
        return $this->getParam(static::PARAM_DISPLAY_GROUP);
    }

    protected function getDisplayName()
    {
        return $this->getParam(static::PARAM_DISPLAY_NAME);
    }

    protected function getDefaultDisplayName()
    {
        return $this->getHead();
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutSettingsLink(): string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutHelpMessage(): string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutBodyEntityId(): string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutRemoveId(): string
    {
        return '';
    }

    protected function getDefaultLayoutLazyLoad(): bool
    {
        return false;
    }

    protected function getIsReloadedWidget(): bool
    {
        return true;
    }
}
