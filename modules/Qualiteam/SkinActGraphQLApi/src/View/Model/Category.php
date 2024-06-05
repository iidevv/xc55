<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\View\Model\Category
{
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->schemaDefault['showInMobileApp'] = [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL => \XLite\Core\Translation::lbl('SkinActGraphQLApi showInMobileApp'),
            self::SCHEMA_REQUIRED => false,
        ];
    }

}