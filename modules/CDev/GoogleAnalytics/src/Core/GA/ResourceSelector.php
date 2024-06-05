<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA;

use XLite\Core\Cache\ExecuteCachedTrait;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Core\GA\Interfaces\IResource;
use CDev\GoogleAnalytics\Core\GA\Library;
use CDev\GoogleAnalytics\Core\GA\Resource;

class ResourceSelector
{
    use ExecuteCachedTrait;

    /**
     * @var IResource
     */
    protected $resource;

    /**
     * @var string
     */
    protected $resource_key;

    public function __construct(string $resource_key)
    {
        $this->resource_key = $resource_key;

        $libraryKey = $this->getPreferredLibraryKey();

        $this->defineResource($libraryKey);
    }

    protected function getPreferredLibraryKey(): string
    {
        return static::preferredLibrary()[$this->resource_key] ?? GA::LIBRARY_GTAG;
    }

    protected static function preferredLibrary(): array
    {
        return [
            GA::CODE_VERSION_U => GA::LIBRARY_ANALYTICS,
            GA::CODE_VERSION_4 => GA::LIBRARY_GTAG,
        ];
    }

    protected function defineResource(string $libraryKey): void
    {
        if (
            $this->resource === null
            && ($resourceClass = $this->selectResourceClass($this->resource_key))
            && ($libraryClass = $this->selectLibraryClass($libraryKey))
        ) {
            $this->resource = new $resourceClass($libraryClass);
        }
    }

    protected function selectResourceClass($key): ?string
    {
        return static::resourceClasses()[$key] ?? null;
    }

    /**
     * @return string[]|IResource[]
     */
    protected static function resourceClasses(): array
    {
        return [
            GA::CODE_VERSION_U => Resource\Universal::class,
            GA::CODE_VERSION_4 => Resource\GA4::class,
        ];
    }

    protected function selectLibraryClass($key): ?string
    {
        return static::libraryClasses()[$key] ?? null;
    }

    /**
     * @return string[]|ALibrary[]
     */
    protected static function libraryClasses(): array
    {
        return [
            GA::LIBRARY_ANALYTICS => Library\Analytics::class,
            GA::LIBRARY_GTAG      => Library\GTag::class,
        ];
    }

    public function getResource(): ?IResource
    {
        if ($this->resource && $this->resource->isConfigured()) {
            return $this->resource;
        }

        return null;
    }
}
