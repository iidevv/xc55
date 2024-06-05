<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Abstract dropdown button
 */
class ADropdown extends \XLite\View\Button\AButton
{
    use ExecuteCachedTrait;

    public const PARAM_DROP_DIRECTION   = 'dropDirection';
    public const PARAM_SHOW_CARET       = 'showCaret';
    public const PARAM_USE_CARET_BUTTON = 'useCaretButton';
    public const PARAM_IS_SINGLE_BUTTON = 'isSingleButton';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'button/js/dropdown.js';

        return $list;
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'button/css/dropdown.css';

        return $list;
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
            self::PARAM_DROP_DIRECTION   => new \XLite\Model\WidgetParam\TypeString('Drop direction', $this->getDefaultDropDirection()),
            self::PARAM_SHOW_CARET       => new \XLite\Model\WidgetParam\TypeBool('Show caret', $this->getDefaultShowCaret()),
            self::PARAM_USE_CARET_BUTTON => new \XLite\Model\WidgetParam\TypeBool('Use caret button', $this->getDefaultUseCaretButton()),
            self::PARAM_IS_SINGLE_BUTTON => new \XLite\Model\WidgetParam\TypeBool('Is single button', $this->getDefaultIsSingleButton()),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/dropdown.twig';
    }

    /**
     * @return string
     */
    protected function getDefaultDropDirection()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getDropDirection()
    {
        return $this->getParam(self::PARAM_DROP_DIRECTION);
    }

    /**
     * @return boolean
     */
    protected function getDefaultShowCaret()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function getShowCaret()
    {
        return $this->getParam(self::PARAM_SHOW_CARET);
    }

    /**
     * @return boolean
     */
    protected function getDefaultUseCaretButton()
    {
        return false;
    }

    /**
     * @return boolean
     */
    protected function getUseCaretButton()
    {
        return $this->getParam(self::PARAM_USE_CARET_BUTTON) && $this->getShowCaret();
    }

    /**
     * @return boolean
     */
    protected function getDefaultIsSingleButton()
    {
        return false;
    }

    /**
     * @return boolean
     */
    protected function isSingleButton()
    {
        return $this->getParam(self::PARAM_IS_SINGLE_BUTTON);
    }

    /**
     * Get additional buttons
     *
     * @return array
     */
    protected function getAdditionalButtons()
    {
        return $this->executeCachedRuntime(function () {
            return $this->prepareAdditionalButtons($this->defineAdditionalButtons());
        });
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [];
    }

    /**
     * @param array $additionalButtons
     *
     * @return array
     */
    protected function prepareAdditionalButtons($additionalButtons)
    {
        uasort($additionalButtons, static function ($a, $b) {
            $a = $a['position'];
            $b = $b['position'];

            if ($a === $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        });

        $result = [];
        foreach ($additionalButtons as $name => $additionalButton) {
            $result[$name] = $this->getWidget(
                $additionalButton['params'],
                $additionalButton['class'] ?? 'XLite\View\Button\Regular'
            );
        }

        return $result;
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ($this->getUseCaretButton() ? ' trigger-first-item' : '');
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('Click to expand menu');
    }
}
