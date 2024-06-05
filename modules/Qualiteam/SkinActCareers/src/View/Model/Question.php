<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Model;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;

/**
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Question extends \XLite\View\Model\AModel
{

    protected $schemaDefault = [
        'question' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL => 'SkinActCareers question',
            self::SCHEMA_REQUIRED => true,
        ],
        'type' => [
            self::SCHEMA_CLASS => '\Qualiteam\SkinActCareers\View\FormField\Select\QuestionTypeSelect',
            self::SCHEMA_LABEL => 'SkinActCareers question type',
            self::SCHEMA_REQUIRED => true,
        ],
        'predefinedValues' => [
            self::SCHEMA_CLASS => '\Qualiteam\SkinActCareers\View\FormField\Input\Text',
            self::SCHEMA_LABEL => 'SkinActCareers predefinedValues',
            self::SCHEMA_REQUIRED => true,
            self::SCHEMA_COMMENT => 'SkinActCareers predefinedValues comment',

            self::SCHEMA_DEPENDENCY => [
                self::DEPENDENCY_SHOW => [
                    'type' => \Qualiteam\SkinActCareers\Model\InterviewQuestion::TYPE_SELECT,
                ],
            ],
        ],
        'serviceName' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL => 'SkinActCareers serviceName',
        ],

    ];

    public static function getAllowedTargets()
    {
        return ['career_question'];
    }

    protected function getDefaultModelObject()
    {
        $qId = Request::getInstance()->id;

        $model = null;

        if ($qId) {
            $model = \XLite\Core\Database::getRepo('Qualiteam\SkinActCareers\Model\InterviewQuestion')->find($qId) ?: null;
        }

        return $model ?: new \Qualiteam\SkinActCareers\Model\InterviewQuestion();
    }


    protected function getFormClass()
    {
        return \Qualiteam\SkinActCareers\View\Form\Question::class;
    }
}