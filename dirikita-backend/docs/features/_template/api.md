# [Feature Name] Module - API Documentation

## Base Path

All [Feature] endpoints are prefixed with `/api/v1/[path]`

## Endpoints

### [Endpoint Name]

**Method**: `GET|POST|PUT|PATCH|DELETE`  
**Path**: `/api/v1/[path]`  
**Authentication**: Required/Optional  
**Rate Limit**: X requests per minute

#### Request

**Headers**:
```
Authorization: Bearer {access_token}  # If required
Content-Type: application/json
Accept: application/json
```

**Body**:
```json
{
  "field": "value"
}
```

**Validation Rules**:
- `field`: required, type, constraints

#### Success Response (200 OK)

```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation completed successfully"
}
```

#### Error Response (422 Validation Error)

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "field": ["Error message"]
    }
  }
}
```

#### Example cURL

```bash
curl -X [METHOD] https://api.example.com/api/v1/[path] \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"field": "value"}'
```

## Authorization

- Which endpoints require authentication
- Token requirements
- Special permissions needed

## Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `ERROR_CODE` | 400 | Description |

## Rate Limiting

- Endpoint 1: X requests/minute
- Endpoint 2: Y requests/minute

## Security Considerations

- Security consideration 1
- Security consideration 2

