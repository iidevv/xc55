<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi;

/**
 * Class Context
 * @package XcartGraphqlApi
 */
interface ContextInterface
{
    /**
     * @return mixed
     */
    public function getAuthToken();

    /**
     * @return mixed
     */
    public function getLanguage();

    /**
     * @return mixed
     */
    public function getCurrency();
}
