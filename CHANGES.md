# Refactoring Changes

What was changed in the `GET /has_permission/{token}` endpoint and why.

---

## Project Infrastructure

- Docker setup (`Dockerfile`, `docker-compose.yml`) for a reproducable dev environment
- Makefile shortcuts: `make build`, `make run`, `make test`, `make analyse`, `make shell`
- PHPUnit config with separate `Unit` and `Feature` test suites
- PSR-4 autoloading for tests (`autoload-dev` in `composer.json`)
- PHPStan at `level: max` for static analysis

---

## RESTful URL

`GET /has_permission/{token}` -> `GET /tokens/{token}/permissions/{permission}`

- Original used a verb (`has_permission`). REST URLs should be resource oriented nouns.
- Permission was hardcoded to `read`, now it's a URL parameter.
- New format reads as "the permission `{permission}` of token `{token}`".

---

## Permission Enum

- `App\Enum\Permission` — backed string enum (`read`, `write`) replacing magic strings
- `fromString()` factory method validates input and throws `InvalidPermissionException` on failure.
- Handler calls `Permission::fromString()` directly, no extra abstractions needed.

---

## Custom Exceptions and Standard Response Format

- `HttpException` base class with `statusCode` and `errorCode`
- `TokenNotFoundException` (404) and `InvalidPermissionException` (422)
- `ApiResponse` helper enforcing consistent JSON structure:
  - Success: `{"success": true, "data": {...}}`
  - Error: `{"success": false, "error": {"code": "...", "message": "..."}}`
- Original returned HTTP 400 for everything with inconsistent body.

---

## Service Layer

- `PermissionServiceInterface` + `PermissionService`: business logic extracted from handler.
- Handler only orchestrates (validate input -> call service -> return response).
- Original handler did everything in one method and was untestable.

---

## Token Entity and Repository Pattern

- `Token` entity with `hasPermission()`, replaces raw arrays that was flowing through every layer.
- `TokenCollection`: iterable, countable collection with `findById()`.
- `TokenRepositoryInterface` + `InMemoryTokenRepository`: wraps `TokenDataProvider`, maps raw data to domain objects at the boundary.
- `TokenDataProvider` left unchanged (treated as external data source).

---

## Abstract ApiHandler

- `ApiHandler` base class with Template Method pattern.
- `HttpException` -> proper error response, `\Throwable` -> safe 500 (no stack trace leak).
- Handlers implement `handle()` (happy path only), exception handling is defined once.
- Middleware/decorator is not feasible because framework instantiates handlers directly via route attributes.

---

## Dependency Inversion

- Handler depends on `PermissionServiceInterface`, not concrete `PermissionService`.
- Service depends on `TokenRepositoryInterface`, not concrete `TokenDataProvider`.
- Each layer owns it's abstractions. Data source can change without touching business logic.

---

## Testing

- **Unit tests**: enum, entity, collection, repository, service, exceptions, response helpers (all with mocked dependencies).
- **Feature tests**: handler with mocked service, verifies orchestration logic.
- **Integration tests**: full stack with real `TokenDataProvider` data, verifies everything works together.
- All changes followed TDD: test first (RED), then implement (GREEN).
