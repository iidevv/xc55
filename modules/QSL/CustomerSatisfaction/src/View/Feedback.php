<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Invoice widget
 *
 * @ListChild (list="order.children", weight="30")
 */
class Feedback extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */

    public const PARAM_SURVEY = 'survey';

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getSurvey()
    {
        return $this->getParam(self::PARAM_SURVEY);
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CustomerSatisfaction/survey/style.css';

        return $list;
    }

    /**
     * Return survey answers
     *
     * @return array
     */
    public function getAnswers()
    {
        return \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Answer')->findBy(['survey' => $this->getSurvey()]);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SURVEY => new \XLite\Model\WidgetParam\TypeObject(
                'Survey',
                null,
                false,
                '\QSL\CustomerSatisfaction\Model\Survey'
            ),
        ];
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CustomerSatisfaction/feedback/body.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getSurvey();
    }

    /**
     * Return rating label
     *
     * @return string
     */
    protected function getCustomerRatingString()
    {
        return $this->customerRating[$this->getSurvey()->getRating()];
    }


    // }}}
}
