<?php

$_['api_schema_order_form_history'] = json_decode(<<<'JSON'
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "$id": "https://example.com/product.schema.json",
  "title": "Product",
  "description": "Atualização da situação do pedido",
  "type": "array",
  "items": {
    "type": "object",
    "properties": {
      "order_id": {
        "type": "integer",
        "minimum": 0,
        "default": 0
      },
      "order_status_id": {
        "type": "integer",
        "minimum": 0,
        "default": 0
      },
      "comment": {
        "type": "string",
        "default": ""
      }
    },
    "required": [
      "order_id",
      "order_status_id",
      "comment"
    ]
  }
}
JSON);
