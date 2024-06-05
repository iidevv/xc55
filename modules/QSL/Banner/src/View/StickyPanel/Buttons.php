<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\StickyPanel;

/**
 * Buttons
 */
class Buttons extends \XLite\View\Base\FormStickyPanel
{
    /**
     * Widget params names
     */
    public const BANNER_ID = 'bannerId';

    /**
     * Define widget params
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::BANNER_ID => new \XLite\Model\WidgetParam\TypeInt('Banner ID', 0),
        ];
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function getButtons()
    {
        return [
            'update' => $this->getWidget(
                [
                    'style'    => 'action submit',
                    'label'    => static::t('Update'),
                    \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button'
                ],
                'XLite\View\Button\Submit'
            ),
            'delete' => $this->getWidget(
                [
                    'label'  => static::t('Delete'),
                    'style'  => 'regular-button',
                    'jsCode' => 'update_code_' . $this->getBannerContentId() . '.banner_content_id.value=\'' . $this->getBannerContentId() . '\'; update_code_' . $this->getBannerContentId() . '.action.value=\'delete_content\'; update_code_' . $this->getBannerContentId() . '.submit()'
                ],
                '\XLite\View\Button\Link'
            )
        ];
    }

    /**
     * Return current banner id
     *
     * @return integer
     */
    public function getBannerContentId()
    {
        return intval($this->getParam(self::BANNER_ID));
    }

    /**
     * Check - sticky panel is active only if form is changed
     *
     * @return boolean
     */
    protected function isFormChangeActivation()
    {
        return false;
    }
}
