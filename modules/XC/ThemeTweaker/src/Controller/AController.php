<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * Payment method
 * @Extender\Mixin
 */
abstract class AController extends \XLite\Controller\AController
{
    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        parent::processRequest();

        if (
            !$this->suppressOutput
            && !$this->isAJAX()
            && ThemeTweaker::getInstance()->isInWebmasterMode()
            && $this->isDisplayHtmlTree()
        ) {
            $viewer = $this->getViewer();

            \XLite::getInstance()->addContent($viewer::getHtmlTree());
        }
    }

    protected function isDisplayHtmlTree()
    {
        return true;
    }

    /**
     * Retrieve AJAX output content from viewer
     *
     * @param mixed $viewer Viewer to display in AJAX
     *
     * @return string
     */
    protected function getAJAXOutputContent($viewer)
    {
        return parent::getAJAXOutputContent($viewer) . $viewer::getHtmlTree();
    }
}
