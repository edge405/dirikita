# API Rules

## Endpoint Conventions

### Naming
- **Resource-Based**: Use nouns for resources, not verbs
- **Plural Nouns**: Use plural for collections (`/users`, `/products`)
- **Nested Resources**: Max 2 levels deep (`/users/{id}/orders`)
- **Actions**: Use HTTP methods, not verbs in URL

### Examples
```
✅ GET    /api/v1/users
✅ POST   /api/v1/users
✅ GET    /api/v1/users/{id}
✅ PUT    /api/v1/users/{id}
✅ DELETE /api/v1/users/{id}

❌ GET    /api/v1/getUsers
❌ POST   /api/v1/createUser
```

### HTTP Methods
- **GET**: Retrieve resources (idempotent)
- **POST**: Create resources or perform actions
- **PUT**: Replace entire resource
- **PATCH**: Partially update resource
- **DELETE**: Delete resource (idempotent)

## Request/Response Format

### Request Headers
```
Content-Type: application/json
Authorization: Bearer {token}  # When required
Accept: application/json
```

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation completed successfully"  // Optional
}
```

### Error Response
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "details": null  // Optional: additional error context
  }
}
```

### Validation Error Response
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "email": ["Email is required", "Email must be valid"],
      "password": ["Password must be at least 8 characters"]
    }
  }
}
```

## Errors

### Standard Error Codes
- `VALIDATION_ERROR` - Input validation failed (422)
- `UNAUTHORIZED` - Authentication required (401)
- `INVALID_CREDENTIALS` - Invalid login credentials (401)
- `FORBIDDEN` - Insufficient permissions (403)
- `NOT_FOUND` - Resource not found (404)
- `INTERNAL_ERROR` - Server error (500)

### HTTP Status Mapping
- `200 OK` - Successful GET, PUT, PATCH
- `201 Created` - Successful POST (creation)
- `204 No Content` - Successful DELETE
- `400 Bad Request` - Invalid request
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation errors
- `500 Internal Server Error` - Server error

## Pagination

### Standard Pagination
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "total": 100,
    "per_page": 10,
    "current_page": 1,
    "last_page": 10,
    "from": 1,
    "to": 10
  },
  "links": {
    "first": "https://api.example.com/api/v1/resource?page=1",
    "last": "https://api.example.com/api/v1/resource?page=10",
    "prev": null,
    "next": "https://api.example.com/api/v1/resource?page=2"
  }
}
```

### Query Parameters
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 10, max: 100)

## Filtering & Sorting

### Filtering
```
GET /api/v1/users?filter[status]=active&filter[role]=admin
```

### Sorting
```
GET /api/v1/users?sort=name&order=asc
GET /api/v1/users?sort=-created_at  # Descending
```

### Allowed Sort Fields
- Must be whitelisted in controller/service
- Default sort should be specified
- Sort fields must be indexed in database

## Authentication

### Token-Based Auth
- **Laravel Sanctum**: Use Sanctum for API authentication
- **Token in Header**: `Authorization: Bearer {token}`
- **Token Expiration**: Access tokens expire in 24 hours
- **Refresh Tokens**: Use refresh tokens for token renewal

### Public Endpoints
- Registration: `POST /api/v1/auth/register`
- Login: `POST /api/v1/auth/login`
- Token Refresh: `POST /api/v1/auth/refresh`

### Protected Endpoints
- All other endpoints require authentication
- Return `401 Unauthorized` if token missing/invalid

## Rate Limiting

- **Default**: 60 requests per minute per IP
- **Authenticated**: 100 requests per minute per user
- **Headers**: Rate limit info in response headers
  ```
  X-RateLimit-Limit: 60
  X-RateLimit-Remaining: 59
  X-RateLimit-Reset: 1234567890
  ```

## Versioning

- **Current Version**: `v1`
- **URL Prefix**: `/api/v1/`
- **Backward Compatibility**: Maintain for at least 6 months
- **Deprecation**: Use `Deprecation` header for deprecated endpoints

## Examples

Every endpoint documentation must include:

1. **Request Example**: Complete request with headers
2. **Success Response**: Example success response
3. **Error Response**: At least one error case
4. **Authentication**: Whether auth is required
5. **Rate Limits**: Any special rate limiting

## Field Naming

- **Backend**: `snake_case` for database and API responses
- **Frontend**: Converts to `camelCase` in DTOs
- **Consistency**: Use consistent naming across all endpoints

## Date/Time Format

- **Format**: ISO 8601 (`YYYY-MM-DDTHH:mm:ssZ`)
- **Timezone**: All timestamps in UTC
- **Example**: `2025-12-13T10:30:45Z`

