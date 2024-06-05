<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Domain;

final class StaticConfigDomain
{
    private array $config;

    private array $original;

    public function __construct(
        array $config,
        array $original
    ) {
        $this->config = $config;
        $this->original = $original;
    }

    public function getOption(array $names)
    {
        $section = array_shift($names);
        $variable = array_shift($names);

        $sectionData = $this->config[$section] ?? [];

        if ($variable) {
            return $sectionData[$variable] ?? null;
        }

        return $sectionData;
    }

    public function setOption(array $names, $value): void
    {
        $section = array_shift($names);
        $variable = array_shift($names);

        $this->config[$section] ??= [];
        if ($variable) {
            $this->config[$section][$variable] = $value;
        } else {
            $this->config[$section] = $value;
        }
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getOriginal(): array
    {
        return $this->original;
    }

    public function setOriginal(array $original): void
    {
        $this->original = $original;
    }
}
