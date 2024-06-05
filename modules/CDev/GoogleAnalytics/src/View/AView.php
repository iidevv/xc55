<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View;

use XCart\Extender\Mapping\Extender;
use XLite\Controller\AController;
use CDev\GoogleAnalytics\Core\GA;

/**
 * @Extender\Mixin
 *
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView
{
    /**
     * Register JS files
     *
     * @return array
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), GA::getLibrary()->getJsList()->ecommerce);
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        /** @var AView|AController $this */
        if ($this->getTarget() === 'module') {
            $list[] = 'modules/CDev/GoogleAnalytics/style.css';
        }

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS] = array_merge($list[static::RESOURCE_JS], GA::getLibrary()->getJsList()->common);

        return $list;
    }
}
