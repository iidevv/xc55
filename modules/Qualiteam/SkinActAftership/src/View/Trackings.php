<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XCart\Extender\Mapping\ListChild;

/**
 * Trackings
 *
 * @ListChild (list="center", zone="customer")
 */
class Trackings extends \XLite\View\SimpleDialog
{
    use AftershipTrait;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'trackings';

        return $list;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody(): string
    {
        return $this->getModulePath() . '/trackings/body.twig';
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles(): array
    {
        $list                         = parent::getCommonFiles();
        $list[static::RESOURCE_CSS][] = $this->getModulePath() . '/trackings/style.less';

        return $list;
    }
}