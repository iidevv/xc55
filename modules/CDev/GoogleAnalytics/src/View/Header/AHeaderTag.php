<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\Header;

use XLite;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeCollection;
use XLite\Model\WidgetParam\TypeString;
use XLite\View\AView;

/**
 * Header declaration (abstract)
 */
abstract class AHeaderTag extends AView
{
    public const PARAM_MEASUREMENT_ID    = 'measurement_id';
    public const PARAM_SCRIPT_URL        = 'script_url';
    public const PARAM_JS_SETTINGS       = 'js_settings';
    public const PARAM_ECOMMERCE_ENABLED = 'ecommerce_enabled';
    public const PARAM_DEBUG_ENABLED     = 'debug_enabled';
    public const PARAM_TRACKER_CONFIG    = 'tracker_config';
    public const PARAM_DISABLE_TRACKING  = 'disable_tracking';

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_MEASUREMENT_ID    => new TypeString('Target Resource Id', ''),
            self::PARAM_SCRIPT_URL        => new TypeString('Source URL', ''),
            self::PARAM_JS_SETTINGS       => new TypeCollection('JS Data Settings', []),
            self::PARAM_ECOMMERCE_ENABLED => new TypeBool('Is Ecommerce Enabled', false),
            self::PARAM_DEBUG_ENABLED     => new TypeBool('Is Debug Enabled', false),
            self::PARAM_TRACKER_CONFIG    => new TypeCollection('Tracker Config', []),
            self::PARAM_DISABLE_TRACKING  => new TypeBool('Is Tracking Disabled', false),
        ];
    }

    /**
     * @return bool
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function isVisible()
    {
        return $this->isActive();
    }

    public function isActive(): bool
    {
        return ($this->isVisibleForCustomer() && !XLite::isAdminZone())
            || ($this->isVisibleForAdmin() && XLite::isAdminZone());
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisibleForCustomer(): bool
    {
        return true;
    }

    protected function isVisibleForAdmin(): bool
    {
        return false;
    }

    protected function isTrackingDisabled(): bool
    {
        return $this->getParam(static::PARAM_DISABLE_TRACKING);
    }
}
