<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View;

use Qualiteam\SkinActGraphQLApi\Core\License;

/**
 * Mobile Admin settings dialog
 */
abstract class RendererAbstract extends \XLite\View\AView
{
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function getCssStyles()
    {
        return [];
    }

    protected function getCssInterfaces()
    {
        return [
            \XLite::CUSTOMER_INTERFACE,
            \XLite::ADMIN_INTERFACE,
            \XLite::COMMON_INTERFACE,
        ];
    }

    protected function getCssStylesheets()
    {
        $minifier = new \tubalmartin\CssMin\Minifier();

        return array_reduce(
            $this->getCssStyles(),
            function ($acc, $style) use ($minifier) {
                if (!$style) {
                   return $acc;
                }

                foreach ($this->getCssInterfaces() as $interface) {
                    $path = \XLite\Core\Layout::getInstance()
                        ->getResourceFullPath($style, $interface);

                    if ($path) {
                        $acc[$style] = $minifier->run(file_get_contents($path));
                        break;
                    }
                }

                return $acc;
            }
        );
    }
}
