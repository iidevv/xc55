<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Model;

use Qualiteam\SkinActCareers\Model\InterviewQuestion;
use XCart\Domain\ModuleManagerDomain;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\View\FormField\AFormField;

/**
 *
 * @ListChild (list="center", zone="customer", weight="100")
 */
class CustomerQuestions extends \XLite\View\Model\AModel
{

    public function save()
    {
        $requestData = $this->prepareDataForMapping();
        $this->saveFormData($requestData);
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/interview_questions_form.less';
        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/uploader.js';
        return $list;
    }

    protected function isVisible()
    {
        $job = null;

        if ($jid = (int)Request::getInstance()->jid) {

            $job = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')->getEnabledJobById($jid);
        }

        return $job && parent::isVisible();
    }

    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => 'SkinActCareers Send Profile',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'btn regular-main-button submit',
            ]
        );

        return $result;
    }

    public function __construct(array $params = [], array $sections = [])
    {
        $schema = [];

        $questions = Database::getRepo('\Qualiteam\SkinActCareers\Model\InterviewQuestion')
            ->getEnabledQuestions();

        /** @var InterviewQuestion $question */
        foreach ($questions as $question) {

            if ($question->getType() === InterviewQuestion::TYPE_PLAIN) {

                $schema['q_' . $question->getId()] = [
                    self::SCHEMA_CLASS => '\XLite\View\FormField\Input\Text',
                    self::SCHEMA_LABEL => $question->getQuestion(),
                    self::SCHEMA_REQUIRED => $question->getMandatory(),
                    self::SCHEMA_PLACEHOLDER => static::t('SkinActCareers placeholder prefix') . $question->getQuestion(),
                ];
            }


            if ($question->getType() === InterviewQuestion::TYPE_TEXTAREA) {

                $schema['q_' . $question->getId()] = [
                    self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Simple',
                    self::SCHEMA_LABEL => $question->getQuestion(),
                    self::SCHEMA_REQUIRED => $question->getMandatory(),
                    self::SCHEMA_PLACEHOLDER => static::t('SkinActCareers placeholder textarea'),
                ];
            }

            if ($question->getType() === InterviewQuestion::TYPE_SELECT) {

                $options = explode(';', $question->getPredefinedValues());

                $schema['q_' . $question->getId()] = [
                    self::SCHEMA_CLASS => '\XLite\View\FormField\Select\Regular',
                    self::SCHEMA_LABEL => $question->getQuestion(),
                    self::SCHEMA_REQUIRED => $question->getMandatory(),
                    self::SCHEMA_PLACEHOLDER => static::t('SkinActCareers placeholder textarea'),
                    self::SCHEMA_OPTIONS => $options
                ];
            }

            if (isset($schema['q_' . $question->getId()])) {

                $schema['q_' . $question->getId()][self::SCHEMA_ATTRIBUTES] = [
                    'class' => $question->getServiceName()
                ];
            }

        }

        $schema['files'] = [
            self::SCHEMA_CLASS => '\Qualiteam\SkinActCareers\View\FormField\FileUploader\FileUploader',
            self::SCHEMA_LABEL => static::t('SkinActCareers FileUploader'),
            self::SCHEMA_COMMENT => static::t('SkinActCareers FileUploader comment'),
        ];


        $moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);

        if ($moduleManagerDomain->isEnabled('QSL-reCAPTCHA')
            && \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_public
            && \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_private
        ) {
            $schema['google-recaptcha'] = [
                static::SCHEMA_CLASS => '\QSL\reCAPTCHA\View\FormField\ReCAPTCHA',
                static::SCHEMA_REQUIRED => true,
                static::SCHEMA_FIELD_ONLY => true,
                AFormField::PARAM_ID => 'google-recaptcha',
            ];
        }

        $this->schemaDefault = $schema;

        parent::__construct($params, $sections);

    }

    public static function getAllowedTargets()
    {
        return ['interview_questions'];
    }

    protected function getDefaultModelObject()
    {
        return null;
    }


    protected function getFormClass()
    {
        return \Qualiteam\SkinActCareers\View\Form\CustomerQuestions::class;
    }
}