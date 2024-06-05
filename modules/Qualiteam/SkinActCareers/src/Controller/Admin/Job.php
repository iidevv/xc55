<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Controller\Admin;


class Job extends \XLite\Controller\Admin\AAdmin
{
    protected function addBaseLocation()
    {
        $this->addLocationNode(
            static::t('SkinActCareers Careers'),
            $this->buildURL('jobs')
        );
        $this->addLocationNode(
            static::t('SkinActCareers Jobs'),
            $this->buildURL('jobs')
        );
    }


    public function getTitle()
    {
        if ($this->getJobId() > 0) {
            return $this->getModelForm()->getModelObject()->getTitle();
        }

        return static::t('SkinActCareers New job');
    }

    protected function getModelFormClass()
    {
        return \Qualiteam\SkinActCareers\View\Model\Job::class;
    }

    protected function getJobId()
    {
        return $this->getModelForm()->getModelObject()->getId();
    }

    protected function doActionCreateJob()
    {
        $this->getModelForm()->performAction('create');

        $this->setReturnURL($this->buildURL('job', '', ['id' => $this->getJobId()]));
    }

    protected function doActionUpdateJob()
    {
        $this->getModelForm()->performAction('update');

        $this->setReturnURL($this->buildURL('job', '', ['id' => $this->getJobId()]));
    }

}