<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class MagicSet extends \XLite\View\AView
{
    use MagicImagesTrait;

    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), [(new MagicSet)->getTargetController()]);
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/product/magic_set.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/product/magic_set.less';

        return $list;
    }
}