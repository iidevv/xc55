<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class FrontPage extends \XLite\View\Model\FrontPage
{
    /**
     * Add useCustomOG field to the list of included fields
     *
     * @return array
     */
    protected function getIncludedFields()
    {
        $fields = parent::getIncludedFields();
        $fields[] = 'useCustomOG';

        return $fields;
    }
}
