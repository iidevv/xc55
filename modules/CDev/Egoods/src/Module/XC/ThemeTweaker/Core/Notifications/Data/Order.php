<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Module\XC\ThemeTweaker\Core\Notifications\Data;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class Order extends \XC\ThemeTweaker\Core\Notifications\Data\Order
{
    protected function getTemplateDirectories()
    {
        return array_merge(parent::getTemplateDirectories(), [
            'modules/CDev/Egoods/egoods_links',
        ]);
    }

    public function getSuitabilityErrors($templateDir)
    {
        $errors = parent::getSuitabilityErrors($templateDir);

        /** @var \CDev\Egoods\Model\Order $order */
        $order = $this->getOrder($templateDir);

        if (
            $templateDir === 'modules/CDev/Egoods/egoods_links'
            && $order
            && !$order->getDownloadAttachments()
        ) {
            $errors[] = [
                'code'  => 'no_egoods',
                'value' => $order->getOrderNumber(),
                'type'  => 'warning'
            ];
        }

        return $errors;
    }
}
