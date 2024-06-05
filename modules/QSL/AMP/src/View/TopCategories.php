<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Sidebar categories list
 *
 * @ListChild (list="amp.layout.header.categories", weight="10")
 */
class TopCategories extends \XLite\View\TopCategories
{
    /**
     * Get widge title
     *
     * @return string
     */
    protected function getHead()
    {
        return false;
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/AMP/categories/menulist';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/categories/menulist/body.twig';
    }

    /**
     * Preprocess DTO
     *
     * @param  array    $categoryDTO
     * @return array
     */
    protected function preprocessDTO($categoryDTO)
    {
        $categoryDTO = parent::preprocessDTO($categoryDTO);

        if (static::isAMP()) {
            $categoryDTO['link'] = $this->getAbsoluteURL($categoryDTO['link']);
        }

        return $categoryDTO;
    }

    /**
     * Get cache parameters for proprocessed DTOs
     *
     * @return array
     */
    protected function getProcessedDTOsCacheParameters()
    {
        $params = parent::getProcessedDTOsCacheParameters();

        if (static::isAMP()) {
            $params += ['AMP' => true];
        }

        return $params;
    }
}
