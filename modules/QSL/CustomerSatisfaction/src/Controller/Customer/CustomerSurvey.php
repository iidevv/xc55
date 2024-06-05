<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Controller\Customer;

/**
 * Contact us controller
 */
class CustomerSurvey extends \XLite\Controller\Customer\ACustomer
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
        return static::t('Share your experience with us');
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
     * Return customer survey page top text
     *
     * @return string
     */
    public function getTopText()
    {
        $txtVar = 'cs_text_rating_' . \XLite\Core\Request::getInstance()->rating;

        return static::t(\XLite\Core\Config::getInstance()->QSL->CustomerSatisfaction->{$txtVar});
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getName()
    {
        return $this->getTitle();
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
        $data = \XLite\Core\Session::getInstance()->customer_survey;

        return $data && isset($data[$field]) ? $data[$field] : '';
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
     * Send message
     *
     * @return void
     */
    protected function doActionSend()
    {
        $survey = $this->getSurvey();

        $survey->setRating(\XLite\Core\Request::getInstance()->rating);
        $survey->setFeedbackDate(\XLite\Core\Converter::time());
        $survey->setCustomerMessage(\XLite\Core\Request::getInstance()->customerMessage);
        $survey->setStatus(\QSL\CustomerSatisfaction\Model\Survey::STATUS_NEW);
        $survey->setFilled(true);

        $data = \XLite\Core\Request::getInstance()->getData();

        foreach ($data as $key => $value) {
            if (preg_match('/^qa_([0-9]+)$/', $key, $matches)) {
                $question = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Question')->findOneById($matches[1]);
                $answer = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Answer')->findOneBy(['survey' => $survey, 'question' => $question]);

                if ($question->getId() && !$answer) {
                    $answer = new \QSL\CustomerSatisfaction\Model\Answer();
                    $answer->setSurvey($survey);
                    $answer->setQuestion($question);
                    $answer->setOriginQuestion($question->getQuestion());
                    $answer->setValue($value);
                    $survey->addAnswers($answer);

                    \XLite\Core\Database::getEM()->persist($answer);
                } elseif ($question->getId() && $answer) {
                    $answer->setValue($value);
                }
            }
        }

        \XLite\Core\Database::getEM()->flush();

        $survey = $this->getSurvey();

        $data = [
            'rating'    => \XLite\Core\Request::getInstance()->rating,
            'survey'    => $survey,
            'order'     => $survey->getOrder(),
        ];

        $errorMessage = \XLite\Core\Mailer::getInstance()->sendCSFeedbackMessage(
            $data
        );

        if ($errorMessage) {
            \XLite\Core\TopMessage::addError($errorMessage);
            \XLite\Core\Session::getInstance()->customer_survey = $data;
        }
        $this->setReturnURL(\XLite\Core\Converter::buildURL('survey_sent'));
    }

    /**
     * Empty action
     *
     * @return void
     */
    protected function doNoAction()
    {
        $survey = $this->getSurvey();

        if ($survey->getFilled()) {
            $this->setReturnURL(\XLite\Core\Converter::buildURL('survey_already_sent'));
        } else {
            $survey->setRating(\XLite\Core\Request::getInstance()->rating);
            $survey->setFeedbackDate(\XLite\Core\Converter::time());
            $survey->setStatus(\QSL\CustomerSatisfaction\Model\Survey::STATUS_NEW);

            $questions = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Question')->findByEnabled(true);

            foreach ($questions as $question) {
                $answer = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Answer')->findOneBy(['survey' => $survey, 'question' => $question]);

                if (!$answer) {
                    $answer = new \QSL\CustomerSatisfaction\Model\Answer();
                    $answer->setSurvey($survey);
                    $answer->setQuestion($question);
                    $answer->setOriginQuestion($question->getQuestion());
                    $answer->setValue(\XLite\Core\Request::getInstance()->rating);
                    $survey->addAnswers($answer);
                }
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    public function getAllParams($exceptions = null): array
    {
        $params = parent::getAllParams($exceptions);

        if (\XLite\Core\Request::getInstance()->id) {
            $params['id'] = \XLite\Core\Request::getInstance()->id;
        }

        if (\XLite\Core\Request::getInstance()->rating) {
            $params['rating'] = \XLite\Core\Request::getInstance()->rating;
        }

        if (\XLite\Core\Request::getInstance()->key) {
            $params['key'] = \XLite\Core\Request::getInstance()->key;
        }

        return $params;
    }
}
