<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->relatedTargets['access_filters'][] = 'gdpr';
        $this->relatedTargets['https_settings'][] = 'gdpr';
    }
}
