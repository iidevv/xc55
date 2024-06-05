<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation;

use XcartGraphqlApi\OutputInterface;

/**
 * Class Output
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation
 */
class Output implements OutputInterface
{
    /**
     * @param int   $code
     * @param array $data
     */
    public function output($code, array $data)
    {
        header('Content-Type: application/json', true, $code);

        echo json_encode($data);
    }
}
