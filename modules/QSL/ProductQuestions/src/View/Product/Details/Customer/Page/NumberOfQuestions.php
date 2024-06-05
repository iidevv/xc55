<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Product\Details\Customer\Page;

/**
 * Widget displaying the number of questions asked for a particular product.
 */
class NumberOfQuestions extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-reward-points';
    }

    /**
     * Get list of CSS files needed to display this widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ProductQuestions/product/parts/number_of_questions.css';

        return $list;
    }

    /**
     * Get list of JS files needed to display this widget properly.
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ProductQuestions/product/parts/number_of_questions.js';

        return $list;
    }

    /**
     * Get relative path to the default widget template.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ProductQuestions/product/parts/number_of_questions.twig';
    }

    /**
     * Get the number of questions visible to the current user.
     *
     * @return integer
     */
    protected function getNumberOfQuestions()
    {
        return $this->getRepository()->countQuestionsVisibleToUser(
            $this->getProductId(),
            $this->getProfile(),
            $this->getCurrentGuestUserQuestionIds()
        );
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

    /**
     * Get the repository for the Question model.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question');
    }

    /**
     * Get identifiers of questions asked by the guest customer browsing the website.
     *
     * @return array|mixed
     */
    protected function getCurrentGuestUserQuestionIds()
    {
        $ids = \XLite\Core\Session::getInstance()->questionIds;

        return ($ids && !$this->getProfile()) ? $ids : [];
    }

    /**
     * Get the product ID.
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getProduct()->getProductId();
    }
}
