<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use XCart\Container;

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
     * @param Configuration $configuration
     */
    public function __construct(
        private Configuration $configuration
    )
    {
    }

    /**
     * @param string|null $section
     *
     * @return string
     */
    public function getUrl(?string $section = 'core'): string
    {
        $sectionParam = $this->getSectionParam($section);

        return empty($sectionParam)
            ? sprintf('%s%s', $this->path, $this->param)
            : sprintf('%s/%s%s%s', $sectionParam, $this->configuration->getAppKey(), $this->path, $this->param);
    }

    protected function getSectionParam(?string $section): string
    {
        return match ($section) {
            'core' => Container::getContainer()?->getParameter('yotpo.reviews.api.core'),
            'apps' => Container::getContainer()?->getParameter('yotpo.reviews.api.ugc.apps'),
            'clear' => '',
            default => '/products',
        };
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