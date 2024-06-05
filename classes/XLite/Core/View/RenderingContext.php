<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\View;

use XLite\Core\View\DTO\Assets;
use XLite\Core\View\DTO\RenderedWidget;

class RenderingContext implements RenderingContextInterface
{
    /** @var AssetRegistrarInterface */
    protected $assetRegistrar;

    /** @var MetaTagRegistrarInterface */
    protected $metaTagRegistrar;

    protected $bufferingLevel = 0;

    public function __construct(AssetRegistrarInterface $assetRegistrar, MetaTagRegistrarInterface $metaTagRegistrar)
    {
        $this->assetRegistrar   = $assetRegistrar;
        $this->metaTagRegistrar = $metaTagRegistrar;
    }

    /**
     * {@inheritdoc}
     */
    public function registerAssets(Assets $assets)
    {
        $this->assetRegistrar->register($assets);
    }

    /**
     * {@inheritdoc}
     */
    public function registerMetaTags(array $tags)
    {
        $this->metaTagRegistrar->register($tags);
    }

    /**
     * {@inheritdoc}
     */
    public function startBuffering()
    {
        $this->bufferingLevel++;

        ob_start();

        $this->assetRegistrar->startBuffering();
        $this->metaTagRegistrar->startBuffering();
    }

    /**
     * {@inheritdoc}
     */
    public function stopBuffering()
    {
        $this->bufferingLevel--;

        $widget = new RenderedWidget(
            ob_get_contents(),
            $this->assetRegistrar->stopBuffering(),
            $this->metaTagRegistrar->stopBuffering()
        );

        ob_end_clean();

        return $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function isBuffering()
    {
        return $this->bufferingLevel > 0;
    }

    /**
     * Return output buffering level
     *
     * @return mixed
     */
    public function getBufferingLevel()
    {
        return $this->bufferingLevel;
    }
}
