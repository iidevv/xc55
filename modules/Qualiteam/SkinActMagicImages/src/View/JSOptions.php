<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View;


use Qualiteam\SkinActMagicImages\Classes\Helper;
use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XCart\Extender\Mapping\ListChild;

/**
 * JS options
 *
 * @ListChild (list="jscontainer.js", zone="customer", weight="999999")
 *
 */
class JSOptions extends \XLite\View\AView
{
    use MagicImagesTrait;

    /**
     * Method to get Magic360 options
     *
     * @return string
     */
    public function getHTML()
    {
        $helper = Helper::getInstance();
        $tool   = $helper->getPrimaryTool();
        $tool->params->resetProfile();

        return $tool->getOptionsTemplate();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/templates/js_options.twig';
    }

    /**
     * Check visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $tool    = Helper::getInstance()->getPrimaryTool();
        $page    = static::getCurrentPageType();
        $enabled = $tool->params->checkValue('include-headers-on-all-pages', 'Yes') ||
            $tool->params->profileExists($page) && $tool->params->checkValue('enable-effect', 'Yes', $page);

        return parent::isVisible() && $enabled;
    }
}
