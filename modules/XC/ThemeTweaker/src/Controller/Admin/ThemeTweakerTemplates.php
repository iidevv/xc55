<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XCart\Operation\Service\ViewListRefresh;

/**
 * Theme tweaker templates controller
 */
class ThemeTweakerTemplates extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'switch';

        return $list;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Customization');
    }

    /**
     * Returns link to store front
     *
     * @return string
     */
    public function getStoreFrontLink()
    {
        $styleClass = \XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->isInWebmasterMode()
            ? ''
            : 'hidden';

        $button = new \XLite\View\Button\SimpleLink([
            \XLite\View\Button\SimpleLink::PARAM_LABEL => 'Open storefront',
            \XLite\View\Button\SimpleLink::PARAM_LOCATION => $this->getShopURL(),
            \XLite\View\Button\SimpleLink::PARAM_BLANK => true,
            \XLite\View\Button\SimpleLink::PARAM_STYLE => $styleClass,
        ]);

        return $button->getContent();
    }

    /**
     * Update list
     */
    protected function doActionUpdate()
    {
        $list = new \XC\ThemeTweaker\View\ItemsList\Model\Template();
        $list->processQuick();

        $viewListRefresh = \XCart\Container::getContainer()->get(ViewListRefresh::class);
        ($viewListRefresh)();

        $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
        $cacheDriver->deleteAll();
    }

    /**
     * Switch state
     * TODO: REMOVE. SWITCHER IS IN ThemeTweaker controller
     */
    protected function doActionSwitch()
    {
        $value = !\XLite\Core\Config::getInstance()->XC->ThemeTweaker->edit_mode;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            [
                'category' => 'XC\ThemeTweaker',
                'name'     => 'edit_mode',
                'value'    => $value,
            ]
        );

        \XLite\Core\TopMessage::addInfo(
            $value
                ? 'Webmaster mode is enabled'
                : 'Webmaster mode is disabled'
        );

        $this->setReturnURL($this->buildURL('theme_tweaker_templates'));
    }
}
