<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Controller\Customer;

/**
 * "Ask question" controller.
 *
 */
class ProductQuestion extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Question
     *
     * @var \QSL\ProductQuestions\Model\Question
     */
    protected $question;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Ask a question about the product';
    }

    /**
     * Get return URL
     *
     * @return string
     */
    public function getReturnURL()
    {
        // Reload the page if the form is submitted
        return \XLite\Core\Request::getInstance()->action ? '' : parent::getReturnURL();
    }

    /**
     * Return current product Id
     *
     * @param boolean $getFromQuestion If the product ID should be taken from the current question OPTIONAl
     *
     * @return integer
     */
    public function getProductId($getFromQuestion = true)
    {
        $productId = \XLite\Core\Request::getInstance()->product_id;

        if (empty($productId) && $getFromQuestion) {
            $question = $this->getQuestion();
            if ($question) {
                $productId = $question->getProduct()->getProductId();
            }
        }

        return $productId;
    }

    /**
     * Return question
     *
     * @return \QSL\ProductQuestions\Model\Question
     */
    public function getQuestion()
    {
        $id = $this->getId();

        if ($id) {
            $question = \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question')->find($id);
        } else {
            $question = new \QSL\ProductQuestions\Model\Question();
            $question->setEmail($this->getProfileField('email'));
            $question->setName($this->getProfileField('name'));
        }

        if (!$question->getAnswer()) {
            // Make the life easier for administrators: make Published form field preselected for new questions
            $question->setPublished(true);
        }

        return $question;
    }

    /**
     * Return question Id
     *
     * @return integer
     */
    public function getId()
    {
        return null; // \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return current category Id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->category_id;
    }

    /**
     * Return current profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile() ? : null;
    }

    /**
     * Return field value from current profile
     *
     * @param string $field Field name
     *
     * @return string
     */
    public function getProfileField($field)
    {
        $value = '';
        $auth = \XLite\Core\Auth::getInstance();
        if ($auth->isLogged()) {
            switch ($field) {
                case 'name':
                    if (0 < $auth->getProfile()->getAddresses()->count()) {
                        $value = $auth->getProfile()->getAddresses()->first()->getName();
                    }
                    break;

                case 'email':
                    $value = $auth->getProfile()->getLogin();
                    break;

                default:
            }
        }

        return $value;
    }

    /**
     * Alias
     *
     * @return \QSL\ProductQuestions\Model\Question
     */
    protected function getEntity()
    {
        return $this->getQuestion();
    }

    /**
     * Filters the request data and returns only those parameters that relate to product questions.
     *
     * @param array $data Request data
     *
     * @return array
     */
    protected function filterRequestData(array $data)
    {
        $allowed = [
            'id'         => 'id',
            'product_id' => 'product_id',
            'name'       => 'name',
            'email'      => 'email',
            'question'   => 'question',
            'private'    => 'private',
        ];

        return array_intersect_key($data, $allowed);
    }

    /**
     * Update review ids saved in session
     * used for connection between anonymous user and his reviews
     *
     * @param \QSL\ProductQuestions\Model\Question $entity Question model
     *
     * @return boolean
     */
    protected function updateQuestionIds(\QSL\ProductQuestions\Model\Question $entity)
    {
        if (!$this->getProfile()) {
            $questionIds = \XLite\Core\Session::getInstance()->questionIds;

            if (!is_array($questionIds)) {
                $questionIds = [];
            }

            if ($entity->getId()) {
                $questionIds[] = $entity->getId();
            }

            \XLite\Core\Session::getInstance()->questionIds = array_unique($questionIds);
        }

        return true;
    }

    /**
     * Modify model
     *
     * @return void
     */
    protected function doActionModify()
    {
        $this->doActionCreate();
    }

    /**
     * Create new model
     */
    protected function doActionCreate()
    {
        $data = $this->filterRequestData(\XLite\Core\Request::getInstance()->getData());

        if ($this->isAllowedAskQuestion() && $this->validateQuestion($data)) {
            $this->createQuestion($data);
            $this->closeQuestionPopup();
        } else {
            $this->processInvalidQuestion($data);
        }
    }

    /**
     * Do necessary actions if the submitted question has failed to pass the validation.
     *
     * @param array $data Submitted question fields
     *
     * @return void
     */
    protected function processInvalidQuestion(array $data)
    {
        $this->set('valid', false);
        $this->setReturnURL($this->buildURL('product_question'));
    }

    /**
     * Do necessary actions for a question that has passed the validation.
     *
     * @param array $data Submitted question fields
     */
    protected function createQuestion(array $data)
    {
        $question = new \QSL\ProductQuestions\Model\Question();
        $question->setName($data['name']);
        $question->setEmail($data['email']);
        $question->setQuestion(isset($data['question']) ? trim($data['question']) : '');
        $question->setPrivate(isset($data['private']) ? (int) $data['private'] : 0);
        $question->setProfile($this->getProfile());
        /** @var \XLite\Model\Product $product */
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
        $question->setProduct($product);
        $product->addQuestions($question);
        $question->create();
        $this->updateQuestionIds($question);

        \XLite\Core\Mailer::getInstance()->sendNewProductQuestionAdmin($question);

        \XLite\Core\TopMessage::addInfo(
            static::t('Thank your for asking the question!')
        );
    }

    /**
     * Closes the question popup.
     */
    protected function closeQuestionPopup()
    {
        \XLite\Core\Event::productQuestionAdded();
        $this->setSilenceClose();
    }

    /**
     * Validates the submitted data.
     *
     * @param array $data Submitted question fields
     *
     * @return boolean
     */
    protected function validateQuestion($data)
    {
        $errors = [];

        if (!isset($data['name']) || !$data['name']) {
            $errors['name'] = 'Please enter your name';
        }

        if (!isset($data['question']) || !$data['question']) {
            $errors['question'] = 'Please enter the question';
        }

        foreach ($errors as $field => $message) {
            if (!$this->isAJAX()) {
                \XLite\Core\TopMessage::addError($message);
            }
            \XLite\Core\Event::invalidElement($field, static::t($message));
        }

        return empty($errors);
    }
}
