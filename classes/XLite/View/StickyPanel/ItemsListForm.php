<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Panel form items list-based form
 */
class ItemsListForm extends \XLite\View\StickyPanel\ItemForm
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    public const SETTINGS_LINK_ARRAY_KEY_NAME = 'settings_link';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/stickyPanelModelList.js';

        return $list;
    }

    /**
     * Check panel has more actions buttons
     *
     * @return boolean
     */
    protected function hasMoreActionsButtons()
    {
        return true;
    }

    /**
     * Should more actions buttons be disabled?
     *
     * @return boolean
     */
    protected function isMoreActionsDisabled()
    {
        return $this->hasMoreActionsButtons();
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if ($this->getAdditionalButtons()) {
            $list['additional'] = $this->getWidget(
                [
                    'template' => 'items_list/model/additional_buttons.twig',
                ]
            );
        }

        return $list;
    }

    /**
     * Define buttons widgets that go the very end
     */
    protected function defineLastButtons(): array
    {
        $list = parent::defineLastButtons();

        // Setting link will be added here via indirect way
        if ($settingLink = $this->getModuleSettingURL()) { // here is the pointB to override the method by modules
            $list = $this->addSettingLink($list, $settingLink);
        }

        return $list;
    }

    /**
     * General way to generate link to the setting page using provided module's main class name
     */
    protected function getModuleSettingURL(): string
    {
        $moduleSettingClassName = $this->getSettingLinkClassName(); // here is the pointA to override the method by modules

        if (
            !$moduleSettingClassName
            || !class_exists($moduleSettingClassName)
        ) {
            return '';
        }

        if (
            method_exists($moduleSettingClassName, 'getSettingsForm')
            && ($url = $moduleSettingClassName::getSettingsForm())
        ) {
            // Some modules have special link to setting page
            return $url;
        } elseif (
            method_exists($moduleSettingClassName, 'getId')
            && ($moduleId = $moduleSettingClassName::getId())
        ) {
            // fallback to usual link
            return $this->buildURL('module', '', ['moduleId' => $moduleId]);
        } else {
            return '';
        }
    }

    /**
     * Indirect way to add setting link to sticky panel.
     * A module class which may define moduleid (getSettingsForm/getId),
     *   if so, the setting link will be added to sticky panel
     */
    protected function getSettingLinkClassName(): string
    {
        return '';
    }

    /**
     * Can be called directly from modules or indirectly from this class
     */
    protected function addSettingLink(array $list, string $settingLink): array
    {
        if (\XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)) {
            $list[static::SETTINGS_LINK_ARRAY_KEY_NAME] = new \XLite\View\Button\SimpleLink(
                [
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Settings'),
                    \XLite\View\Button\Link::PARAM_LOCATION => $settingLink,
                ]
            );
        }

        return $list;
    }

    /**
     * Flag to display OR label
     *
     * @return boolean
     */
    protected function isDisplayORLabel()
    {
        return true;
    }

    /**
     * Returns "more actions" specific label
     *
     * @return string
     */
    protected function getMoreActionsText()
    {
        return static::t('More actions for selected');
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
     * These buttons will be composed into dropup menu.
     * The divider button is also available: \XLite\View\Button\Dropdown\Divider
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

            return $a > $b ? 1 : -1;
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
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();
        $class = trim($class) . ' model-list';

        if ($this->getAdditionalButtons()) {
            $class .= ' has-add-buttons';
        }

        if ($this->getModuleSettingURL()) {
            $class .= ' has-settings-url';
            if (!in_array('always-visible', explode(' ', $class), true)) {
                $class .= ' always-visible';
            }
        }

        return $class;
    }
}
