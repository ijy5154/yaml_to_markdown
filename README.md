![header](https://capsule-render.vercel.app/api?text=YAML_TO_MARKDOWN&type=venom)

## Installation

To install the necessary dependencies, run the following command:

```bash
composer require symfony/yaml
```

## Usage

To generate the markdown, run the following command:

```bash
php generate_markdown.php
```

## Example

Here is an example of how a YAML file is converted to Markdown:

### YAML Input

```yml
openapi: 3.0.0
info:
    title: Sample API
    description: A sample API to illustrate OpenAPI concepts
    version: 1.0.0
servers:
    - url: https://api.example.com/v1
paths:
    /users:
        get:
            summary: List all users
            responses:
                '200':
                    description: A list of users
                    content:
                        application/json:
                            schema:
                                type: array
                                items:
                                    type: object
                                    properties:
                                        id:
                                            type: integer
                                        name:
                                            type: string
    /users/{id}:
        get:
            summary: Get a user by ID
            parameters:
                - name: id
                    in: path
                    required: true
                    schema:
                        type: integer
            responses:
                '200':
                    description: A user object
                    content:
                        application/json:
                            schema:
                                type: object
                                properties:
                                    id:
                                        type: integer
                                    name:
                                        type: string
                '404':
                    description: User not found
```
------------
### Markdown Output

```
# Sample API

**Version:** 1.0.0

**Description:** A sample API to illustrate OpenAPI concepts

## Servers

- **URL:** https://api.example.com/v1
  **Description:** 

## /users

### GET

**Summary:** List all users

**Description:** 

### Responses

#### 200

A list of users

**Content Type:** application/json

## /users/{id}

### GET

**Summary:** Get a user by ID

**Description:** 

### Parameters

| Name | In | Description | Required | Type |
|------|----|-------------|----------|------|
| id | path |  | 1 | integer |

### Responses

#### 200

A user object

**Content Type:** application/json

| Name | Type | Description | Example |
|------|------|-------------|---------|
| id | integer |  |  |
| name | string |  |  |
json
{
    "id": "",
    "name": ""
}

#### 404

User not found

```
------------
### Markdown Render

# Sample API

**Version:** 1.0.0

**Description:** A sample API to illustrate OpenAPI concepts

## Servers

- **URL:** https://api.example.com/v1
  **Description:** 

## /users

### GET

**Summary:** List all users

**Description:** 

### Responses

#### 200

A list of users

**Content Type:** application/json

## /users/{id}

### GET

**Summary:** Get a user by ID

**Description:** 

### Parameters

| Name | In | Description | Required | Type |
|------|----|-------------|----------|------|
| id | path |  | 1 | integer |

### Responses

#### 200

A user object

**Content Type:** application/json

| Name | Type | Description | Example |
|------|------|-------------|---------|
| id | integer |  |  |
| name | string |  |  |
```json
{
    "id": "",
    "name": ""
}
```
#### 404

User not found