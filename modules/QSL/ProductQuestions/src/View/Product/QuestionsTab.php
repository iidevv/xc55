<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Product;

use XCart\Extender\Mapping\ListChild;

/**
 * Questions & Answers tab on product details page
 *
 * @ListChild (list="product.details.page.tab.questions")
 */
class QuestionsTab extends \XLite\View\AView
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ProductQuestions/questions_tab/styles.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ProductQuestions/questions_tab/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ProductQuestions/questions_tab/body.twig';
    }

    /**
     * Get identifier of the product being viewed.
     *
     * @return integer
     */
    protected function getProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * Get the profile model for the user browsing the website.
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }
}
