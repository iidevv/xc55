<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action\Base;

use XLite\Core\Session;
use CDev\GoogleAnalytics\Core\GA;

abstract class ABackendAction extends AAction
{
    protected static function getActionType(): ?string
    {
        return null;
    }

    /**
     * @param mixed $item
     * @param mixed $key
     *
     * @return bool
     */
    protected static function filterCallback($item, $key): bool
    {
        if (in_array($key, ['value', 'items'])) {
            return true;
        }

        return parent::filterCallback($item, $key);
    }

    public function isApplicable(): bool
    {
        return parent::isApplicable() && $this->isBackendApplicable();
    }

    public function isBackendApplicable(): bool
    {
        return true;
    }

    public function getClientId(): string
    {
        if (!Session::getInstance()->ga_uuid) {
            /** @noinspection NonSecureUniqidUsageInspection Backward compat */

            Session::getInstance()->ga_uuid = uniqid();
        }


        return Session::getInstance()->ga_uuid;
    }

    protected function isValid(): bool
    {
        return GA::getResource()->isECommerceEnabled();
    }
}
