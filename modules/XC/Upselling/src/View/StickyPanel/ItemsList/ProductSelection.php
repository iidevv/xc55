<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\View\StickyPanel\ItemsList;

/**
 * Upselling ProductSelection sticky panel
 */
class ProductSelection extends \XLite\View\StickyPanel\ItemsList\ProductSelection
{
    /**
     * @param array $additionalButtons
     *
     * @return array
     */
    protected function prepareAdditionalButtons($additionalButtons)
    {
        $result = parent::prepareAdditionalButtons($additionalButtons);
        $result[] = $this->getMakeMutualRelationsWidget();

        return $result;
    }

    /**
     * Get send notification widget
     *
     * @return \Xlite\View\AView
     */
    protected function getMakeMutualRelationsWidget()
    {
        return $this->getWidget(
            [
                'template' => 'modules/XC/Upselling/u_products/parts/mutual_relations.twig',
                'checked'  => true,
            ]
        );
    }

    /**
     * Flag to display OR label
     *
     * @return boolean
     */
    protected function isDisplayORLabel()
    {
        return false;
    }
}
