<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Core\Notifications;


use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;
use XLite\Model\Repo\Product;

/**
 * DataPreProcessor
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XC\ThemeTweaker\Core\Notifications\DataPreProcessor
{
    public static function prepareDataForNotification($dir, array $data)
    {
        $data = parent::prepareDataForNotification($dir, $data);

        if ($dir === 'modules/Qualiteam/SkinActCareers/interview_questions') {
            $data = static::getDemoInterviewQuestions();
        }

        return $data;
    }

    protected static function getDemoInterviewQuestions()
    {
        $job = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')
            ->getJobForDemo();

        $demo = [];

        $demo[] = [
            'question' => 'What salary are you seeking?',
            'userInput' => '$5k'
        ];

        return [$job ? $job->getId() : 0, $demo, []];
    }


}