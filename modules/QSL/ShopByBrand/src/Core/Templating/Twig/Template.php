<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Core\Templating\Twig;

use XCart\Extender\Mapping\Extender;

/**
 * Template
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\CrispWhiteSkin")
 * @Extender\After ("QSL\RichGoogleSearchResults")
 */
abstract class Template extends \XLite\Core\Templating\Twig\Template
{
    /**
     * Extend display function to add snippets
     *
     * @param array $context The context
     * @param array $blocks  The current set of blocks
     */
    public function display(array $context, array $blocks = [])
    {
        $targets = ['brands'];

        if (
            !in_array(\XLite\Core\Request::getInstance()->target, $targets)
            || \XLite::isAdminZone()
            || $this->getTemplateName() !== 'pager/parts/items_total.twig'
        ) {
            parent::display($context, $blocks);

            return;
        }

        $productsLabel = \XLite\Core\Translation::getInstance()->translate('Products');
        $brandsLabel   = \XLite\Core\Translation::getInstance()->translate('Brands');

        $sourceCode = $this->getSBBDisplayCode($context, $blocks);
        echo str_replace(
            "<span class=\"pager-items-label\">{$productsLabel}: </span>",
            "<span class=\"pager-items-label\">{$brandsLabel}: </span>",
            $sourceCode
        );

        return;
    }

    /**
     * Intercept twig output
     *
     * @param array $context The context
     * @param array $blocks  The current set of blocks
     *
     * @return string
     * @throws \Exception
     */
    public function getSBBDisplayCode(array $context, array $blocks = [])
    {
        $level = ob_get_level();
        ob_start();

        try {
            parent::display($context, $blocks);
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }

        return ob_get_clean();
    }
}
