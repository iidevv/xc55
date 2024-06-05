<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler;
use XC\ProductVariants\Model\ProductVariant;

class UpdateAssembler
{
    protected string $path = '';

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch\UpdateConstructor $createConstructor
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler                             $assembler
     */
    public function __construct(
        private UpdateConstructor $createConstructor,
        private Assembler         $assembler,
    ) {
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant|null $variant
     *
     * @return void
     */
    public function setProductVariant(?ProductVariant $variant): void
    {
        $this->createConstructor->prepareProductVariant($variant);
    }
    /**
     * Assembling a data to call endpoint
     */
    public function assemble(): void
    {
        $this->createConstructor->build();

        $this->assembler->setBody(
            $this->getBody()
        );

        $this->assembler->setPath(
            $this->getPath()
        );

        $this->assembler->assemble();
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->createConstructor->getBody();
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }
}