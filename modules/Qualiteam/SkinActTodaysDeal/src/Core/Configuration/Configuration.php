<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActTodaysDeal\Core\Configuration;

class Configuration
{

    /**
     * @param string $td_name
     * @param int $td_category
     * @param int    $td_limit
     */
    public function __construct(
        private string $td_name,
        private int $td_category,
        private int $td_limit
    )
    {
    }

    public function getName(): string
    {
        return $this->td_name;
    }

    public function getCategoryId(): ?int
    {
        return $this->td_category ?: null;
    }

    public function getLimit(): int
    {
        return $this->td_limit;
    }
}