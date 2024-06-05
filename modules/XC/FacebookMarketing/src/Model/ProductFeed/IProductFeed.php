<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Model\ProductFeed;

interface IProductFeed
{
    /**
     * Return feed header row
     *
     * @return array
     */
    public function getHeaderRow();

    /**
     * Return extracted data from entity
     *
     * @param \XLite\Model\AEntity $model
     *
     * @return array
     */
    public function extractEntityData($model);

    /**
     * Return feed unique service name
     *
     * @return string
     */
    public function getServiceName();

    /**
     * Return file path
     *
     * @return string
     */
    public function getStoragePath();
}
