<?php

$_['api_schema_product_form'] = json_decode(<<<'JSON'
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "$id": "https://example.com/product.schema.json",
  "title": "Product",
  "description": "Cadastro de Produto",
  "type": "object",
  "properties": {
    "name": {
      "type": "object",
      "properties": {
        "default": {
          "type": "string",
          "minLength": 1,
          "maxLength": 255
        }
      },
      "additionalProperties": {
        "type": "string",
        "minLength": 1,
        "maxLength": 255
      },
      "minProperties": 1,
      "required": [
        "default"
      ]
    },
    "description": {
      "type": "object",
      "properties": {
        "default": {
          "type": "string",
          "minLength": 1,
          "maxLength": 255
        }
      },
      "additionalProperties": {
        "type": "string"
      },
      "minProperties": 1,
      "required": [
        "default"
      ]
    },
    "meta_title": {
      "type": "object",
      "properties": {
        "default": {
          "type": "string",
          "minLength": 1,
          "maxLength": 255
        }
      },
      "additionalProperties": {
        "type": "string",
        "minLength": 1,
        "maxLength": 255
      },
      "minProperties": 1,
      "required": [
        "default"
      ]
    },
    "meta_keyword": {
      "type": "object",
      "properties": {
        "default": {
          "type": "string",
          "maxLength": 255
        }
      },
      "additionalProperties": {
        "type": "string",
        "maxLength": 255
      },
      "minProperties": 1,
      "required": [
        "default"
      ]
    },
    "meta_description": {
      "type": "object",
      "properties": {
        "default": {
          "type": "string",
          "maxLength": 255
        }
      },
      "additionalProperties": {
        "type": "string",
        "maxLength": 255
      },
      "minProperties": 1,
      "required": [
        "default"
      ]
    },
    "tags": {
      "type": "object",
      "properties": {
        "default": {
          "type": "array",
          "items": {
            "type": "string"
          },
          "default": []
        }
      },
      "additionalProperties": {
        "type": "array",
        "items": {
          "type": "string"
        }
      },
      "minProperties": 1,
      "required": [
        "default"
      ]
    },
    "model": {
      "type": "string",
      "minLength": 1,
      "maxLength": 64,
      "default": ""
    },
    "sku": {
      "type": "string",
      "maxLength": 64,
      "default": ""
    },
    "ncm": {
      "type": "string",
      "maxLength": 12,
      "default": ""
    },
    "cest": {
      "type": "string",
      "maxLength": 12,
      "default": ""
    },
    "upc": {
      "type": "string",
      "maxLength": 12,
      "default": ""
    },
    "ean": {
      "type": "string",
      "maxLength": 14,
      "default": ""
    },
    "jan": {
      "type": "string",
      "maxLength": 13,
      "default": ""
    },
    "isbn": {
      "type": "string",
      "maxLength": 17,
      "default": ""
    },
    "mpn": {
      "type": "string",
      "maxLength": 64,
      "default": ""
    },
    "location": {
      "type": "string",
      "maxLength": 128,
      "default": ""
    },
    "price": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "tax_class_id": {
      "type": "number",
      "default": 0
    },
    "quantity": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "minimum": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "subtract": {
      "type": "boolean",
      "default": true
    },
    "stock_status_id": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "shipping": {
      "type": "boolean",
      "minimum": 0,
      "default": true
    },
    "date_available": {
      "type": "string",
      "format": "date"
    },
    "dimensions": {
      "type": "object",
      "properties": {
        "length": {
          "$ref": "#/definitions/dimension"
        },
        "width": {
          "$ref": "#/definitions/dimension"
        },
        "height": {
          "$ref": "#/definitions/dimension"
        },
        "weight": {
          "$ref": "#/definitions/dimension"
        },
        "length_class_id": {
          "$ref": "#/definitions/dimension"
        },
        "weight_class_id": {
          "$ref": "#/definitions/dimension"
        }
      },
      "required": [
        "length_class_id",
        "weight_class_id"
      ]
    },
    "status": {
      "type": ["boolean", "integer"],
      "default": false,
      "maximum": 1
    },
    "sort_order": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "manufacturer_id": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "categories": {
      "type": "array",
      "items": {
        "type": "number"
      },
      "default": []
    },
    "filters": {
      "type": "array",
      "items": {
        "type": "number",
        "minimum": 0
      },
      "default": []
    },
    "stores": {
      "type": "array",
      "items": {
        "type": "number",
        "minimum": 0
      },
      "default": [
        0
      ]
    },
    "downloads": {
      "type": "array",
      "items": {
        "type": "number",
        "minimum": 0
      },
      "default": []
    },
    "product_related": {
      "type": "array",
      "items": {
        "type": "number",
        "minimum": 0
      },
      "default": []
    },
    "attributes": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "id": {
            "type": "number",
            "minimum": 0
          },
          "default": {
            "type": "string"
          }
        },
        "additionalProperties": {
          "type": "string"
        },
        "required": [
          "id",
          "default"
        ]
      },
      "default": []
    },
    "options": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "type": {
            "enum": [
              "radio",
              "checkbox",
              "text",
              "select",
              "date",
              "time",
              "date-time",
              "file"
            ]
          },
          "required": {
            "type": "boolean",
            "default": false
          }
        },
        "allOf": [
          {
            "if": {
              "properties": {
                "type": {
                  "pattern": "radio|checkbox|select"
                }
              }
            },
            "then": {
              "properties": {
                "values": {
                  "type": "array",
                  "items": {
                    "type": "object",
                    "properties": {
                      "option_id": {
                        "type": "number",
                        "minimum": 0
                      },
                      "option_value_id": {
                        "description": "Valor deve existir na loja",
                        "type": "number"
                      },
                      "sku": {
                        "type": "string"
                      },
                      "quantity": {
                        "type": "number",
                        "minimum": 0,
                        "default": 0
                      },
                      "subtract": {
                        "type": "boolean",
                        "default": true
                      },
                      "price": {
                        "$ref": "#/definitions/product_option_with_prefix"
                      },
                      "points": {
                        "$ref": "#/definitions/product_option_with_prefix"
                      }
                    },
                    "required": [
                      "option_value_id"
                    ]
                  }
                }
              }
            }
          },
          {
            "if": {
              "properties": {
                "type": {
                  "pattern": "text(area)?"
                }
              }
            },
            "then": {
              "properties": {
                "value": {
                  "type": "string"
                }
              }
            }
          },
          {
            "if": {
              "properties": {
                "type": {
                  "pattern": "date"
                }
              }
            },
            "then": {
              "properties": {
                "value": {
                  "type": "string",
                  "format": "date"
                }
              }
            }
          },
          {
            "if": {
              "properties": {
                "type": {
                  "pattern": "date-time"
                }
              }
            },
            "then": {
              "properties": {
                "value": {
                  "type": "string",
                  "format": "date-time"
                }
              }
            }
          },
          {
            "if": {
              "properties": {
                "type": {
                  "pattern": "time"
                }
              }
            },
            "then": {
              "properties": {
                "value": {
                  "type": "string",
                  "format": "time"
                }
              }
            }
          }
        ],
        "required": [
          "type",
          "option_id"
        ]
      },
      "default": []
    },
    "recurring": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "recurring_id": {
            "type": "number"
          },
          "customer_group_id": {
            "$ref": "#/definitions/customer_group"
          }
        },
        "required": [
          "recurring_id",
          "customer_group_id"
        ]
      },
      "default": []
    },
    "special": {
      "type": "array",
      "items": {
        "$ref": "#/definitions/product_special"
      },
      "default": []
    },
    "discounts": {
      "type": "array",
      "items": {
        "type": "object",
        "allOf": [
          {
            "$ref": "#/definitions/product_special"
          },
          {
            "properties": {
              "quantity": {
                "type": "number",
                "default": 0
              }
            }
          }
        ]
      },
      "default": []
    },
    "image": {
      "$ref": "#/definitions/image"
    },
    "additional_images": {
      "type": "array",
      "items": {
        "$ref": "#/definitions/image"
      },
      "default": []
    },
    "points_to_buy": {
      "$ref": "#/definitions/points"
    },
    "points_reward": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "customer_group_id": {
            "$ref": "#/definitions/customer_group"
          },
          "points": {
            "$ref": "#/definitions/points"
          }
        },
        "required": [
          "customer_group_id"
        ]
      },
      "default": []
    },
    "seo_url_generate": {
      "type": "object",
      "properties": {
        "auto": {
          "type": "boolean",
          "default": false
        },
        "suffix": {
          "type": "string"
        },
        "prefix": {
          "type": "string"
        }
      }
    }
  },
  "definitions": {
    "product_option_with_prefix": {
      "type": "object",
      "properties": {
        "prefix": {
          "enum": [
            "-",
            "+"
          ],
          "default": "+"
        },
        "value": {
          "type": "number",
          "minimum": 0,
          "default": 0
        }
      },
      "required": [
        "value"
      ]
    },
    "customer_group": {
      "type": "number"
    },
    "product_special": {
      "type": "object",
      "properties": {
        "customer_group_id": {
          "$ref": "#/definitions/customer_group"
        },
        "priority": {
          "type": "number",
          "default": 1
        },
        "price": {
          "type": "number",
          "default": 0
        },
        "date_start": {
          "type": "string",
          "format": "date",
          "default": "2021-07-13"
        },
        "date_end": {
          "type": "string",
          "format": "date",
          "default": "2021-09-17"
        }
      },
      "required": [
        "customer_group_id"
      ]
    },
    "points": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "dimension": {
      "type": "number",
      "minimum": 0,
      "default": 0
    },
    "image": {
      "type": "string",
      "format": "uri",
      "pattern": "\\.(jpe?g|gif|webp|png)$",
      "default": ""
    }
  },
  "required": [
    "name",
    "meta_title",
    "model"
  ]
}
JSON);

$_['api_schema_product_form_stock'] = json_decode(<<<'JSON'
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "$id": "https://example.com/product.schema.json",
  "title": "Product",
  "description": "Atualização de estoque",
  "type": "array",
  "items": {
    "type": "object",
    "properties": {
      "product_id": {
        "type": "integer",
        "minimum": 0,
        "default": 0
      },
      "minimum": {
        "type": "integer",
        "minimum": 0,
        "default": 0
      },
      "quantity": {
        "type": "integer",
        "minimum": 0,
        "default": 0
      },
      "weight": {
        "type": "number",
        "minimum": 0,
        "default": 0
      },
      "length": {
        "type": "number",
        "minimum": 0,
        "default": 0
      },
      "weight": {
        "type": "number",
        "minimum": 0,
        "default": 0
      },
      "width": {
        "type": "number",
        "minimum": 0,
        "default": 0
      },
      "height": {
        "type": "number",
        "minimum": 0,
        "default": 0
      },
      "price": {
        "type": "number",
        "minimum": 0,
        "default": 0
      },
      "weight_class_id": {
        "type": "integer",
        "minimum": 0,
        "default": 0
      },
      "length_class_id": {
        "type": "integer",
        "minimum": 0,
        "default": 0
      }
    },
    "required": [
      "product_id",
      "location",
      "minimum",
      "quantity",
      "weight",
      "length",
      "width",
      "height",
      "price"
    ]
  }
}
JSON);
