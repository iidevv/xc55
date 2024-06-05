<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\Symfony\Component\Asset;

use Symfony\Component\Asset\PackageInterface;

final class PackagePublicDirDecorator implements PackageInterface
{
    private PackageInterface $inner;

    private bool $xcartPublicDir;

    public function __construct(PackageInterface $inner, bool $xcartPublicDir)
    {
        $this->inner          = $inner;
        $this->xcartPublicDir = $xcartPublicDir;
    }

    public function getUrl(string $path): string
    {
        if (!$this->xcartPublicDir) {
            return $this->inner->getUrl($path);
        }

        return $this->inner->getUrl("public/{$path}");
    }

    public function getVersion(string $path): string
    {
        if (!$this->xcartPublicDir) {
            return $this->inner->getVersion($path);
        }

        return $this->inner->getVersion("public/{$path}");
    }
}
