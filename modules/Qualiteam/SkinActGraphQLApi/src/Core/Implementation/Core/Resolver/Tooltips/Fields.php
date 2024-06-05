<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips;

class Fields
{
    public function __construct(string $page)
    {
        $this->page = $page;
    }

    public function getTooltipsSchema()
    {
        return $this->getPrepareSchema()->getTooltipsSchema();
    }

    protected function getPrepareSchema()
    {
        return $this->getSchema()[$this->page] ?? new DefaultSchema();
    }

    protected function getSchema()
    {
        return [];
    }

    public function checkDefaultSchema()
    {
        return $this->getPrepareSchema() instanceof DefaultSchema;
    }
}