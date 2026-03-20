# Yoummday Refactoring Task

## Background

Original task: Refactoring the `GET /has_permission/{token}` endpoint and create tests.

See [CHANGES.md](CHANGES.md) for a detailed explanation of every change and the reasonings behind it.

## Requirements

- Docker & Docker Compose

Or without Docker:
- PHP 8.1
- Composer
- ext-yaml

## Installation

```shell
# With Docker
make build
make install

# Without Docker
composer install
```

## Run

```shell
# With Docker
make run

# Without Docker
php src/main.php
```

Expected output:
```
[INFO] Registering GET /tokens/{token}/permissions/{permission}
[INFO] Server running on 127.0.0.1:1337
```

## Testing

```shell
# With Docker
make test

# Without Docker
php vendor/bin/phpunit Test
```

## API Endpoint

```
GET /tokens/{token}/permissions/{permission}
```

Checks wether a given token has the specified permission.

### Parameters

| Parameter    | Type   | Location | Description                          |
|--------------|--------|----------|--------------------------------------|
| `token`      | string | path     | The token to check                   |
| `permission` | string | path     | The permission to verify (`read`, `write`)  |

### Responses

#### 200 - Permission check successful

```shell
curl http://localhost:1337/tokens/token1234/permissions/read
```
```json
{
  "success": true,
  "data": {
    "has_permission": true
  }
}
```

```shell
curl http://localhost:1337/tokens/tokenReadonly/permissions/write
```
```json
{
  "success": true,
  "data": {
    "has_permission": false
  }
}
```

#### 404 - Token Not Found

```shell
curl http://localhost:1337/tokens/unknownToken/permissions/read
```
```json
{
  "success": false,
  "error": {
    "code": "TOKEN_NOT_FOUND",
    "message": "Token \"unknownToken\" not found"
  }
}
```

#### 422 - Invalid permission value

```shell
curl http://localhost:1337/tokens/token1234/permissions/delete
```
```json
{
  "success": false,
  "error": {
    "code": "INVALID_PERMISSION",
    "message": "Invalid permission \"delete\". Valid values: read, write"
  }
}
```
