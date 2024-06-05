<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Post;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\AssemblerInterface;
use XLite\Model\Product;

class CreateAssembler implements AssemblerInterface
{
    protected string $path = '';

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Post\CreateConstructor $constructorCreate
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Assembler                       $assembler
     */
    public function __construct(
        private CreateConstructor $constructorCreate,
        private Assembler         $assembler,
    ) {
    }

    public function setProduct(?Product $product)
    {
        $this->constructorCreate->prepareProduct($product);
    }

    /**
     * Assembling a data to call endpoint
     */
    public function assemble(): void
    {
        $this->constructorCreate->build();

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
        return $this->constructorCreate->getBody();
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