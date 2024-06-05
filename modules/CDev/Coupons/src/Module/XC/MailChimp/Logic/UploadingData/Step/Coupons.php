<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Module\XC\MailChimp\Logic\UploadingData\Step;

use XCart\Extender\Mapping\Extender;
use XC\MailChimp\Core\MailChimpECommerce;

/**
 * @Extender\Depend("XC\MailChimp")
 */
class Coupons extends \XC\MailChimp\Logic\UploadingData\Step\AStep
{
    /**
     * Process model
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        /** @var \XLite\Model\Product $model */

        foreach ($this->getStores() as $storeId) {
            MailChimpECommerce::getInstance()->createCoupon(
                $storeId,
                $model
            );
        }
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon');
    }

    /**
     * @param array $models
     *
     * @return mixed
     */
    protected function processBatch(array $models)
    {
    }
}
