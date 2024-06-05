<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * ThemeTweaker controller
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Customer\Product
{
    /**
     * Process an action 'preview'
     *
     * @return void
     */
    public function doActionPreview()
    {
        if (Request::getInstance()->activate_editor) {
            ThemeTweaker::getInstance()->setCurrentMode(ThemeTweaker::MODE_LAYOUT_EDITOR);

            $this->setReturnURL($this->getURL(['action' => 'preview']));
        }

        parent::doActionPreview();
    }

    /**
     * Get controller parameters
     * TODO - check this method
     * FIXME - backward compatibility
     *
     * @param string $exceptions Parameter keys string OPTIONAL
     *
     * @return array
     */
    public function getAllParams($exceptions = null)
    {
        $params = parent::getAllParams($exceptions);

        if (
            Request::getInstance()->activate_editor
            && ThemeTweaker::getInstance()->getCurrentMode() === ThemeTweaker::MODE_LAYOUT_EDITOR
        ) {
            unset($params['activate_editor']);
        }

        return $params;
    }
}
