<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\HTMLPurifierModules;

use HTMLPurifier_AttrDef_Enum;
use HTMLPurifier_AttrDef_URI;
use HTMLPurifier_Config;
use HTMLPurifier_HTMLModule_Scripting;

class Scripting extends HTMLPurifier_HTMLModule_Scripting
{
    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        parent::setup($config);

        $this->info['script']->attr = [
            'defer' => new HTMLPurifier_AttrDef_Enum(['defer']),
            'src' => new HTMLPurifier_AttrDef_URI(true),
            'type' => new HTMLPurifier_AttrDef_Enum(['text/javascript', 'application/ld+json'])
        ];
    }
}
