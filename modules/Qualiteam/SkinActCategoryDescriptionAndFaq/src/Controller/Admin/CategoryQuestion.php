<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\Controller\Admin;

class CategoryQuestion extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @var array
     */
    protected $params = ['target', 'id'];

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['id', 'parent']);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getQuestion() ?? static::t('SkinActCategoryDescriptionAndFaq Question');
    }

    /**
     * Check controller visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCategoryQuestion();
    }

    /**
     * Add part to the location nodes list
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getCategoryQuestion()) {
            $this->addLocationNode(
                'Category Questions',
                $this->buildURL('category_questions')
            );
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return !$this->isVisible()
            ? static::t('No category question defined')
            : (($question = $this->getQuestion())
                ? $question
                : static::t('Manage category questions')
            );
    }

    /**
     * Return the top link title
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->getCategoryQuestion() ? $this->getCategoryQuestion()->getQuestion() : '';
    }

    /**
     * Return the category question
     *
     * @return string
     */
    public function getCategoryQuestion()
    {
        if (is_null($this->category_question)) {
            $this->category_question = \XLite\Core\Database::getRepo('Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion')
                ->find($this->getCategoryQuestionId());
        }

        return $this->category_question;
    }

    /**
     * Return the category question id
     *
     * @return string
     */
    public function getCategoryQuestionId()
    {
        $id = \XLite\Core\Request::getInstance()->id;

        return $id ?? null;
    }

    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
        if (!\XLite\Core\Request::getInstance()->id) {
            $this->setReturnURL(
                $this->buildURL(
                    'category_question',
                    '',
                    ['id' => $this->getModelForm()->getModelObject()->getId()]
                )
            );
        }
    }

    protected function doActionUpdateAndClose()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(
                \XLite\Core\Converter::buildURL('category_questions')
            );
        }
    }

    /**
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'Qualiteam\SkinActCategoryDescriptionAndFaq\View\Model\CategoryQuestion';
    }
}
