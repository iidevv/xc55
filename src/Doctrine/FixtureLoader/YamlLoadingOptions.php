<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine\FixtureLoader;

final class YamlLoadingOptions
{
    private array $options = [];

    public function getOption($name)
    {
        return $this->options[$name] ?? null;
    }

    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    public function reset(): void
    {
        $this->options = [];
    }
}
