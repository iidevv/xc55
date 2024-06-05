<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Survey page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Survey extends \XLite\View\AView
{
    /**
     * Rating labels
     *
     * @var array
     */
    protected static $labels = [
        '1' => 'Awful',
        '2' => 'Bad',
        '3' => 'Fair',
        '4' => 'Good',
        '5' => 'Excellent'
    ];

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['survey']);
    }

    /**
     * Get a list of CSS files required to display the widget properly
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
     * Return tags
     *
     * @return string
     */
    public function getTags()
    {
        $tags = $this->getSurvey()->getTags();

        $returnTags = [];
        foreach ($tags as $tag) {
            $returnTags[] = $tag->getName();
        }
        if ($returnTags) {
            $tagsString = implode(',', $returnTags);
        }

        return (!empty($tagsString)) ? $tagsString : '';
    }

    /**
     * Return rating label
     *
     * @return string
     */
    protected function getCustomerRatingString()
    {
        return static::getRatingLabel($this->getSurvey()->getRating());
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CustomerSatisfaction/survey/body.twig';
    }

    /**
     * Return rating label
     *
     * @param integer $rating Rating value
     *
     * @return string
     */
    public static function getRatingLabel($rating)
    {
        return static::$labels[$rating] ? static::t(static::$labels[$rating]) : '';
    }
}
