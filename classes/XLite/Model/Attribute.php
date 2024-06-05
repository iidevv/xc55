<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\Attribute\Checkbox\DTO\AttributeCheckboxInput as InputCheckbox;
use XLite\API\Endpoint\Attribute\Checkbox\DTO\AttributeCheckboxOutput as OutputCheckbox;
use XLite\API\Endpoint\Attribute\Hidden\DTO\AttributeHiddenInput as InputHidden;
use XLite\API\Endpoint\Attribute\Hidden\DTO\AttributeHiddenOutput as OutputHidden;
use XLite\API\Endpoint\Attribute\Select\DTO\AttributeSelectInput as InputSelect;
use XLite\API\Endpoint\Attribute\Select\DTO\AttributeSelectOutput as OutputSelect;
use XLite\API\Endpoint\Attribute\Text\DTO\AttributeTextInput as InputText;
use XLite\API\Endpoint\Attribute\Text\DTO\AttributeTextOutput as OutputText;
use XLite\API\Endpoint\ProductAttribute\Checkbox\DTO\ProductAttributeCheckboxInput as ProductAttributeInputCheckbox;
use XLite\API\Endpoint\ProductAttribute\Checkbox\DTO\ProductAttributeCheckboxOutput as ProductAttributeOutputCheckbox;
use XLite\API\Endpoint\ProductAttribute\Select\DTO\ProductAttributeSelectInput as ProductAttributeInputSelect;
use XLite\API\Endpoint\ProductAttribute\Select\DTO\ProductAttributeSelectOutput as ProductAttributeOutputSelect;
use XLite\API\Endpoint\ProductAttribute\Text\DTO\ProductAttributeTextInput as ProductAttributeInputText;
use XLite\API\Endpoint\ProductAttribute\Text\DTO\ProductAttributeTextOutput as ProductAttributeOutputText;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;

/**
 * @ORM\Entity
 * @ORM\Table (name="attributes")
 * @ApiPlatform\ApiResource(
 *     itemOperations={
 *          "get_text"={
 *              "method"="GET",
 *              "path"="/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a global textarea attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put_text"={
 *              "method"="PUT",
 *              "path"="/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Update a global textarea attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete_text"={
 *              "method"="DELETE",
 *              "path"="/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Delete a global textarea attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "get_checkbox"={
 *              "method"="GET",
 *              "path"="/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a global yes/no attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put_checkbox"={
 *              "method"="PUT",
 *              "path"="/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Update a global yes/no attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete_checkbox"={
 *              "method"="DELETE",
 *              "path"="/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Delete a global yes/no attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "get_select"={
 *              "method"="GET",
 *              "path"="/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put_select"={
 *              "method"="PUT",
 *              "path"="/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Update a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete_select"={
 *              "method"="DELETE",
 *              "path"="/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Delete a global plain field attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "get_hidden"={
 *              "method"="GET",
 *              "path"="/attributes_hidden/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHidden::class,
 *              "output"=OutputHidden::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put_hidden"={
 *              "method"="PUT",
 *              "path"="/attributes_hidden/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHidden::class,
 *              "output"=OutputHidden::class,
 *              "openapi_context"={
 *                  "summary"="Update a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete_hidden"={
 *              "method"="DELETE",
 *              "path"="/attributes_hidden/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHidden::class,
 *              "output"=OutputHidden::class,
 *              "openapi_context"={
 *                  "summary"="Delete a global hidden attribute",
 *                  "parameters"={
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_get_text"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a product class textarea attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_put_text"={
 *              "method"="PUT",
 *              "path"="/product_classes/{class_id}/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Update a product class textarea attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_delete_text"={
 *              "method"="DELETE",
 *              "path"="/product_classes/{class_id}/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Delete a product class textarea attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_get_checkbox"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a product class yes/no attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_put_checkbox"={
 *              "method"="PUT",
 *              "path"="/product_classes/{class_id}/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Update a product class yes/no attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_delete_checkbox"={
 *              "method"="DELETE",
 *              "path"="/product_classes/{class_id}/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Delete a product class yes/no attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_get_select"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_put_select"={
 *              "method"="PUT",
 *              "path"="/product_classes/{class_id}/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Update a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_delete_select"={
 *              "method"="DELETE",
 *              "path"="/product_classes/{class_id}/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Delete a product class plain field attribute",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_get_text"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputText::class,
 *              "output"=ProductAttributeOutputText::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a product-specific textarea attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_put_text"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputText::class,
 *              "output"=ProductAttributeOutputText::class,
 *              "openapi_context"={
 *                  "summary"="Update a product-specific textarea attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_delete_text"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attributes_text/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputText::class,
 *              "output"=ProductAttributeOutputText::class,
 *              "openapi_context"={
 *                  "summary"="Delete a product-specific textarea attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_get_checkbox"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputCheckbox::class,
 *              "output"=ProductAttributeOutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a product-specific yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_put_checkbox"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputCheckbox::class,
 *              "output"=ProductAttributeOutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Update a product-specific yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_delete_checkbox"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attributes_checkbox/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputCheckbox::class,
 *              "output"=ProductAttributeOutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Delete a product-specific yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_get_select"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputSelect::class,
 *              "output"=ProductAttributeOutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_put_select"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputSelect::class,
 *              "output"=ProductAttributeOutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Update a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_delete_select"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attributes_select/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputSelect::class,
 *              "output"=ProductAttributeOutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Delete a product-specific plain field attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get_texts"={
 *              "method"="GET",
 *              "path"="/attributes_text.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of global textarea attributes",
 *              }
 *          },
 *          "post_text"={
 *              "method"="POST",
 *              "path"="/attributes_text.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "controller"="xcart.api.attribute.text.controller",
 *              "openapi_context"={
 *                  "summary"="Create a global textarea attribute",
 *              }
 *          },
 *          "get_checkboxes"={
 *              "method"="GET",
 *              "path"="/attributes_checkbox.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of global yes/no attributes",
 *              }
 *          },
 *          "post_checkbox"={
 *              "method"="POST",
 *              "path"="/attributes_checkbox.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "controller"="xcart.api.attribute.checkbox.controller",
 *              "openapi_context"={
 *                  "summary"="Create a global yes/no attribute",
 *              }
 *          },
 *          "get_selects"={
 *              "method"="GET",
 *              "path"="/attributes_select.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of global plain field attributes",
 *              }
 *          },
 *          "post_select"={
 *              "method"="POST",
 *              "path"="/attributes_select.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "controller"="xcart.api.attribute.select.controller",
 *              "openapi_context"={
 *                  "summary"="Create a global plain field attribute",
 *              }
 *          },
 *          "get_hiddens"={
 *              "method"="GET",
 *              "path"="/attributes_hidden.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHidden::class,
 *              "output"=OutputHidden::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of global hidden attributes",
 *              }
 *          },
 *          "post_hidden"={
 *              "method"="POST",
 *              "path"="/attributes_hidden.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputHidden::class,
 *              "output"=OutputHidden::class,
 *              "controller"="xcart.api.attribute.hidden.controller",
 *              "openapi_context"={
 *                  "summary"="Create a global hidden attribute",
 *              }
 *          },
 *          "product_class_based_get_texts"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_text.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of product class textarea attributes",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_post_text"={
 *              "method"="POST",
 *              "path"="/product_classes/{class_id}/attributes_text.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputText::class,
 *              "output"=OutputText::class,
 *              "controller"="xcart.api.attribute.text.product_class_based_controller",
 *              "openapi_context"={
 *                  "summary"="Add a textarea attribute to a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_get_checkboxes"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_checkbox.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of product class yes/no attributes",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_post_checkbox"={
 *              "method"="POST",
 *              "path"="/product_classes/{class_id}/attributes_checkbox.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputCheckbox::class,
 *              "output"=OutputCheckbox::class,
 *              "controller"="xcart.api.attribute.checkbox.product_class_based_controller",
 *              "openapi_context"={
 *                  "summary"="Add a yes/no attribute to a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_get_selects"={
 *              "method"="GET",
 *              "path"="/product_classes/{class_id}/attributes_select.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of product class plain field attributes",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_class_based_post_select"={
 *              "method"="POST",
 *              "path"="/product_classes/{class_id}/attributes_select.{_format}",
 *              "identifiers"={"id"},
 *              "input"=InputSelect::class,
 *              "output"=OutputSelect::class,
 *              "controller"="xcart.api.attribute.select.product_class_based_controller",
 *              "openapi_context"={
 *                  "summary"="Add a plain field attribute to a product class",
 *                  "parameters"={
 *                     {"name"="class_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_get_texts"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_text.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputText::class,
 *              "output"=ProductAttributeOutputText::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of product-specific textarea attributes",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_post_text"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/attributes_text.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputText::class,
 *              "output"=ProductAttributeOutputText::class,
 *              "controller"="xcart.api.attribute.text.product_based_controller",
 *              "openapi_context"={
 *                  "summary"="Add a textarea attribute to a product",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_get_checkboxes"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_checkbox.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputCheckbox::class,
 *              "output"=ProductAttributeOutputCheckbox::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of product-specific yes/no attributes",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_post_checkbox"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/attributes_checkbox.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputCheckbox::class,
 *              "output"=ProductAttributeOutputCheckbox::class,
 *              "controller"="xcart.api.attribute.checkbox.product_based_controller",
 *              "openapi_context"={
 *                  "summary"="Add a yes/no attribute to a product",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_get_selects"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_select.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputSelect::class,
 *              "output"=ProductAttributeOutputSelect::class,
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of product-specific plain field attributes",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "product_based_post_select"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/attributes_select.{_format}",
 *              "identifiers"={"id"},
 *              "input"=ProductAttributeInputSelect::class,
 *              "output"=ProductAttributeOutputSelect::class,
 *              "controller"="xcart.api.attribute.select.product_based_controller",
 *              "openapi_context"={
 *                  "summary"="Add a plain field attribute to a product",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Attribute extends \XLite\Model\Base\I18n
{
    use ExecuteCachedTrait;

    /*
     * Attribute types
     */
    public const TYPE_TEXT     = 'T';
    public const TYPE_CHECKBOX = 'C';
    public const TYPE_SELECT   = 'S';
    public const TYPE_HIDDEN   = 'H';

    /*
     * Add to new products or class’s assigns automatically with select value
     */
    public const ADD_TO_NEW_YES    = 'Y'; // 'Yes'
    public const ADD_TO_NEW_NO     = 'N'; // 'NO'
    public const ADD_TO_NEW_YES_NO = 'B'; // 'YES/NO' (BOTH)

    /*
     * Attribute delimiter
     */
    public const DELIMITER = ', ';

    /*
     * Display modes
     */
    public const SELECT_BOX_MODE = 'S';
    public const SPECIFICATION_MODE = 'P';
    public const BLOCKS_MODE     = 'B';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Is attribute shown above the price
     *
     * @var bool
     *
     * @ORM\Column (type="boolean", options={"default":"0"})
     */
    protected $displayAbove = false;

    /**
     * @var int
     *
     * @ORM\Column (type="integer", length=1)
     */
    protected $decimals = 0;

    /**
     * @var \XLite\Model\ProductClass
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\ProductClass", inversedBy="attributes")
     * @ORM\JoinColumn (name="product_class_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $productClass;

    /**
     * @var \XLite\Model\AttributeGroup
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\AttributeGroup", inversedBy="attributes")
     * @ORM\JoinColumn (name="attribute_group_id", referencedColumnName="id")
     */
    protected $attributeGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeOption", mappedBy="attribute", cascade={"all"})
     */
    protected $attribute_options;

    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product", inversedBy="attributes")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Option type
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=1)
     */
    protected $type = self::TYPE_SELECT;

    /**
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=1)
     */
    protected $displayMode = '';

    /**
     * Add to new products or class’s assigns automatically
     *
     * @var bool
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=1)
     */
    protected $addToNew = '';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeProperty", mappedBy="attribute")
     */
    protected $attribute_properties;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Return name of widget class
     *
     * @param string $type      Attribute type
     * @param string $interface Interface (Admin | Customer) OPTIONAL
     *
     * @return string
     */
    public static function getWidgetClass($type, $interface = null)
    {
        if ($interface === null) {
            $interface = \XLite::isAdminZone() ? 'Admin' : 'Customer';
        }

        return '\XLite\View\Product\AttributeValue\\'
            . $interface
            . '\\'
            . static::getTypes($type, true);
    }

    /**
     * Return name of value class
     *
     * @param string $type Type
     *
     * @return string
     */
    public static function getAttributeValueClass($type)
    {
        return '\XLite\Model\AttributeValue\AttributeValue'
            . static::getTypes($type, true);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->attribute_options = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return number of products associated with this attribute
     *
     * @return integer
     */
    public function getProductsCount()
    {
        return $this->getClass()->getProductsCount();
    }

    /**
     * Return list of types or type
     *
     * @param string  $type              Type OPTIONAL
     * @param boolean $returnServiceType Return service type OPTIONAL
     *
     * @return array | string
     */
    public static function getTypes($type = null, $returnServiceType = false)
    {
        $list = [
            static::TYPE_SELECT   => static::t('Plain field'),
            static::TYPE_TEXT     => static::t('Textarea'),
            static::TYPE_CHECKBOX => static::t('Yes/No'),
            static::TYPE_HIDDEN   => static::t('Hidden field'),
        ];

        $listServiceTypes = [
            static::TYPE_SELECT   => 'Select',
            static::TYPE_TEXT     => 'Text',
            static::TYPE_CHECKBOX => 'Checkbox',
            static::TYPE_HIDDEN   => 'Hidden',
        ];

        $list = $returnServiceType ? $listServiceTypes : $list;

        return $type !== null
            ? ($list[$type] ?? null)
            : $list;
    }

    /**
     * Return list of 'addToNew' types
     *
     * @return array
     */
    public static function getAddToNewTypes()
    {
        return [
            static::ADD_TO_NEW_YES,
            static::ADD_TO_NEW_NO,
            static::ADD_TO_NEW_YES_NO,
        ];
    }

    /**
     * Return values associated with this attribute
     *
     * @return mixed
     */
    public function getAttributeValues()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->attribute = $this;

        return Database::getRepo(static::getAttributeValueClass($this->getType()))
            ->search($cnd);
    }

    /**
     * Return number of values associated with this attribute
     *
     * @return integer
     */
    public function getAttributeValuesCount()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->attribute = $this;

        return Database::getRepo(static::getAttributeValueClass($this->getType()))
            ->search($cnd, true);
    }

    /**
     * Set 'addToNew' value
     *
     * @param string|array $value Value
     *
     * @return void
     */
    public function setAddToNew($value)
    {
        if (
            is_array($value)
            && $this->getType() === static::TYPE_CHECKBOX
        ) {
            if (count($value) === 2) {
                $value = static::ADD_TO_NEW_YES_NO;
            } elseif (count($value) === 1) {
                $value = array_shift($value) ? static::ADD_TO_NEW_YES : static::ADD_TO_NEW_NO;
            }
        }

        $this->addToNew = in_array($value, static::getAddToNewTypes()) ? $value : '';
    }

    /**
     * Get 'addToNew' value
     *
     * @return array
     */
    public function getAddToNew()
    {
        $value = null;
        if ($this->getType() === static::TYPE_CHECKBOX) {
            switch ($this->addToNew) {
                case static::ADD_TO_NEW_YES:
                    $value = [1];
                    break;

                case static::ADD_TO_NEW_NO:
                    $value = [0];
                    break;

                case static::ADD_TO_NEW_YES_NO:
                    $value = [0, 1];
                    break;

                default:
            }
        }

        return $value;
    }

    /**
     * Set type
     *
     * @param string $type Type
     *
     * @return void
     */
    public function setType($type)
    {
        $types = static::getTypes();

        if (isset($types[$type])) {
            if (
                $this->type
                && $type != $this->type
                && $this->getId()
            ) {
                foreach ($this->getAttributeOptions() as $option) {
                    Database::getEM()->remove($option);
                }
                foreach ($this->getAttributeValues() as $value) {
                    Database::getEM()->remove($value);
                }
            }
            $this->type = $type;
        }
    }

    /**
     * Return product property (return new property if property does not exist)
     *
     * @param \XLite\Model\Product $product Product OPTIONAL
     *
     * @return \XLite\Model\AttributeProperty
     */
    public function getProperty($product)
    {
        return $this->executeCachedRuntime(function () use ($product) {
            $property = null;

            if ($product->getProductId()) {
                $property = Database::getRepo(\XLite\Model\AttributeProperty::class)->findOneBy([
                    'product' => $product,
                    'attribute'  => $this,
                ]);

                if ($property === null) {
                    $property = $this->getNewProperty($product);
                }
            }

            return $property;
        }, ['getProperty', $this->getId(), $product->getProductId()]);
    }

    /**
     * Return new product property
     *
     * @param \XLite\Model\Product $product Product OPTIONAL
     *
     * @return \XLite\Model\AttributeProperty
     */
    protected function getNewProperty($product)
    {
        $result = new \XLite\Model\AttributeProperty();
        $result->setAttribute($this);
        $result->setProduct($product);
        $result->setDisplayAbove($this->getDisplayAbove());
        $this->addAttributeProperty($result);
        Database::getEM()->persist($result);

        return $result;
    }

    /**
     * Returns position
     *
     * @param \XLite\Model\Product $product Product OPTIONAL
     *
     * @return integer
     */
    public function getPosition($product = null)
    {
        if ($product) {
            $result = $this->getProperty($product);
            $result = $result ? $result->getPosition() : 0;
        } else {
            $result = $this->position;
        }

        return $result;
    }

    /**
     * Set the position
     *
     * @param integer|array $value
     *
     * @return void
     */
    public function setPosition($value)
    {
        if (is_array($value)) {
            $property = $this->getProperty($value['product']);
            $property->setPosition($value['position']);
        } else {
            $this->position = $value;
        }
    }

    /**
     * @param \XLite\Model\Product $product Product OPTIONAL
     *
     * @return integer
     */
    public function getDisplayAbove($product = null)
    {
        if ($product) {
            $result = $this->getProperty($product);
            $result = $result ? $result->getDisplayAbove() : $this->displayAbove;
        } else {
            $result = $this->displayAbove;
        }

        return $result;
    }

    /**
     * @param boolean|array $value
     *
     * @return void
     */
    public function setDisplayAbove($value)
    {
        if (is_array($value)) {
            $property = $this->getProperty($value['product']);
            $property->setDisplayAbove($value['displayAbove']);
        } else {
            $this->displayAbove = $value;
        }
    }

    /**
     * Add to new product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public function addToNewProduct(\XLite\Model\Product $product)
    {
        $displayAbove = $this->getDisplayAbove();

        if ($this->getAddToNew()) {
            $displayAbove = count($this->getAddToNew()) > 1 ?: $displayAbove;

            foreach ($this->getAddToNew() as $value) {
                $av = $this->createAttributeValue($product);
                if ($av) {
                    $av->setValue($value);
                }
            }
        } elseif ($this->getType() === static::TYPE_SELECT) {
            $attributeOptions = Database::getRepo(\XLite\Model\AttributeOption::class)->findBy(
                [
                    'attribute' => $this,
                    'addToNew'  => true,
                ],
                ['position' => 'ASC']
            );

            $displayAbove = count($attributeOptions) > 1 ?: $displayAbove;

            foreach ($attributeOptions as $attributeOption) {
                $av = $this->createAttributeValue($product);
                if ($av) {
                    $av->setAttributeOption($attributeOption);
                    $av->setPosition($attributeOption->getPosition());
                }
            }
        } elseif ($this->getType() === static::TYPE_TEXT) {
            $av = $this->createAttributeValue($product);
            if ($av) {
                $av->setEditable(false);
                $av->setValue('');
            }
        } elseif ($this->getType() === static::TYPE_HIDDEN) {
            $attributeOption = Database::getRepo(\XLite\Model\AttributeOption::class)->findOneBy(
                [
                    'attribute' => $this,
                    'addToNew'  => true,
                ]
            );

            if ($attributeOption) {
                $av = $this->createAttributeValue($product);
                if ($av) {
                    $av->setAttributeOption($attributeOption);
                }
            }
        }

        $this->setDisplayAbove(
            [
                'product' => $product,
                'displayAbove' => $displayAbove,
            ]
        );
    }

    /**
     * Apply changes
     *
     * @param \XLite\Model\Product $product Product
     * @param mixed                $changes Changes
     *
     * @return void
     */
    public function applyChanges(\XLite\Model\Product $product, $changes)
    {
        if (
            (
                !$this->getProductClass()
                && !$this->getProduct()
            )
            || (
                $this->getProductClass()
                && $product->getProductClass()
                && $this->getProductClass()->getId() == $product->getProductClass()->getId()
            )
            || ($this->getProduct()
                && $this->getProduct()->getId() == $product->getId()
            )
        ) {
            $class = static::getAttributeValueClass($this->getType());
            $repo = Database::getRepo($class);

            switch ($this->getType()) {
                case static::TYPE_TEXT:
                    $this->setAttributeValue($product, $changes);
                    break;

                case static::TYPE_CHECKBOX:
                case static::TYPE_SELECT:
                    foreach ($repo->findBy(['product' => $product, 'attribute' => $this]) as $av) {
                        $uniq = $this->getType() === static::TYPE_CHECKBOX
                            ? $av->getValue()
                            : $av->getAttributeOption()->getId();

                        if (in_array($uniq, $changes['deleted'])) {
                            $repo->delete($av, false);
                        } elseif (
                            isset($changes['changed'][$uniq])
                            || isset($changes['added'][$uniq])
                        ) {
                            $data = $changes['changed'][$uniq] ?? $changes['added'][$uniq];

                            if (
                                isset($data['defaultValue'])
                                && $data['defaultValue']
                                && !$av->getDefaultValue()
                            ) {
                                $pr = $repo->findOneBy(
                                    [
                                        'product'      => $product,
                                        'attribute'    => $this,
                                        'defaultValue' => true
                                    ]
                                );
                                if ($pr) {
                                    $pr->setDefaultValue(false);
                                }
                            }

                            $repo->update($av, $data);

                            if (isset($changes['added'][$uniq])) {
                                unset($changes['added'][$uniq]);
                            }
                        }
                    }

                    if ($changes['added']) {
                        foreach ($changes['added'] as $uniq => $data) {
                            if (
                                isset($data['defaultValue'])
                                && $data['defaultValue']
                            ) {
                                $pr = $repo->findOneBy(
                                    [
                                        'product'      => $product,
                                        'attribute'    => $this,
                                        'defaultValue' => true
                                    ]
                                );
                                if ($pr) {
                                    $pr->setDefaultValue(false);
                                }
                            }
                            $av = $this->createAttributeValue($product);

                            if ($av) {
                                if ($this->getType() === static::TYPE_CHECKBOX) {
                                    $av->setValue($uniq);
                                } else {
                                    $av->setAttributeOption(
                                        Database::getRepo(\XLite\Model\AttributeOption::class)->find($uniq)
                                    );
                                }
                                $repo->update($av, $data);
                            }
                        }
                    }
                    break;

                default:
            }
            Database::getEM()->flush();
        }
    }

    /**
     * Set attribute value
     *
     * @param \XLite\Model\Product $product Product
     * @param mixed                $data    Value
     *
     * @return void
     */
    public function setAttributeValue(\XLite\Model\Product $product, $data)
    {
        $repo = Database::getRepo(
            static::getAttributeValueClass($this->getType())
        );

        $method = $this->defineSetAttributeValueMethodName($data);
        $this->$method($repo, $product, $data);
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Product $product  Product
     * @param boolean              $asString As string flag OPTIONAL
     *
     * @return mixed
     */
    public function getAttributeValue(\XLite\Model\Product $product, $asString = false)
    {
        $repo = Database::getRepo(static::getAttributeValueClass($this->getType()));

        if (in_array($this->getType(), [static::TYPE_SELECT, static::TYPE_CHECKBOX, static::TYPE_HIDDEN])) {
            $attributeValue = $repo->findBy(
                ['product' => $product, 'attribute' => $this],
                $this->getType() === static::TYPE_SELECT ? ['position' => 'ASC'] : null
            );

            if (
                $attributeValue
                && $asString
            ) {
                if (is_array($attributeValue)) {
                    foreach ($attributeValue as $k => $v) {
                        $attributeValue[$k] = $v->asString();
                    }
                } elseif (is_object($attributeValue)) {
                    $attributeValue = $attributeValue->asString();
                } elseif ($this->getType() === static::TYPE_CHECKBOX) {
                    $attributeValue = static::t('Yes');
                }
            }
        } else {
            $attributeValue = $repo->findOneBy(
                ['product' => $product, 'attribute' => $this]
            );
            if ($attributeValue && $asString) {
                $attributeValue = $attributeValue->getValue();
            }
        }

        return $attributeValue;
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    public function getDefaultAttributeValue(\XLite\Model\Product $product)
    {
        $repo = Database::getRepo(static::getAttributeValueClass($this->getType()));

        $attributeValue = $repo->findOneBy(['product' => $product, 'attribute' => $this, 'defaultValue' => true]);
        if (!$attributeValue) {
            $attributeValue = $repo->findDefaultAttributeValue(['product' => $product, 'attribute' => $this]);
        }

        return $attributeValue;
    }

    /**
     * This attribute is multiple or not flag
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return boolean
     */
    public function isMultiple(\XLite\Model\Product $product)
    {
        $repo = Database::getRepo(static::getAttributeValueClass($this->getType()));

        return (!$this->getProduct() || $this->getProduct()->getId() == $product->getId())
            && (!$this->getProductClass()
                || ($product->getProductClass()
                    && $this->getProductClass()->getId() == $product->getProductClass()->getId()
                )
            )
            && 1 < count($repo->findBy(['product' => $product, 'attribute' => $this]));
    }

    /**
     * This attribute is hidden or not flag
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->getType() === static::TYPE_HIDDEN;
    }

    /**
     * Create attribute value
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return mixed
     */
    protected function createAttributeValue(\XLite\Model\Product $product)
    {
        $class = static::getAttributeValueClass($this->getType());

        $attributeValue = new $class();
        $attributeValue->setProduct($product);
        $attributeValue->setAttribute($this);
        Database::getEM()->persist($attributeValue);

        return $attributeValue;
    }

    /**
     * Create attribute option
     *
     * @param string $value Option name
     *
     * @return \XLite\Model\AttributeOption
     */
    protected function createAttributeOption($value)
    {
        $attributeOption = new \XLite\Model\AttributeOption();
        $attributeOption->setAttribute($this);
        $attributeOption->setName($value);

        Database::getEM()->persist($attributeOption);

        return $attributeOption;
    }

    // {{{ Set attribute value

    /**
     * Define method name for 'setAttributeValue' operation
     *
     * @param mixed $data Data
     *
     * @return string
     */
    protected function defineSetAttributeValueMethodName($data)
    {
        if ($this->getType() === static::TYPE_SELECT) {
            $result = 'setAttributeValueSelect';
        } elseif ($this->getType() === static::TYPE_CHECKBOX && isset($data['multiple']) && $data['multiple']) {
            $result = 'setAttributeValueCheckbox';
        } elseif ($this->getType() === static::TYPE_HIDDEN) {
            $result = 'setAttributeValueHidden';
        } else {
            $result = 'setAttributeValueDefault';
        }

        return $result;
    }

    /**
     * Set attribute value (select)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     *
     * @return void
     */
    protected function setAttributeValueSelect(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data
    ) {
        $ids = [];
        krsort($data['value']);
        foreach ($data['value'] as $id => $value) {
            $value = trim($value);
            if (strlen($value) > 0 && is_int($id)) {
                if (!isset($data['deleteValue'][$id])) {
                    [$avId] = $this->setAttributeValueSelectItem($repo, $product, $data, $id, $value);
                    $ids[$avId] = $avId;
                }

                if (!isset($data['multiple'])) {
                    break;
                }
            }
        }

        foreach ($repo->findBy(['product' => $product, 'attribute' => $this]) as $data) {
            if ($data->getId() && !isset($ids[$data->getId()])) {
                $repo->delete($data, false);
            }
        }
    }

    /**
     * Set select attribute item
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     * @param integer                 $id      Attribute value ID
     * @param mixed                   $value   Attribute value
     *
     * @return array
     */
    protected function setAttributeValueSelectItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $id,
        $value
    ) {
        $result = [null, null, null];

        $attributeValue = $attributeOption = null;

        if ($this->getProduct() && 0 < $id && !isset($data['ignoreIds'])) {
            $attributeValue = $repo->find($id);
            if ($attributeValue) {
                $attributeOption = $attributeValue->getAttributeOption();
                $attributeOption->setName($value);
            }
        }

        if (!$attributeOption) {
            $attributeOption = Database::getRepo(\XLite\Model\AttributeOption::class)
                ->findOneByNameAndAttribute($value, $this);
        }

        if (!$attributeOption) {
            $attributeOption = $this->createAttributeOption($value);
        } else {
            $attributeValue = $repo->findOneBy(
                [
                    'attribute_option' => $attributeOption,
                    'product' => $product,
                ]
            );
        }

        if (!$attributeValue && 0 < $id && !isset($data['ignoreIds'])) {
            $attributeValue = $repo->find($id);
        }

        if ($attributeValue) {
            $result[0] = $attributeValue->getId();
        } elseif ($attributeOption) {
            $attributeValue = $this->createAttributeValue($product);

            $attributeValue->setPosition(
                array_reduce($product->getAttributeValueS()->toArray(), function ($carry, $item) {
                    /* @var \XLite\Model\AttributeValue\AttributeValueSelect $item */
                    return $item->getAttribute() === $this
                        ? max($carry, $item->getPosition())
                        : $carry;
                }, 0) + 10
            );

            $product->addAttributeValueS($attributeValue);
        }

        if ($attributeValue) {
            $attributeValue->setAttributeOption($attributeOption);
            $attributeValue->setDefaultValue(isset($data['default'][$id]));
            foreach ($attributeValue::getModifiers() as $modifier => $options) {
                if (isset($data[$modifier]) && isset($data[$modifier][$id])) {
                    $attributeValue->setModifier($data[$modifier][$id], $modifier);
                }
            }

            Database::getEM()->flush();
            $result = [
                $attributeValue->getId(),
                $attributeValue,
                $attributeOption,
            ];
        }

        return $result;
    }

    /**
     * Set attribute value (checkbox)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     *
     * @return void
     */
    protected function setAttributeValueCheckbox(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data
    ) {
        foreach ([true, false] as $value) {
            $this->setAttributeValueCheckboxItem($repo, $product, $data, $value);
        }
    }

    /**
     * Set attribute value (checkbox item)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     * @param boolean|int             $value   Item value
     *
     * @return \XLite\Model\AttributeValue\AttributeValueCheckbox
     */
    protected function setAttributeValueCheckboxItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $value
    ) {
        $attributeValue = $repo->findOneBy(
            [
                'product'   => $product,
                'attribute' => $this,
                'value'     => $value,
            ]
        );

        if (!$attributeValue) {
            $attributeValue = $this->createAttributeValue($product);
            $attributeValue->setValue($value);
        }

        $value = (int) $value;
        $attributeValue->setDefaultValue(isset($data['default'][$value]));
        foreach ($attributeValue::getModifiers() as $modifier => $options) {
            if (isset($data[$modifier]) && isset($data[$modifier][$value])) {
                $attributeValue->setModifier($data[$modifier][$value], $modifier);
            }
        }

        return $attributeValue;
    }

    /**
     * Set attribute value (hidden)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     *
     * @return void
     */
    protected function setAttributeValueHidden(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data
    ) {
        $value = $data['value'];

        if (is_array($value)) {
            $value = end($value);
        }
        $value = trim($value);

        if (strlen($value) != 0) {
            $this->setAttributeValueHiddenItem($repo, $product, $data, $value);
        } else {
            $attributeValue = $repo->findOneBy(
                [
                    'attribute' => $this,
                    'product' => $product,
                ]
            );

            if ($attributeValue) {
                $repo->delete($attributeValue);
            }
        }
    }

    /**
     * Set hidden attribute item
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param array                   $data    Data
     * @param mixed                   $value   Attribute value
     *
     * @return \XLite\Model\AttributeValue\AttributeValueHidden
     */
    protected function setAttributeValueHiddenItem(
        \XLite\Model\Repo\ARepo $repo,
        \XLite\Model\Product $product,
        array $data,
        $value
    ) {
        $attributeValue = $repo->findOneBy(
            [
                'attribute' => $this,
                'product' => $product,
            ]
        );

        $attributeOption = Database::getRepo(\XLite\Model\AttributeOption::class)
            ->findOneByNameAndAttribute($value, $this);

        if (!$attributeOption) {
            $attributeOption = $this->createAttributeOption($value);
        }

        if (!$attributeValue) {
            $attributeValue = $this->createAttributeValue($product);
            $product->addAttributeValueH($attributeValue);
        }

        if ($attributeValue) {
            $attributeValue->setAttributeOption($attributeOption);

            Database::getEM()->flush();
        }

        return $attributeValue;
    }

    /**
     * Set attribute value (default)
     *
     * @param \XLite\Model\Repo\ARepo $repo    Repository
     * @param \XLite\Model\Product    $product Product
     * @param mixed                   $data    Data
     *
     * @return \XLite\Model\AttributeValue\AttributeValueText
     */
    protected function setAttributeValueDefault(\XLite\Model\Repo\ARepo $repo, \XLite\Model\Product $product, $data)
    {
        $editable = is_array($data) && $this->getType() === static::TYPE_TEXT && isset($data['editable'])
            ? (bool) preg_match('/^1|yes|y|on$/iS', $data['editable'])
            : null;
        $value = is_array($data) ? $data['value'] : $data;
        $value = is_null($value) ? '' : $value;
        if (is_array($value)) {
            $value = array_shift($value);
        }
        $delete = true;
        $attributeValue = null;

        if ($value !== '' || $editable !== null || $this->getType() === static::TYPE_TEXT) {
            $attributeValue = $repo->findOneBy(['product' => $product, 'attribute' => $this]);

            if (!$attributeValue) {
                $attributeValue = $this->createAttributeValue($product);
                $delete = false;
            }

            $attributeValue->setValue($value);
            if ($editable !== null) {
                $attributeValue->setEditable($editable);
            }
        }

        if ($delete) {
            foreach ($repo->findBy(['product' => $product, 'attribute' => $this]) as $data) {
                if (!$attributeValue || $attributeValue->getId() != $data->getId()) {
                    $repo->delete($data, false);
                }
            }
        }

        return $attributeValue;
    }

    // }}}

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
     * Set decimals
     *
     * @param integer $decimals
     * @return Attribute
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * Get decimals
     *
     * @return integer
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get display mode
     *
     * @param \XLite\Model\Product $product Product OPTIONAL
     * @return string
     */
    public function getDisplayMode($product = null)
    {
        $productId = $product
            ? $product->getId()
            : \XLite\Core\Request::getInstance()->product_id;

        $prop = $this->getProductAttributeProperty($productId);

        if ($prop && $prop->getDisplayMode()) {
            return $prop->getDisplayMode();
        }

        return $this->displayMode;
    }

    /**
     * @param $productId
     *
     * @return null|\XLite\Model\AttributeProperty
     */
    protected function getProductAttributeProperty($productId)
    {
        return $this->executeCachedRuntime(function () use ($productId) {
            $property = null;

            if (
                $productId
                && ($product = Database::getRepo(\XLite\Model\Product::class)->find($productId))
            ) {
                $property = Database::getRepo(\XLite\Model\AttributeProperty::class)->findOneBy([
                    'product' => $product,
                    'attribute'  => $this,
                ]);
            }

            return $property;
        }, ['getProductAttributeProperty', $this->getId(), $productId]);
    }

    /**
     * Set display mode
     *
     * @param string $value
     * @param boolean $isNew New attribute flag OPTIONAL
     *
     * @return Attribute
     */
    public function setDisplayMode($value, $isNew = false)
    {
        if (
            $this->displayMode !== $value
            && $this->getAttributeProperties()
            && (!\XLite\Core\Request::getInstance()->product_id
                || $isNew)
        ) {
            foreach ($this->getAttributeProperties() as $prop) {
                $prop->setDisplayMode($value);
            }
        }

        $this->displayMode = $value;

        return $this;
    }

    /**
     * Return display modes
     *
     * @return array
     */
    public static function getDisplayModes()
    {
        return [
            static::SELECT_BOX_MODE    => static::t('Selectbox'),
            static::BLOCKS_MODE        => static::t('Blocks'),
            static::SPECIFICATION_MODE => static::t('Specification'),
        ];
    }

    /**
     * Return display mode name
     *
     * @return string
     */
    public function getDisplayModeName()
    {
        $displayModes = self::getDisplayModes();

        return $displayModes[$this->displayMode] ?? '';
    }

    /**
     * Set productClass
     *
     * @param \XLite\Model\ProductClass $productClass
     * @return Attribute
     */
    public function setProductClass(\XLite\Model\ProductClass $productClass = null)
    {
        $this->productClass = $productClass;
        return $this;
    }

    /**
     * Get productClass
     *
     * @return \XLite\Model\ProductClass
     */
    public function getProductClass()
    {
        return $this->productClass;
    }

    /**
     * Set attributeGroup
     *
     * @param \XLite\Model\AttributeGroup $attributeGroup
     * @return Attribute
     */
    public function setAttributeGroup(\XLite\Model\AttributeGroup $attributeGroup = null)
    {
        $this->attributeGroup = $attributeGroup;
        return $this;
    }

    /**
     * Get attributeGroup
     *
     * @return \XLite\Model\AttributeGroup
     */
    public function getAttributeGroup()
    {
        return $this->attributeGroup;
    }

    /**
     * Add attribute_options
     *
     * @param \XLite\Model\AttributeOption $attributeOptions
     * @return Attribute
     */
    public function addAttributeOptions(\XLite\Model\AttributeOption $attributeOptions)
    {
        $this->attribute_options[] = $attributeOptions;
        return $this;
    }

    /**
     * Get attribute_options
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributeOptions()
    {
        return $this->attribute_options;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Attribute
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Add attribute property
     *
     * @param \XLite\Model\AttributeProperty $attributeProperty
     * @return Attribute
     */
    public function addAttributeProperty(\XLite\Model\AttributeProperty $attributeProperty)
    {
        $this->attribute_properties[] = $attributeProperty;
        return $this;
    }

    /**
     * Get attribute_properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributeProperties()
    {
        return $this->attribute_properties;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $unit
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setUnit($unit)
    {
        return $this->setTranslationField(__FUNCTION__, $unit);
    }

    // }}}

    /**
     * @return void
     *
     * @ORM\PostUpdate
     */
    public function prepareAfterSave()
    {
        Database::getRepo(\XLite\Model\AttributeProperty::class)->updateFromAttribute($this);
    }
}
