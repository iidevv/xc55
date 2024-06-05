<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View\FormField;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite\View\FormField\Label as MainLabel;

class Label extends MainLabel
{
    use MagicImagesTrait;

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/label.less';

        return $list;
    }
}