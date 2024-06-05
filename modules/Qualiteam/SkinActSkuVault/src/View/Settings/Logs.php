<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\Settings;

class Logs extends ASettings
{
    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/logs.twig';
    }

    /**
     * Check - search block visible or not
     */
    protected function isSearchVisible(): bool
    {
        return true;
    }

    /**
     * Define attributes
     */
    protected function getAttributes(): array
    {
        return [
            'data-widget' => 'Qualiteam\SkinActSkuVault\View\Settings\Logs'
        ];
    }
}
