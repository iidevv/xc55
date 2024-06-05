<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core;

use XC\ThemeTweaker\View\ThemeTweakerPanel;

/**
 * Request
 */
class ThemeTweaker extends \XLite\Base\Singleton
{
    public const MODE_LAYOUT_EDITOR = 'layout_editor';
    public const MODE_LABELS_EDITOR = 'labels_editor';
    public const MODE_WEBMASTER = 'webmaster';
    public const MODE_CUSTOM_CSS = 'custom_css';
    public const MODE_INLINE_EDITOR = 'inline_editor';

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInLayoutMode()
    {
        return $this->isInMode(self::MODE_LAYOUT_EDITOR)
            && $this->canRunThemeTweaker()
            && !\XLite::isAdminZone();
    }

    /**
     * Are we on the checkout page.
     *
     * @return bool
     */
    public static function isInCheckout()
    {
        $controller = \XLite::getController();
        return method_exists($controller, 'isCheckoutLayout') && $controller->isCheckoutLayout();
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInWebmasterMode()
    {
        $conditions = $this->getDefaultRunConditions();
        unset($conditions['not_post'], $conditions['not_ajax']);

        $editorAllowed = \XLite::isAdminZone()
            ? static::isAdminTargetAllowedInWebmasterMode()
            : static::isTargetAllowedInWebmasterMode() && $this->isInMode(self::MODE_WEBMASTER);

        return $this->canRunThemeTweaker($conditions)
            && $editorAllowed;
    }

    /**
     * @return boolean
     */
    public function isInEmailTemplateMode()
    {
        $conditions = $this->getDefaultRunConditions();
        unset($conditions['not_post'], $conditions['not_ajax'], $conditions['theme_tweaker_enabled']);

        return $this->canRunThemeTweaker($conditions)
            && \XLite::isAdminZone()
            && static::isAdminTargetAllowedInWebmasterMode();
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isTargetAllowedInWebmasterMode()
    {
        return \XLite\Core\Request::getInstance()->target !== 'image';
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isAdminTargetAllowedInWebmasterMode()
    {
        return \XLite\Core\Request::getInstance()->target === 'notification_editor';
    }

    /**
     * Check if inline editor mode is available
     *
     * @return boolean
     */
    public function isInInlineEditorMode()
    {
        return $this->isInMode(self::MODE_LAYOUT_EDITOR)
            && static::isTargetAllowedInInlineEditorMode()
            && $this->canRunThemeTweaker()
            && !\XLite::isAdminZone();
    }

    /**
     * Check target allowed
     *
     * @return boolean
     */
    public static function isTargetAllowedInInlineEditorMode()
    {
        $targets = ['product', 'category', 'page', 'main'];

        return in_array(\XLite\Core\Request::getInstance()->target, $targets, true);
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInLabelsMode()
    {
        return $this->isInMode(self::MODE_LABELS_EDITOR)
            && $this->canRunThemeTweaker()
            && !\XLite::isAdminZone();
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    public function isInCustomCssMode()
    {
        return $this->isInMode(self::MODE_CUSTOM_CSS)
            && $this->canRunThemeTweaker()
            && !\XLite::isAdminZone();
    }

    /**
     * @return boolean
     */
    public function isPanelExpanded()
    {
        $cookies = \XLite\Core\Request::getInstance()->getCookieData();

        return !isset($cookies['ThemeTweaker_isExpanded']) || isset($cookies['ThemeTweaker_isExpanded']) && $cookies['ThemeTweaker_isExpanded'] === 'true';
    }

    /**
     * Get current mode
     * @return string
     */
    public function getCurrentMode()
    {
        return \XLite\Core\Session::getInstance()->themetweaker_mode ?: self::MODE_LAYOUT_EDITOR;
    }

    /**
     * Set current mode
     */
    public function setCurrentMode($mode)
    {
        if ($mode === null || in_array($mode, $this->getAvailableModes(), true)) {
            \XLite\Core\Session::getInstance()->themetweaker_mode = $mode;
            \XLite\Core\Session::getInstance()->themetweaker_cache_key = uniqid();
        }
    }

    /**
     * Get available modes
     * @return string
     */
    public function getAvailableModes()
    {
        return [
            self::MODE_LAYOUT_EDITOR,
            self::MODE_LABELS_EDITOR,
            self::MODE_WEBMASTER,
            self::MODE_CUSTOM_CSS,
            self::MODE_INLINE_EDITOR
        ];
    }

    /**
     * Check if is in the specific mode
     * @return boolean
     */
    public function isInMode($mode)
    {
        return $this->getCurrentMode() === $mode;
    }

    /**
     * Checks if themetweaker mode can be run
     * @param array $conditions Array of callables to check for (should return false if the mode cannot be run)
     * @return bool
     */
    public function canRunThemeTweaker($conditions = null)
    {
        if ($conditions === null) {
            $conditions = $this->getDefaultRunConditions();
        }

        return !in_array(false, $conditions, true);
    }

    /**
     * @return array
     */
    protected function getDefaultRunConditions()
    {
        return [
            'not_post'              => !\XLite\Core\Request::getInstance()->isPost(),
            'not_cli'               => !\XLite\Core\Request::getInstance()->isCLI(),
            'not_ajax'              => !\XLite\Core\Request::getInstance()->isAJAX(),
            'theme_tweaker_enabled' => ThemeTweakerPanel::isThemeTweakerEnabled(),
            'user_allowed'          => static::isUserAllowed(),
            'not_rebuilding'        => true
        ];
    }

    /**
     * Check user allowed
     *
     * @return boolean
     */
    public static function isUserAllowed()
    {
        $auth = \XLite\Core\Auth::getInstance();

        return $auth->getProfile()
            && $auth->getProfile()->isAdmin()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Checks checkbox config value and casts to proper boolean
     *
     * @param $value
     * @return bool
     */
    public static function castCheckboxValue($value)
    {
        return $value === true || $value === 'true' || $value === '1' || $value === 'Y';
    }
}
