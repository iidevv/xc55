<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Customer survey widget
 *
 * @ListChild (list="center", zone="customer")
 */
class CustomerSurvey extends \XLite\View\AView
{
    /**
     * Params
     *
     * @var array
     */
    protected $params = ['target', 'survey_id'];

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['customer_survey']);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CustomerSatisfaction/style.css';
        $list[] = 'modules/QSL/CustomerSatisfaction/form_field/input/stars/stars.css';
        $list[] = 'modules/QSL/CustomerSatisfaction/vote_bar/vote_bar.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/CustomerSatisfaction/form_field/input/stars/stars_1.js';

        return $list;
    }

    /**
     * Return survey questions
     *
     * @return array
     */
    public function getSurveyQuestions()
    {

        return \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Question')
            ->findByEnabled(true, [ 'position' => 'ASC' ]);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CustomerSatisfaction/customer_survey/body.twig';
    }
}
