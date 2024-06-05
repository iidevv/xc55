<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View;

/**
 * Widget displaying product questions visible to the user.
 */
class ProductQuestions extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_DISPLAY_MODE = 'displayMode';
    public const PARAM_PRODUCT      = 'product';
    public const PARAM_PRODUCT_ID   = 'product_id';
    public const PARAM_PROFILE      = 'profile';

    /**
     * Widget display modes
     */
    public const DISPLAY_MODE_QUICK = 'quick';
    public const DISPLAY_MODE_FULL  = 'full';

    protected $productId;
    protected $profileId;
    protected $questions;

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
          'file' => $this->getDir() . '/styles.less',
          'media' => 'screen',
          'merge' => 'bootstrap/css/bootstrap.less',
        ];

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
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        if (!$this->isCloned) {
            // Init from the request those widget parameters that haven't been set manually yet
            $params += $this->getWidgetParamsFromRequest();
        }

        parent::setWidgetParams($params);
    }

    /**
     * Check if the full admin name should be displayed for the question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Question
     *
     * @return boolean
     */
    public function isFullAdminNameVisible(\QSL\ProductQuestions\Model\Question $question)
    {
        return $question->getAnswerProfile()
            && \XLite\Core\Config::getInstance()->QSL->ProductQuestions->product_questions_admin_fullname;
    }

    /**
     * Returns the default name that should be displayed as the signature of the user answered the question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Question model
     *
     * @return string
     */
    protected function getDefaultAnswerName(\QSL\ProductQuestions\Model\Question $question)
    {
        return static::t('Administrator');
    }

    /**
     * Returns the full name of the user answered the question.
     *
     * @param \QSL\ProductQuestions\Model\Question $question Question model
     *
     * @return string
     */
    protected function getFullAnswerName(\QSL\ProductQuestions\Model\Question $question)
    {
        return $question->getAnswerProfile()->getName();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
          static::PARAM_DISPLAY_MODE => new \XLite\Model\WidgetParam\TypeString('Display mode', static::DISPLAY_MODE_QUICK),
          static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
          static::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\TypeInt('Product ID', 0),
          static::PARAM_PROFILE => new \XLite\Model\WidgetParam\TypeObject('Profile', $this->getCurrentProfile(), false, '\XLite\Model\Profile'),
        ];
    }

    /**
     * Read widget parameters from the request.
     *
     * @return array
     */
    protected function getWidgetParamsFromRequest()
    {
        $request = \XLite\Core\Request::getInstance();

        return [
            static::PARAM_PRODUCT_ID   => $request->{static::PARAM_PRODUCT_ID},
            static::PARAM_DISPLAY_MODE => $request->{static::PARAM_DISPLAY_MODE},
        ];
    }

    /**
     * Get the profile model for the user browsing the website.
     *
     * @return \XLite\Model\Profile
     */
    protected function getCurrentProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->isListOfProductQuestionsEmpty()
          ? $this->getEmptyTemplate()
          : parent::getTemplate();
    }

    /**
     * Check if there are no visible questions for the product.
     *
     * @return boolean
     */
    protected function isListOfProductQuestionsEmpty()
    {
        return $this->countVisibleQuestions() === 0;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return template when there are no items in the list.
     *
     * @return string
     */
    protected function getEmptyTemplate()
    {
        return $this->getDir() . '/empty.twig';
    }


    /**
     * Get path to the directory where widget resources reside.
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ProductQuestions/product_questions';
    }

    /**
     * Get questions to display in the widget.
     *
     * @return mixed
     */
    protected function getQuestions()
    {
        if (!isset($this->questions)) {
            $this->defineQuestions();
        }

        return $this->questions;
    }

    /**
     * Retrieve questions to be displayed in the widget and store it in the object.
     */
    protected function defineQuestions()
    {
        $max = $this->getMaxNumberOfItems();

        $this->questions = [];

        foreach ($this->getUserQuestions($max) as $question) {
            $this->questions[] = $question;
        }

        if (empty($this->questions) || $this->isFullDisplayMode()) {
            // Join questions into a single list in the full mode
            // or show other questions if there are no user's questions in the quick mode
            foreach ($this->getOtherQuestions($max) as $question) {
                $this->questions[] = $question;
            }
        }
    }

    /**
     * Get questions asked by the current user.
     *
     * @param integer $limit Maximum number of questions to retrieve
     *
     * @return mixed
     */
    protected function getUserQuestions($limit)
    {
        return $this->getRepository()->findUserProductQuestions(
            $this->getProductId(),
            $this->getProfile(),
            $this->getCurrentGuestUserQuestionIds(),
            false,
            $limit
        );
    }

    /**
     * Get questions asked by other users.
     *
     * @param integer $limit Maximum number of questions to retrieve
     *
     * @return mixed
     */
    protected function getOtherQuestions($limit)
    {
        return $this->getRepository()->findOthersProductQuestions(
            $this->getProductId(),
            $this->getProfile(),
            $this->getCurrentGuestUserQuestionIds(),
            false,
            $limit
        );
    }

    /**
     * Get identifiers of questions asked by the guest customer browsing the website.
     *
     * @return array|mixed
     */
    protected function getCurrentGuestUserQuestionIds()
    {
        $ids = \XLite\Core\Session::getInstance()->questionIds;

        return ($ids && !$this->getProfile()) ? $ids : [];
    }

    /**
     * Get the repository for the Question model.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question');
    }

    /**
     * Get the profile model for the user browsing the website.
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getParam(static::PARAM_PROFILE);
    }

    /**
     * Get identifier of the product being viewed.
     *
     * @return integer
     */
    protected function getProductId()
    {
        if (!isset($this->productId)) {
            $this->productId = $this->getParam(static::PARAM_PRODUCT_ID);
            if (!$this->productId) {
                $product = $this->getParam(static::PARAM_PRODUCT);
                if ($product) {
                    $this->productId = $product->getProductId();
                }
            }
        }

        return $this->productId;
    }

    /**
     * Get profile identifier for the user viewing the page.
     *
     * @return integer
     */
    protected function getProfileId()
    {
        if (!isset($this->profileId)) {
            $profile = $this->getParam(static::PARAM_PROFILE);
            $this->profileId = $profile ? $profile->getProfileId() : 0;
        }

        return $this->productId;
    }

    /**
     * Whether the widget is being displayed in the quick-list mode.
     *
     * @return boolean
     */
    protected function isQuickDisplayMode()
    {
        return $this->getParam(static::PARAM_DISPLAY_MODE) === static::DISPLAY_MODE_QUICK;
    }

    /**
     * Whether the widget is being displayed in the full-list mode.
     *
     * @return boolean
     */
    protected function isFullDisplayMode()
    {
        return $this->getParam(static::PARAM_DISPLAY_MODE) === static::DISPLAY_MODE_FULL;
    }

    /**
     * Whether the "Show all questions" link should be displayed.
     *
     * @return boolean
     */
    protected function isMoreLinkVisible()
    {
        return $this->isQuickDisplayMode() && $this->hasMoreQuestions();
    }

    /**
     * Check if there are more questions than can be displayed on the page.
     *
     * @return boolean
     */
    protected function hasMoreQuestions()
    {
        return $this->countVisibleQuestions() < $this->countAllQuestions();
    }

    /**
     * Get the number of questions visible to the current user.
     *
     * @return integer
     */
    protected function countVisibleQuestions()
    {
        if (!isset($this->questions)) {
            $this->defineQuestions();
        }

        return count($this->questions);
    }

    /**
     * Get the total number of questions which the current user can see for the product.
     *
     * @return integer
     */
    protected function countAllQuestions()
    {
        return $this->getRepository()->countQuestionsVisibleToUser(
            $this->getProductId(),
            $this->getProfile(),
            $this->getCurrentGuestUserQuestionIds()
        );
    }

    /**
     * Get the maximum number of questions to display in the widget.
     *
     * @return integer
     */
    protected function getMaxNumberOfItems()
    {
        return $this->isQuickDisplayMode() ? 5 : 0;
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return [
            'product_id' => $this->getProductId(),
            'displayMode' => $this->getParam(static::PARAM_DISPLAY_MODE),
        ];
    }
}
