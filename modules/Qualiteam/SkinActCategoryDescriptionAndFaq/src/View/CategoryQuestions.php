<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Category Questions widget
 *
 * @ListChild (list="category-bottom.element", zone="customer", weight="101")
 */
class CategoryQuestions extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'category';

        return $result;
    }

    /**
     * Get top links
     *
     * @return array
     */
    protected function getQuestionsAndAnswers()
    {
        $result = [];
        $questions_and_answers = \XLite\Core\Database::getRepo('Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion')->findBy(
            ['enabled' => true],
            ['position' => 'ASC']
        );

        foreach ($questions_and_answers as $question_and_answer) {
            $result[$question_and_answer->getId()] = [
                'question' => $question_and_answer->getQuestion(),
                'answer' => $question_and_answer->getAnswer(),
            ];
        }

        return $result;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && count($this->getQuestionsAndAnswers()) > 0 ;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/content/category_questions.twig';
    }

//    public function getCSSFiles()
//    {
//        $list = parent::getCSSFiles();
//
//        $list[] = [
//            'file'  => 'modules/Qualiteam/PalmFlexTopLinks/css/top-links.less',
//            'media' => 'screen',
//            'merge' => 'bootstrap/css/bootstrap.less',
//        ];
//
//        $list[] = [
//            'file'  => 'modules/Qualiteam/PalmFlexTopLinks/css/top-links.less',
//            'media' => 'screen',
//            'merge' => 'bootstrap/css/bootstrap.less',
//        ];
//
//        return $list;
//    }
}
