<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints;

class DynamicUrl
{
    /**
     * @var string
     */
    private string $param = '';

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return sprintf('%s/%s', $this->path, $this->param);
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setParam(string $value): void
    {
        $this->param = $value;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }
}