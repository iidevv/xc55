<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Profile operation
 */
class ProfileOperations extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        $targets = parent::getAllowedTargets();

        return array_merge($targets, \XLite\View\Tabs\AdminProfile::getAllowedTargets());
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        $result = false;

        foreach ($this->getAdditionalButtons() as $button) {
            if ($button->isVisible()) {
                $result = true;
                break;
            }
        }

        return $result && parent::isVisible();
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'operateAdmin' => [
                'class'    => 'XLite\View\Button\LoginAsAdmin',
                'params'   => [],
                'position' => 50,
            ],
            'operate'      => [
                'class'    => 'XLite\View\Button\OperateAsThisUser',
                'params'   => [],
                'position' => 200,
            ],
        ];
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Profile actions';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'profile/profile_operations.less';

        return $list;
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'profile-actions always-enabled';
    }

    /**
     * @return boolean
     */
    protected function isSingleButton()
    {
        return $this->getParam(self::PARAM_IS_SINGLE_BUTTON);
    }
}
