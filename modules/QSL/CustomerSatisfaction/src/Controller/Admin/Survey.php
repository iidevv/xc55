<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Controller\Admin;

/**
 * Survey controller
 */
class Survey extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'id'];


    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Manage Customer Feedback');
    }

    /**
     * Get feedback survey
     *
     * @return void
     */
    public function getSurvey()
    {

        return \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Survey')->find(\XLite\Core\Request::getInstance()->id);
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('survey'));
        }
    }

    /**
     * Set feedback manager
     *
     * @return void
     */

    protected function doActionSetManager()
    {
        if ($this->getProfile() && !$this->getSurvey()->getManager()) {
            $survey = $this->getSurvey();
            $survey->setManager($this->getProfile());
            $survey->setFeedbackProcessedDate(\XLite\Core\Converter::time());
            $survey->setStatus(\QSL\CustomerSatisfaction\Model\Survey::STATUS_IN_PROGRESS);

            \XLite\Core\Database::getEM()->flush();
        }

        $this->setReturnURL(\XLite\Core\Converter::buildURL('survey', null, ['id' => \XLite\Core\Request::getInstance()->id]));
    }

    /**
     * Update staff info
     *
     * @return void
     */
    protected function doActionUpdateStaff()
    {
        $survey = $this->getSurvey();
        $survey->setComments(\XLite\Core\Request::getInstance()->comments);
        $survey->setStatus(\XLite\Core\Request::getInstance()->status);

        $tmpTags = \XLite\Core\Request::getInstance()->tags;

        $tags = explode(',', $tmpTags);

        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            $tag = \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Tag')->findBy(['name' => $tagName]);

            if (!$tag) {
                $tag = new \QSL\CustomerSatisfaction\Model\Tag();
                $tag->setName($tagName);
            } else {
                $tag = $tag[0];
            }

            if (!$survey->getTags()->contains($tag)) {
                $tag->addSurveys($survey);
                $survey->addTags($tag);
            }
        }

        \XLite\Core\Database::getEM()->flush();

        $this->setReturnURL(\XLite\Core\Converter::buildURL('survey', null, ['id' => \XLite\Core\Request::getInstance()->id]));
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'QSL\CustomerSatisfaction\View\Model\Survey';
    }
}
