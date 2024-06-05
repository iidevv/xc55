<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

interface IModuleRelatedEntity
{
    public function getModule(): ?string;

    public function setModule(string $module): void;
}
