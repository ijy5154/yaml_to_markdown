<?php

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

function parseParameters($parameters)
{
    if (empty($parameters)) {
        return "";
    }
    $result = "### Parameters\n\n";
    $result .= "| Name | In | Description | Required | Type |\n";
    $result .= "|------|----|-------------|----------|------|\n";
    foreach ($parameters as $param) {
        $name = $param['name'] ?? '';
        $in = $param['in'] ?? '';
        $description = $param['description'] ?? '';
        $required = $param['required'] ?? false;
        $type = $param['schema']['type'] ?? '';
        $result .= "| $name | $in | $description | $required | $type |\n";
    }
    $result .= "\n";
    return $result;
}

function parseProperties($properties)
{
    $result = "";
    $example = [];
    foreach ($properties as $prop => $details) {
        $propType = $details['type'] ?? '';
        $propDesc = $details['description'] ?? '';
        $propExample = $details['example'] ?? '';

        if (!in_array($propType, ['array', 'object'])) {
            $result .= "| $prop | $propType | $propDesc | $propExample |\n";
            $example[$prop] = $propExample;
        }

        if ($propType === 'array' && isset($details['items'])) {
            $itemsType = $details['items']['type'] ?? '';

            if ($itemsType === 'object' && isset($details['items']['properties'])) {
                list($nestedResult, $nestedExample) = parseProperties($details['items']['properties']);
                $result .= $nestedResult;
                $example[$prop] = [$nestedExample];
            } else {
                if ($itemsType === 'string') {
                    if ($details['items']['enum'] ?? false) {
                        $result .= "| $prop | $propType | $propDesc | " . implode(', ', $details['items']['enum']) . " |\n";
                        $example[$prop] = $details['items']['enum'];
                    } else {
                        $result .= "| $prop | $propType | $propDesc | " . $details['items']['example'] . " |\n";
                        $example[$prop] = [$details['items']['example']];
                    }
                }
            }
        } elseif ($propType === 'object' && isset($details['properties'])) {
            list($nestedResult, $nestedExample) = parseProperties($details['properties']);
            $result .= $nestedResult;
            $example[$prop] = $nestedExample;
        }
    }
    return [$result, $example];
}

function parseRequestBody($requestBody)
{
    if (empty($requestBody)) {
        return "";
    }
    $result = "### Request Body\n\n";
    $result .= "| Name | Type | Description | Example |\n";
    $result .= "|------|------|-------------|---------|\n";
    $example = [];
    foreach ($requestBody['content'] as $contentType => $content) {
        $schema = $content['schema'] ?? [];
        if (isset($schema['properties'])) {
            list($propertiesResult, $example) = parseProperties($schema['properties']);
            $result .= $propertiesResult;
        }
    }
    $result .= "\n";
    if (!empty($example)) {
        $result .= "```json\n" . json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```\n\n";
    }
    return $result;
}

function parseResponses($responses)
{
    if (empty($responses)) {
        return "";
    }
    $result = "### Responses\n\n";
    foreach ($responses as $status => $response) {
        $description = $response['description'] ?? '';
        $result .= "#### $status\n\n";
        $result .= "$description\n\n";
        if (isset($response['content'])) {
            foreach ($response['content'] as $contentType => $content) {
                $result .= "**Content Type:** $contentType\n\n";
                $example = [];
                if (isset($content['schema'])) {
                    $schema = $content['schema'];
                    if (isset($schema['$ref'])) {
                        $result .= "Schema: {$schema['$ref']}\n\n";
                    } elseif (isset($schema['properties'])) {
                        $result .= "| Name | Type | Description | Example |\n";
                        $result .= "|------|------|-------------|---------|\n";
                        list($propertiesResult, $example) = parseProperties($schema['properties']);
                        $result .= $propertiesResult;
                    }
                }
                if (!empty($example)) {
                    $result .= "```json\n" . json_encode($example, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```\n\n";
                }
            }
        }
    }
    return $result;
}

function parsePaths($paths)
{
    $result = "";
    foreach ($paths as $path => $methods) {
        $result .= "## $path\n\n";
        foreach ($methods as $method => $details) {
            $summary = $details['summary'] ?? '';
            $description = $details['description'] ?? '';
            $parameters = $details['parameters'] ?? [];
            $requestBody = $details['requestBody'] ?? [];
            $responses = $details['responses'] ?? [];
            $result .= "### " . strtoupper($method) . "\n\n";
            $result .= "**Summary:** $summary\n\n";
            $result .= "**Description:** $description\n\n";
            $result .= parseParameters($parameters);
            $result .= parseRequestBody($requestBody);
            $result .= parseResponses($responses);
        }
    }
    return $result;
}

function parseTags($tags)
{
    if (empty($tags)) {
        return "";
    }
    $result = "## Tags\n\n";
    foreach ($tags as $tag) {
        $name = $tag['name'] ?? '';
        $description = $tag['description'] ?? '';
        $result .= "- **$name**: $description\n";
    }
    $result .= "\n";
    return $result;
}

function parseSecuritySchemes($securitySchemes)
{
    if (empty($securitySchemes)) {
        return "";
    }
    $result = "## Security Schemes\n\n";
    foreach ($securitySchemes as $name => $scheme) {
        $type = $scheme['type'] ?? '';
        $description = $scheme['description'] ?? '';
        $result .= "- **$name** ($type): $description\n";
    }
    $result .= "\n";
    return $result;
}

function main()
{
    $openapiData = Yaml::parseFile('openapi.yml');

    $info = $openapiData['info'] ?? [];
    $title = $info['title'] ?? 'API Documentation';
    $version = $info['version'] ?? '1.0.0';
    $description = $info['description'] ?? '';

    $markdown = "# $title\n\n";
    $markdown .= "**Version:** $version\n\n";
    $markdown .= "**Description:** $description\n\n";

    $servers = $openapiData['servers'] ?? [];
    if (!empty($servers)) {
        $markdown .= "## Servers\n\n";
        foreach ($servers as $server) {
            $url = $server['url'] ?? '';
            $serverDescription = $server['description'] ?? '';
            $markdown .= "- **URL:** $url\n";
            $markdown .= "  **Description:** $serverDescription\n";
        }
        $markdown .= "\n";
    }

    $paths = $openapiData['paths'] ?? [];
    $markdown .= parsePaths($paths);

    $tags = $openapiData['tags'] ?? [];
    $markdown .= parseTags($tags);

    $components = $openapiData['components'] ?? [];
    $securitySchemes = $components['securitySchemes'] ?? [];
    $markdown .= parseSecuritySchemes($securitySchemes);

    file_put_contents('api_documentation.md', $markdown);

    echo "Markdown documentation has been generated and saved to 'api_documentation.md'.\n";
}

main();
