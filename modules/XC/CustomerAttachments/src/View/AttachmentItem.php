<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\View;

/**
 * Row in attachment list
 */
class AttachmentItem extends \XLite\View\AView
{
    /**
     * Widget param
     */
    public const PARAM_ATTACHMENT = 'attachment';

    /**
     * Return widget default template
     *
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'modules/XC/CustomerAttachments/attachment_item.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ATTACHMENT => new \XLite\Model\WidgetParam\TypeObject('Item attachment', null, false, 'XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment'),
        ];
    }

    /**
     * Get attachment
     *
     * @return \XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment
     */
    protected function getAttachment()
    {
        return $this->getParam(static::PARAM_ATTACHMENT);
    }
}
