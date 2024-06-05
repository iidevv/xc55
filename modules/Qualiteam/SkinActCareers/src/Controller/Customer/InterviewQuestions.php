<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Controller\Customer;


use Qualiteam\SkinActCareers\Core\Mailer;
use Qualiteam\SkinActCareers\Model\InterviewQuestion;
use XCart\Domain\ModuleManagerDomain;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;

class InterviewQuestions extends \XLite\Controller\Customer\ACustomer
{
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            static::t('SkinActCareers Careers'),
            $this->buildURL('careers')
        );
    }

    protected function getLocation()
    {
        return $this->getTitle();
    }


    protected function isVisible()
    {
        if ($jid = (int)Request::getInstance()->jid) {

            $job = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')->getEnabledJobById($jid);

            if ($job) {
                return true;
            }
        }

        return false;
    }

    protected function collectData()
    {
        $data = Request::getInstance()->getData();

        $result = [];

        foreach ($data as $key => $value) {
            if (strpos($key, 'q_') === 0) {
                $questionId = (int)str_replace('q_', '', $key);
                if ($questionId > 0) {
                    /** @var \Qualiteam\SkinActCareers\Model\InterviewQuestion $question */
                    $question = Database::getRepo('\Qualiteam\SkinActCareers\Model\InterviewQuestion')->find($questionId);
                    if ($question) {
                        if ($question->getType() === InterviewQuestion::TYPE_SELECT) {
                            $predefined = explode(';', $question->getPredefinedValues());
                            $value = $predefined[$value] ?? '';
                        }
                        // collect non empty values only
                        if ($value) {
                            $result[] = [
                                'question' => $question->getQuestion(),
                                'userInput' => $value
                            ];
                        }
                    }
                }
            }
        }

        return $result;
    }


    public function doActionSendProfile()
    {
        $this->getModelForm()->save();

        $reCaptcha = \CDev\ContactUs\Core\ReCaptcha::getInstance();

        $moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);

        $reCaptchaConfigured = false;

        if ($moduleManagerDomain->isEnabled('QSL-reCAPTCHA')
            && \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_public
            && \XLite\Core\Config::getInstance()->QSL->reCAPTCHA->google_recaptcha_private
        ) {
            $reCaptchaConfigured = true;
        }

        $reCaptchaError = false;

        if ($reCaptchaConfigured) {
            $data = \XLite\Core\Request::getInstance()->getData();
            $response = $reCaptcha->verify($data['g-recaptcha-response'] ?? '');

            if (!$response || !$response->isSuccess()) {
                $reCaptchaError = true;
                TopMessage::addError('Please enter the correct captcha');
            }
        }

        if (!$reCaptchaError) {
            $questionsData = $this->collectData();

            $success = false;

            if (!empty($questionsData)) {
                $jid = (int)Request::getInstance()->jid;
                if ($jid > 0) {

                    // send it
                    $files = Request::getInstance()->files;
                    $files = is_array($files) ? $files : [];

                    Mailer::sendNotificationInterviewQuestions(
                        $jid,
                        $questionsData,
                        $files
                    );

                    $success = true;

                    TopMessage::addInfo('SkinActCareers successfully sent');

                    foreach ($files as $file) {
                        $tmpFile = Database::getRepo('\XLite\Model\TemporaryFile')->find($file['temp_id'] ?? 0);
                        if ($tmpFile) {
                            $tmpFile->removeFile();
                        }

                    }
                }
            }

            if (!$success) {
                TopMessage::addError('SkinActCareers submission problem');
            }
        }
    }

    public function getTitle()
    {
        if ($jid = (int)Request::getInstance()->jid) {

            $job = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')->getEnabledJobById($jid);

            if ($job) {
                return $job->getTitle();
            }
        }

        return '';
    }

    protected function getModelFormClass()
    {
        return \Qualiteam\SkinActCareers\View\Model\CustomerQuestions::class;
    }

}
