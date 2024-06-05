<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

interface LayoutBlockInterface
{
    public const PARAM_DISPLAY_GROUP         = 'displayGroup';
    public const PARAM_DISPLAY_NAME          = 'displayName';
    public const PARAM_LAYOUT_SETTINGS_LINK  = 'layoutSettingsLink';
    public const PARAM_LAYOUT_HELP_MESSAGE   = 'layoutHelpMessage';
    public const PARAM_LAYOUT_BODY_ENTITY_ID = 'layoutBodyEntityId';
    public const PARAM_LAYOUT_REMOVE_ID      = 'layoutRemoveId';
    public const PARAM_LAYOUT_LAZY_LOAD      = 'layoutLazyLoad';
    public const PARAM_IS_RELOADED_WIDGET    = 'isReloadedWidget';
    public const DISPLAY_GROUP_MAIN          = 'layout.main';
    public const DISPLAY_GROUP_CENTER        = 'center';
    public const DISPLAY_GROUP_SIDEBAR       = 'sidebar';
}
