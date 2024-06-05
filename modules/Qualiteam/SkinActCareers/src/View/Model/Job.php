<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Model;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Converter;
use XLite\Core\Request;

/**
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Job extends \XLite\View\Model\AModel
{

    protected $schemaDefault = [
        'title' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL => 'SkinActCareers title',
            self::SCHEMA_REQUIRED => true,
        ],
        'briefDescription' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'SkinActCareers briefDescription',
        ],
        'pageDescription' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'SkinActCareers pageDescription',
        ],
        'duties' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'SkinActCareers duties',
        ],
        'requirements' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'SkinActCareers requirements',
        ],
        'compensation' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'SkinActCareers compensation',
        ],
        'employmentType' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'SkinActCareers employmentType',
        ],
        'probationTime' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Textarea\Advanced',
            self::SCHEMA_LABEL => 'SkinActCareers probationTime',
        ],
        'publicationDate' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Input\Text\Date',
            self::SCHEMA_LABEL => 'SkinActCareers publicationDate',
        ],

    ];

    public static function getAllowedTargets()
    {
        return ['job'];
    }

    protected function getDefaultModelObject()
    {
        $jobId = Request::getInstance()->id;

        $model = null;
        if ($jobId) {
            $model = \XLite\Core\Database::getRepo('Qualiteam\SkinActCareers\Model\Job')->find($jobId) ?: null;
        }

        return $model ?: new \Qualiteam\SkinActCareers\Model\Job();
    }


    protected function getFormClass()
    {
        return \Qualiteam\SkinActCareers\View\Form\Job::class;
    }

    protected function setModelProperties(array $data)
    {
        $time = strtotime($data['publicationDate'] ?? '') > 0 ? strtotime($data['publicationDate']) : Converter::time();

        $data['publicationDate'] = $time;

        parent::setModelProperties($data);
    }
}