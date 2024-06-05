<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\View;

use RuntimeException;
use XLite\Core\Layout;
use XLite\Core\View\DTO\Assets;

/**
 * Asset registrar allows widgets to register their css and js assets to be included into the generated html
 */
class AssetRegistrar implements AssetRegistrarInterface
{
    /** @var array[Assets] */
    protected $assetBuffers = [];

    protected $layout;

    public function __construct()
    {
        $this->layout = Layout::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function register(Assets $assets)
    {
        if (!empty($this->assetBuffers)) {
            foreach ($this->assetBuffers as &$buffer) {
                $buffer[] = $assets;
            }
        } else {
            $this->layout->registerResources($assets->assets, $assets->index, $assets->interface, $assets->zone, $assets->group);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startBuffering()
    {
        $this->assetBuffers[] = [];
    }

    /**
     * {@inheritdoc}
     */
    public function stopBuffering()
    {
        if (empty($this->assetBuffers)) {
            throw new RuntimeException('Unbalanced startBuffering()/stopBuffering() calls');
        }

        return array_pop($this->assetBuffers);
    }
}
