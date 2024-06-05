<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActDocumentsTab\Trait;

/**
 * Trait documents tab
 */
trait DocumentsTabTrait
{
    /**
     * Get module path
     *
     * @return string
     */
    public function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActDocumentsTab';
    }

    /**
     * Get documents label
     *
     * @return string
     */
    public function getDocumentsTabLabel(): string
    {
        return 'Documents';
    }
}