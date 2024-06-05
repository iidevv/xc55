<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\AttributeOption\Hidden\DTO\AttributeOptionHiddenInput as InputHiddenOption;
use XLite\API\Endpoint\AttributeOption\Hidden\DTO\AttributeOptionHiddenOutput as OutputHiddenOption;
use XLite\API\Endpoint\AttributeOption\Select\DTO\AttributeOptionSelectInput as InputSelectOption;
use XLite\API\Endpoint\AttributeOption\Select\DTO\AttributeOptionSelectOutput as OutputSelectOption;

/**
 * @ORM\Entity
 * @ORM\Table (name="attribute_options")
 * @ApiPlatform\ApiResource(
 *     shortName="Attribute Option",
 *     itemOperations={
 *          "get_option_select"={
 *              "method"="GET",
 *              "path"="/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve an option from a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put_option_select"={
 *              "method"="PUT",
 *              "path"="/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Update an option of a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete_option_select"={
 *              "method"="DELETE",
 *              "path"="/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Delete an option from a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "get_option_hidden"={
 *              "method"="GET",
 *              "path"="/attributes_hidden/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHiddenOption::class,
 *              "output"=OutputHiddenOption::class,
 *              "requirements"={"attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve an option from a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put_option_hidden"={
 *              "method"="PUT",
 *              "path"="/attributes_hidden/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHiddenOption::class,
 *              "output"=OutputHiddenOption::class,
 *              "requirements"={"attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Update an option of a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete_option_hidden"={
 *              "method"="DELETE",
 *              "path"="/attributes_hidden/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHiddenOption::class,
 *              "output"=OutputHiddenOption::class,
 *              "requirements"={"attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Delete an option from a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_get_option_select"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"class_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve an option from a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_put_option_select"={
 *              "method"="PUT",
 *              "path"="/product_classes/{class_id}/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"class_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Update an option of a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_delete_option_select"={
 *              "method"="DELETE",
 *              "path"="/product_classes/{class_id}/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"class_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Delete an option from a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_get_option_select"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve an option from a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_put_option_select"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Update an option of a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_delete_option_select"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attributes_select/{attribute_id}/options/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Delete an option from a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get_option_selects"={
 *              "method"="GET",
 *              "path"="/attributes_select/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of options from a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "post_option_select"={
 *              "method"="POST",
 *              "controller"="xcart.api.attribute_option.select.controller",
 *              "path"="/attributes_select/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add an option to a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "get_option_hiddens"={
 *              "method"="GET",
 *              "path"="/attributes_hidden/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHiddenOption::class,
 *              "output"=OutputHiddenOption::class,
 *              "requirements"={"attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of options from a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "post_option_hidden"={
 *              "method"="POST",
 *              "controller"="xcart.api.attribute_option.hidden.controller",
 *              "path"="/attributes_hidden/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHiddenOption::class,
 *              "output"=OutputHiddenOption::class,
 *              "requirements"={"attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add an option to a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "product_class_based_get_option_selects"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_select/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"class_id"="\d+", "attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of options from a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "product_class_based_post_option_select"={
 *              "method"="POST",
 *              "controller"="xcart.api.attribute_option.select.product_class_controller",
 *              "path"="/product_classes/{class_id}/attributes_select/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"class_id"="\d+", "attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add an option to a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "product_based_get_option_selects"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_select/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of options from a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "product_based_post_option_select"={
 *              "method"="POST",
 *              "controller"="xcart.api.attribute_option.select.product_controller",
 *              "path"="/products/{product_id}/attributes_select/{attribute_id}/options.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelectOption::class,
 *              "output"=OutputSelectOption::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add an option to a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class AttributeOption extends \XLite\Model\Base\I18n
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var \XLite\Model\Attribute
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Attribute", inversedBy="attribute_options")
     * @ORM\JoinColumn (name="attribute_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute;

    /**
     * Add to new products or class’s assigns automatically
     *
     * @var bool
     *
     * @ORM\Column (type="boolean")
     */
    protected $addToNew = false;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeOptionTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set addToNew
     *
     * @param boolean $addToNew
     */
    public function setAddToNew($addToNew)
    {
        $this->addToNew = $addToNew;
    }

    /**
     * Get addToNew
     *
     * @return boolean
     */
    public function getAddToNew()
    {
        return $this->addToNew;
    }

    /**
     * Set attribute
     *
     * @param \XLite\Model\Attribute $attribute
     */
    public function setAttribute(\XLite\Model\Attribute $attribute = null)
    {
        $this->attribute = $attribute;
    }

    /**
     * Get attribute
     *
     * @return \XLite\Model\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
