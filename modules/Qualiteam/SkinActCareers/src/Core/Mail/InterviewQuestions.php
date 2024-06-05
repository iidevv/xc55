<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Core\Mail;

use XLite\Core\Database;
use XLite\Core\Mailer;

class InterviewQuestions extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'modules/Qualiteam/SkinActCareers/interview_questions';
    }

    public function __construct(int $jobId, array $questionsData, array $files)
    {
        parent::__construct();

        $job = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')->find($jobId);

        if ($job) {
            $this->populateVariables(['title' => $job->getTitle()]);
        }

        $this->appendData([
            'questionsData' => $questionsData,
        ]);

        if (!empty($files)) {

            foreach ($files as $ind => $data) {

                $tempId = (int)($data['temp_id'] ?? 0);

                if ($tempId > 0) {

                    $file = Database::getRepo('\XLite\Model\TemporaryFile')->find($tempId);

                    if ($file && $file->isFileExists()) {

                        $this->addStringAttachment([
                            file_get_contents($file->getStoragePath()),
                            $file->getFileName(),
                            'base64',
                            'application/octet-stream'
                        ]);
                    }
                }
            }
        }

        $this->setTo(Mailer::getSiteAdministratorMails());
        $this->setFrom(Mailer::getSiteAdministratorMail());
    }

    protected static function defineVariables()
    {
        return parent::defineVariables() + [
                'title' => ''
            ];
    }
}