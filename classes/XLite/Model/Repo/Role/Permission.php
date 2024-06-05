<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Role;

/**
 * Permission repository
 */
class Permission extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['code'],
    ];
}
