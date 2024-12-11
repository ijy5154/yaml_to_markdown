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

