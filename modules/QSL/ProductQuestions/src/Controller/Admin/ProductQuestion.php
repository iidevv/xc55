<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Controller\Admin;

/**
 * Question controller
 */
class ProductQuestion extends \XLite\Controller\Admin\AAdmin
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
        $id = (int) \XLite\Core\Request::getInstance()->id;
        $model = $id
            ? \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question')->find($id)
            : null;

        return ($model && $model->getId())
            ? $model->getName()
            : \XLite\Core\Translation::getInstance()->lbl('Product question');
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Questions'), $this->buildURL('product_questions'));
    }

    /**
     * Update model
     * @throws \Exception
     */
    protected function doActionUpdate()
    {
        /** @var \QSL\ProductQuestions\Model\Question $model */
        $model = $this->getModelForm()->getModelObject();

        $answerBefore =  $model ? $model->getPublished() : false;

        if ($this->getModelForm()->performAction('modify')) {
            if (!$answerBefore && $model->getPublished()) {
                $this->doFirstAnswerActions($model);
            }

            $model->setAnswerProfile($this->getAnswerProfile());

            \XLite\Core\Database::getEM()->flush();

            $this->setReturnUrl(\XLite\Core\Converter::buildURL('product_questions'));
        }
    }

    /**
     * Do necessary actions when the question has been answered the first time.
     *
     * @param \QSL\ProductQuestions\Model\Question $model Question model
     */
    protected function doFirstAnswerActions(\QSL\ProductQuestions\Model\Question $model)
    {
        if ($this->isCustomerNotificationEnabled()) {
            $this->sendAnswerToCustomer($model);
        }
    }

    /**
     * Return current admin profile
     *
     * @return \XLite\Model\Profile
     */
    public function getAnswerProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile() ?: null;
    }

    /**
     * Check if customers should receive answers on their questions by e-mail.
     *
     * @return bool
     */
    protected function isCustomerNotificationEnabled()
    {
        return true;
    }

    /**
     * Email answer to the customer that asked the question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Question model
     */
    protected function sendAnswerToCustomer($question)
    {
        \XLite\Core\Mailer::getInstance()->sendProductQuestionAnswerCustomer($question);
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'QSL\ProductQuestions\View\Model\Question';
    }
}
