<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Settings extends \XLite\View\AView
{
    use AftershipTrait;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets(): array
    {
        $list = parent::getAllowedTargets();
        $list[] = static::getMainConfigName();

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/settings/body.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/settings/style.less';

        return $list;
    }
}