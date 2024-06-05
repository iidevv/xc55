<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\Model;

use Qualiteam\SkinActVideoTour\Model\VideoTours as VideoToursModel;
use Qualiteam\SkinActVideoTour\View\Form\Model\VideoTour as VideoTourFormClass;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\View\Button\AButton;
use XLite\View\Button\Submit;
use XLite\View\FormField\Input\Checkbox\OnOff;
use XLite\View\FormField\Input\Text;
use XLite\View\FormField\Input\Text\Integer;
use XLite\View\FormField\Textarea\Simple;

/**
 * Class video tour
 */
class VideoTour extends \XLite\View\Model\AModel
{
    /**
     * @inheritdoc
     */
    public function __construct(array $params = [], array $sections = [])
    {
        $this->schemaDefault = [
            'video_url' => [
                self::SCHEMA_CLASS       => Text::class,
                self::SCHEMA_LABEL       => static::t('SkinActVideoTour video url'),
                self::SCHEMA_REQUIRED    => true,
            ],
            'description'       => [
                self::SCHEMA_CLASS    => Simple::class,
                self::SCHEMA_LABEL    => static::t('SkinActVideoTour video description'),
                self::SCHEMA_REQUIRED => false,
            ],
            'enabled' => [
                self::SCHEMA_CLASS => OnOff::class,
                self::SCHEMA_LABEL => static::t('SkinActVideoTour video enabled'),
                self::SCHEMA_REQUIRED => false,
            ],
            'position' => [
                self::SCHEMA_CLASS => Integer::class,
                self::SCHEMA_LABEL => static::t('SkinActVideoTour video position'),
            ],
        ];

        parent::__construct($params, $sections);
    }

    /**
     * Return current model ID
     *
     * @return int|null
     */
    public function getModelId(): ?int
    {
        return (int) Request::getInstance()->id;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return VideoToursModel
     */
    protected function getDefaultModelObject(): VideoToursModel
    {
        $model = Database::getRepo(VideoToursModel::class)->find($this->getModelId());

        return $model ?: new VideoToursModel();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass(): string
    {
        return VideoTourFormClass::class;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons(): array
    {
        $result = parent::getFormButtons();
        $label = $this->getModelObject()->getId()
            ? static::t('SkinActVideoTour update')
            : static::t('SkinActVideoTour create');

        $result['submit'] = new Submit([
            AButton::PARAM_LABEL    => $label,
            AButton::PARAM_BTN_TYPE => 'regular-main-button',
            AButton::PARAM_STYLE    => 'action',
        ]);

        return $result;
    }
}