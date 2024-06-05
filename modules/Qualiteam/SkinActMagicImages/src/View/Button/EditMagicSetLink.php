<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View\Button;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite\Model\WidgetParam\TypeInt;
use XLite\View\Button\Link;

class EditMagicSetLink extends Link
{
    use MagicImagesTrait;

    public const PARAM_MAGIC_SET_COUNT = 'magicSetCount';

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/button/less/edit_magic_set.less';

        return $list;
    }

    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_MAGIC_SET_COUNT => new TypeInt(
                'Magic set count',
                0,
            ),
        ];
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return '';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getButtonLabel(): string
    {
        return static::t('SkinActMagicImages edit images x', [
            'count' => $this->getMagicSetCount(),
        ]);
    }

    /**
     * @return int|null
     */
    protected function getMagicSetCount(): ?int
    {
        return $this->getParam(self::PARAM_MAGIC_SET_COUNT);
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass(): string
    {
        return 'edit-magic-set';
    }
}