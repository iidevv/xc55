<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Controller\Customer;

/**
 * Contact us controller
 */
class ShareWithFriends extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
        && \XLite\Core\Request::getInstance()->key
        && $this->getSurvey()
        && $this->checkKey();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Thanks for the highest rating given to us');
    }

    /**
     * Return value of data
     *
     * @param string $field Field
     *
     * @return string
     */
    public function getValue($field)
    {
        $data = \XLite\Core\Session::getInstance()->share_with_friends;

        $value = $data && isset($data[$field]) ? $data[$field] : '';

        return $value;
    }

    /**
     * Return FaceBook URL
     *
     * @return string
     */
    public function getFBUrl()
    {

        return \XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction->cs_facebook_funpage;
    }

    /**
     * Return feedback survey
     *
     * @return object
     */
    public function getSurvey()
    {
        return \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Survey')->findOneBy(
            [
                'hashKey' => \XLite\Core\Request::getInstance()->key
            ]
        );
    }

    /**
     * Return page top text
     *
     * @return string
     */
    public function getTopText()
    {
        $txtVar = 'cs_text_rating_' . \XLite\Core\Request::getInstance()->rating;

        return static::t(\XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction->{$txtVar});
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Common method to survey validation
     *
     * @return boolean
     */
    protected function checkKey()
    {
        return \XLite\Core\Request::getInstance()->key && \XLite\Core\Request::getInstance()->key == $this->getSurvey()->getHashKey();
    }

    /**
     * Empty action
     *
     * @return void
     */
    protected function doNoAction()
    {
        $survey = $this->getSurvey();

        if ($survey->getFilled() && $survey->getRating() < \XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction->cs_raiting_for_share) {
            $this->setReturnURL(\XLite\Core\Converter::buildURL('survey_already_sent'));
        } else {
            $survey->setRating(\XLite\Core\Request::getInstance()->rating);
            $survey->setFeedbackDate(\XLite\Core\Converter::time());
            $survey->setStatus(\QSL\CustomerSatisfaction\Model\Survey::STATUS_CLOSED);
            $survey->setFilled(true);

            $isValid = true;

            $questions = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Question')->findByEnabled(true);

            foreach ($questions as $question) {
                if (!(\XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Answer')->findOneBy(['survey' => $survey, 'question' => $question]))) {
                    $answer = new \QSL\CustomerSatisfaction\Model\Answer();
                    $answer->setSurvey($survey);
                    $answer->setQuestion($question);
                    $answer->setOriginQuestion($question->getQuestion());
                    $answer->setValue(\XLite\Core\Request::getInstance()->rating);
                    $survey->addAnswers($answer);
                }
            }

            \XLite\Core\Database::getEM()->flush();

            if ($isValid) {
                $survey = $this->getSurvey();
                $data = [
                    'rating'    => \XLite\Core\Request::getInstance()->rating,
                    'survey'  => $survey,
                    'order'     => $survey->getOrder(),
                ];
                \XLite\Core\Mailer::getInstance()->sendCSFeedbackMessage(
                    $data
                );
            }
        }
    }
}
