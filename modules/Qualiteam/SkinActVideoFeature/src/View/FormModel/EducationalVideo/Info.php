<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\FormModel\EducationalVideo;

use XLite\Core\Database;
use XLite\View\Button\AButton;
use XLite\View\Button\Submit;

class Info extends \XLite\View\FormModel\AFormModel
{
    /**
     * Do not render form_start and form_end in null returned
     *
     * @return string|null
     */
    protected function getTarget()
    {
        return 'educational_video';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'update';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        $identity = $this->getDataObject()->default->identity;

        return $identity ? ['id' => $identity] : [];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'form_model/product/style.less',
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = [
            self::SECTION_DEFAULT => [
                'description' => [
                    'label'       => static::t('SkinActVideoFeature description'),
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('This field is required'),
                        ],
                    ],
                    'required'    => false,
                    'position'    => 100,
                ],
                'video_code'  => [
                    'label'    => static::t('SkinActVideoFeature youtube embed video code or share url'),
                    'required' => false,
                    'position' => 200,
                    'help' => static::t('SkinActVideoFeature youtube embed video code or share url tooltip'),
                ],
                'youtube_video_id' => [
                    'label' => static::t('SkinActVideoFeature youtube video id'),
                    'type'    => 'XLite\View\FormModel\Type\CaptionType',
                    'caption' => $this->getDataObject()->default->youtube_video_id ?? static::t('SkinActVideoFeature youtube video id none'),
                ],
                'category'    => [
                    'label'    => static::t('SkinActVideoFeature category'),
                    'type'     => 'Qualiteam\SkinActVideoFeature\View\FormModel\Type\EducationalVideoCategoryType',
                    'multiple' => true,
                    'position' => 300,
                ],
            ],
        ];

        return $schema;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result   = parent::getFormButtons();
        $identity = $this->getDataObject()->default->identity;

        $label = $identity
            ? static::t('SkinActVideoFeature update video')
            : static::t('SkinActVideoFeature add video');

        $result['submit'] = new Submit(
            [
                AButton::PARAM_LABEL    => $label,
                AButton::PARAM_BTN_TYPE => 'regular-main-button',
                AButton::PARAM_STYLE    => 'action',
            ]
        );

        return $result;
    }

    protected function getVideoEntity()
    {
        return Database::getRepo('Qualiteam\SkinActVideoFeature\Model\EducationalVideo')->find($this->getDataObject()->default->identity);
    }
}