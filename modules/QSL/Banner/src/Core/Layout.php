<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    /**
     * Prepare JS resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareJSResources(array $resources)
    {
        $resources = parent::prepareJSResources($resources);

        $ignoreCycle2Lib = Request::getInstance()->ignoreCycle2Lib;
        $deleteFile      = false;

        foreach ($resources as $key => $value) {
            if (isset($resources[$key]['file'])) {
                if (preg_match('/jquery.cycle2.min.js$/', $resources[$key]['file'])) {
                    if ($deleteFile || $ignoreCycle2Lib) {
                        unset($resources[$key]);
                    }

                    $deleteFile = true;
                }
            }
        }

        return $resources;
    }
}
