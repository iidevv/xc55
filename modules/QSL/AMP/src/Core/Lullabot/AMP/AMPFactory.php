<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core\Lullabot\AMP;

use Lullabot\AMP\AMP;

/**
 * Create customized instances of Lullabot\AMP
 */
class AMPFactory
{
    /**
     * Create instance of AMP converter
     *
     * @return AMP
     */
    public static function createInstance()
    {
        $amp = new AMP();

        $amp->passes[array_search('Lullabot\AMP\Pass\ImgTagTransformPass', $amp->passes)] =
            'QSL\AMP\Core\Lullabot\AMP\Pass\ImgTagTransformPass';

        return $amp;
    }
}
