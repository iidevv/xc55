<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Twig\Loader;

use Twig\Loader\LoaderInterface;
use Twig\Source;
use XLite\Core\Layout;

class FilesystemLoader implements LoaderInterface
{
    private LoaderInterface $inner;

    private Layout $layout;

    public function __construct(
        LoaderInterface $inner,
        Layout $layout
    ) {
        $this->inner = $inner;
        $this->layout = $layout;
    }

    public function getSourceContext(string $name): Source
    {
        return $this->inner->getSourceContext($this->addNamespaceToName($name));
    }

    public function getCacheKey(string $name): string
    {
        return $this->inner->getCacheKey($this->addNamespaceToName($name));
    }

    /**
     * @return bool
     */
    public function exists(string $name)
    {
        return $this->inner->exists($this->addNamespaceToName($name));
    }

    public function isFresh(string $name, int $time): bool
    {
        return $this->inner->isFresh($this->addNamespaceToName($name), $time);
    }

    private function addNamespaceToName(string $name): string
    {
        $interface = $this->layout->getInterface();
        $zone = $this->layout->getZone();

        return ($name && (strpos($name, '@') !== 0)) ? "@{$interface}.{$zone}/{$name}" : $name;
    }
}
