<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Controller\Admin;


class CareerQuestion extends \XLite\Controller\Admin\AAdmin
{
    protected function addBaseLocation()
    {
        $this->addLocationNode(
            static::t('SkinActCareers Careers'),
            $this->buildURL('interview_questions')
        );
        $this->addLocationNode(
            static::t('SkinActCareers Interview Questions'),
            $this->buildURL('interview_questions')
        );
    }


    public function getTitle()
    {
        if ($this->getQuestionId() > 0) {
            return $this->getModelForm()->getModelObject()->getQuestion();
        }

        return static::t('SkinActCareers New question');
    }

    protected function getModelFormClass()
    {
        return \Qualiteam\SkinActCareers\View\Model\Question::class;
    }

    protected function getQuestionId()
    {
        return $this->getModelForm()->getModelObject()->getId();
    }

    protected function doActionCreateQuestion()
    {
        $this->getModelForm()->performAction('create');

        $this->setReturnURL($this->buildURL('career_question', '', ['id' => $this->getQuestionId()]));
    }

    protected function doActionUpdateQuestion()
    {
        $this->getModelForm()->performAction('update');

        $this->setReturnURL($this->buildURL('career_question', '', ['id' => $this->getQuestionId()]));
    }

}