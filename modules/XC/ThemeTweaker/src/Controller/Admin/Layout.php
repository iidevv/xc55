<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * Layout
 * @Extender\Mixin
 */
class Layout extends \XLite\Controller\Admin\Layout
{
    /**
     * Returns link to store front
     *
     * @return string
     */
    public function getStoreFrontLink()
    {
        $styleClass = ThemeTweaker::getInstance()->isInWebmasterMode()
            ? ''
            : 'hidden';

        $button = new \XLite\View\Button\SimpleLink([
            \XLite\View\Button\SimpleLink::PARAM_LABEL    => 'Open storefront',
            \XLite\View\Button\SimpleLink::PARAM_LOCATION => $this->getShopURL(),
            \XLite\View\Button\SimpleLink::PARAM_BLANK    => true,
            \XLite\View\Button\SimpleLink::PARAM_STYLE    => $styleClass,
        ]);

        return $button->getContent();
    }

    /**
     * Add warning after template is changed if custom CSS was defined and enabled
     *
     * @return void
     */
    protected function doActionChangeTemplate()
    {
        parent::doActionChangeTemplate();

        if (ThemeTweaker::castCheckboxValue(\XLite\Core\Config::getInstance()->XC->ThemeTweaker->use_custom_css)) {
            $content = \Includes\Utils\FileManager::read(
                \XC\ThemeTweaker\Main::getThemeDir() . 'custom.css'
            );
            if (!empty($content)) {
                \XLite\Core\TopMessage::getInstance()->addWarning(
                    'There are some custom CSS styles in your store. These styles may affect the look of the installed template. Review the custom styles and disable them if necessary.',
                    ['url' => $this->buildURL('custom_css')]
                );
            }
        }

        if ($this->isShowTemplatesWarning()) {
            \XLite\Core\TopMessage::getInstance()->addWarning(
                'There are some custom templates in your store that may contain skin dependent code.',
                ['templates_url' => $this->buildURL('theme_tweaker_templates')]
            );
        }
    }

    /**
     * @return bool
     */
    protected function isShowTemplatesWarning()
    {
        return \XLite\Core\Database::getRepo('XC\ThemeTweaker\Model\Template')->count() > 0;
    }
}
