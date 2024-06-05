<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\Action\Base\AAction;

/**
 * @Extender\Mixin
 *
 * Main
 */
class Main extends \XLite\View\Product\Details\Customer\Page\Main
{
    /**
     * Get container attributes
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getContainerAttributes()
    {
        $list = parent::getContainerAttributes();

        $action = new Action\ProductInfo(
            $this->getProduct()
        );

        $params = AAction::RETURN_PART_TYPE | AAction::RETURN_PART_DATA | AAction::FORMAT_JSON;
        if ($actionData = $action->getActionData($params)) {
            $list['data-ga-ec-action'] = $actionData;
        }

        return $list;
    }
}
