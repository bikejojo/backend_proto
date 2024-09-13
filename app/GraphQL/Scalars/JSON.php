<?php


namespace App\GraphQL\Scalars;


use Exception;
use JsonException;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Utils\Utils as GraphQLUtils;


class JSON extends ScalarType{

    public string  $name = 'JSON';
    public ?string $description = 'A custom scalar type for JSON data';

    public function serialize($value): string
    {
        return json_encode($value);
    }
    public function parseValue($value)
    {
        if (is_string($value)) {
            return $this->decodeJSON($value);
        }
        return $value;
    }

   public function parseLiteral($valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            $decoded = json_decode($valueNode->value, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Error('Invalid JSON: ' . json_last_error_msg());
            }

            return $decoded;
        }

        throw new Error(
            'Cannot represent following value as JSON: ' . json_encode($valueNode)
        );
    }


    protected function decodeJSON($value)
    {
        try {
            $parsed = json_decode($value);
        } catch (JsonException $jsonException) {
            throw new Error(
                $jsonException->getMessage()
            );
        }

        return $parsed;
    }
}
