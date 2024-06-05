<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Templating\Twig\Loader;

use Twig\Loader\LoaderInterface;
use Twig\Source;
use XLite\Core\Layout;

class ThemeTweaker implements LoaderInterface
{
    private Layout $layout;

    public function __construct(
        Layout $layout
    ) {
        $this->layout = $layout;
    }

    public function getSourceContext(string $name): Source
    {
        $interface = $this->layout->getInterface();
        $zone      = $this->layout->getZone();

        return new Source(
            $this->layout->getTweakerContent($name, $interface, $zone),
            $name,
            $this->layout->getResourceFullPath($name, $interface, $zone) ?? $name
        );
    }

    public function getCacheKey(string $name): string
    {
        $interface = $this->layout->getInterface();
        $zone      = $this->layout->getZone();

        return "{$interface}/{$zone}/$name";
    }

    public function isFresh(string $name, int $time): bool
    {
        return $time >= $this->layout->getTweakerDate($name);
    }

    public function exists(string $name)
    {
        return $this->layout->hasTweakerTemplate($name, $this->layout->getInterface(), $this->layout->getZone());
    }
}
