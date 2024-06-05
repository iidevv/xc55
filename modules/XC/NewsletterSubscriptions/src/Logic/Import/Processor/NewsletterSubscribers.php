<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\Logic\Import\Processor;

/**
 * Subscribers import processor
 */
class NewsletterSubscribers extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Subscribers imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XC\NewsletterSubscriptions\Model\Subscriber');
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'Email Address'       => [
                static::COLUMN_IS_KEY          => true,
                static::COLUMN_VERIFICATOR     => [$this, 'verifyEmail'],
                static::COLUMN_PROPERTY        => 'email',
            ],
        ];
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages() +
            [
                'SUBSCRIBER-EMAIL-FMT'      => 'Email is in wrong format',
            ];
    }

    /**
     * Verify 'email' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    public function verifyEmail($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsEmail($value)) {
            $this->addError('SUBSCRIBER-EMAIL-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    // }}}
}
