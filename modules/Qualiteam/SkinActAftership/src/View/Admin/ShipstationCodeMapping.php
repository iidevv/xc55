<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\Admin;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;

class ShipstationCodeMapping extends \XLite\View\AView
{
    use AftershipTrait;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = static::getCodeMappingConfigName();

        return $result;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/admin/code_mapping.twig';
    }
}
