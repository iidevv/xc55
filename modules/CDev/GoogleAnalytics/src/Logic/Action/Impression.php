<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action;

use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action;

class Impression extends Action\Base\AProduct
{
    protected static function getActionType(): string
    {
        return 'impression';
    }

    public function isApplicable(): bool
    {
        return parent::isApplicable()
            && $this->listName;
    }

    protected function buildRequestData(): array
    {
        return GA::getProductDataMapper()->getData(
            $this->product,
            $this->listName,
            $this->position
        );
    }

    protected function buildContext(): array
    {
        return array_merge_recursive(parent::buildContext(), [
            'list' => $this->listName,
        ]);
    }
}
