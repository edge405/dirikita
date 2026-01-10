# User Module - API Documentation

## Base Path

All User endpoints are web routes (not under `/api/v1/`) that return Inertia.js views for SPA integration. They are prefixed with `/settings/`.

## Endpoints

### Profile Settings

**Method**: `GET` / `PATCH` / `DELETE`  
**Path**: `/settings/profile`  
**Authentication**: Required  
**Rate Limit**: None

#### GET Request

Returns the profile settings view.

**Response**: Inertia.js view (`settings/profile`) with:
- `mustVerifyEmail`: boolean - Whether user must verify email
- `status`: string|null - Session status message

#### PATCH Request

Updates the user's profile information.

**Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com"
}
```

**Validation Rules**:
- `name`: required, string, max:255
- `email`: required, string, lowercase, email, max:255, unique:users (ignoring current user)

**Success Response**: Redirect to profile settings page

**Error Response (422 Validation Error)**: Returns to profile settings view with validation errors

**Business Logic**:
- If email is changed, `email_verified_at` is set to `null` (requires re-verification)

#### DELETE Request

Deletes the user's account.

**Body**:
```json
{
  "password": "CurrentPassword123!"
}
```

**Validation Rules**:
- `password`: required, string, must match current password

**Success Response**: 
- User is logged out
- Session is invalidated
- User account is deleted
- Redirect to home page

**Error Response (422 Validation Error)**: Returns to profile settings view with validation errors

---

### Password Settings

**Method**: `GET` / `PUT`  
**Path**: `/settings/password`  
**Authentication**: Required  
**Rate Limit**: 6 requests per minute

#### GET Request

Returns the password settings view.

**Response**: Inertia.js view (`settings/password`)

#### PUT Request

Updates the user's password.

**Body**:
```json
{
  "current_password": "OldPassword123!",
  "password": "NewPassword123!",
  "password_confirmation": "NewPassword123!"
}
```

**Validation Rules**:
- `current_password`: required, string, must match current password
- `password`: required, string, min:8 (default Laravel password rules)
- `password_confirmation`: required, must match password

**Success Response**: Redirect back to password settings page

**Error Response (422 Validation Error)**: Returns to password settings view with validation errors

**Rate Limiting**: After 6 attempts per minute, user must wait before retrying

---

### Two-Factor Authentication Settings

**Method**: `GET`  
**Path**: `/settings/two-factor`  
**Authentication**: Required  
**Rate Limit**: None

#### GET Request

Returns the two-factor authentication settings view.

**Query Parameters**: None

**Response**: Inertia.js view (`settings/two-factor`) with:
- `twoFactorEnabled`: boolean - Whether 2FA is currently enabled
- `requiresConfirmation`: boolean - Whether 2FA requires confirmation

**Middleware**: 
- If `confirmPassword` option is enabled in Fortify config, requires password confirmation

---

### Settings Redirect

**Method**: `GET`  
**Path**: `/settings`  
**Authentication**: Required  
**Rate Limit**: None

#### Request

Redirects to the profile settings page.

**Response**: Redirect to `/settings/profile`

---

### Appearance Settings

**Method**: `GET`  
**Path**: `/settings/appearance`  
**Authentication**: Required  
**Rate Limit**: None

#### Request

Returns the appearance settings view.

**Response**: Inertia.js view (`settings/appearance`)

**Note**: Currently only displays the view. Implementation details are in the frontend.

---

## Authorization

- **All Endpoints**: Require authentication (`auth` middleware)
- **Password Confirmation**: Two-factor settings may require password confirmation if configured
- **Self-Service Only**: Users can only manage their own profile and settings

## Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `VALIDATION_ERROR` | 422 | Validation failed |
| `UNAUTHORIZED` | 401 | User not authenticated |
| `TOO_MANY_ATTEMPTS` | 429 | Rate limit exceeded (password update) |

## Rate Limiting

- **Password Update**: 6 requests per minute
- Rate limiting is handled by Laravel's throttle middleware

## Security Considerations

- All endpoints require authentication
- Password updates require current password verification
- Account deletion requires password confirmation
- Email changes require re-verification
- Rate limiting prevents brute force password attempts
- Two-factor settings may require password confirmation
