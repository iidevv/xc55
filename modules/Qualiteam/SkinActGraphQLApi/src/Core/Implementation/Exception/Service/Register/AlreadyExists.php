<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service\Register;

use GraphQL\Error\UserError;

class AlreadyExists extends UserError
{
    public function __construct($login)
    {
        parent::__construct('The ' . $login . ' profile is already registered. Please, try some other email address.');
    }
}
