# Auth Module - API Documentation

## Base Path

All Auth endpoints are handled by Laravel Fortify and are available at the root level (not under `/api/v1/`). These are web routes that return Inertia.js views for SPA integration.

## Endpoints

### Registration

**Method**: `GET` / `POST`  
**Path**: `/register`  
**Authentication**: Not required  
**Rate Limit**: None (handled by Fortify)

#### GET Request

Returns the registration view.

**Response**: Inertia.js view (`auth/register`)

#### POST Request

**Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!"
}
```

**Validation Rules**:
- `name`: required, string, max:255
- `email`: required, string, email, max:255, unique:users
- `password`: required, string, min:8 (default Laravel password rules)
- `password_confirmation`: required, must match password

**Success Response**: Redirect to dashboard or email verification page

**Error Response (422 Validation Error)**: Returns to registration view with validation errors

---

### Login

**Method**: `GET` / `POST`  
**Path**: `/login`  
**Authentication**: Not required  
**Rate Limit**: 5 requests per minute per email/IP

#### GET Request

Returns the login view.

**Response**: Inertia.js view (`auth/login`) with:
- `canResetPassword`: boolean
- `canRegister`: boolean
- `status`: session status message

#### POST Request

**Body**:
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "remember": false
}
```

**Validation Rules**:
- `email`: required, string, email
- `password`: required, string
- `remember`: optional, boolean

**Success Response**: Redirect to dashboard (or intended URL)

**Error Response (422 Validation Error)**: Returns to login view with validation errors

**Rate Limiting**: After 5 failed attempts per minute, user must wait before retrying

---

### Logout

**Method**: `POST`  
**Path**: `/logout`  
**Authentication**: Required  
**Rate Limit**: None

#### Request

**Headers**:
```
X-CSRF-TOKEN: {csrf_token}
```

**Success Response**: Redirect to home page

---

### Forgot Password

**Method**: `GET` / `POST`  
**Path**: `/forgot-password`  
**Authentication**: Not required  
**Rate Limit**: None

#### GET Request

Returns the forgot password view.

**Response**: Inertia.js view (`auth/forgot-password`)

#### POST Request

**Body**:
```json
{
  "email": "john@example.com"
}
```

**Validation Rules**:
- `email`: required, string, email, exists:users

**Success Response**: Returns to forgot password view with success message

**Error Response (422 Validation Error)**: Returns to forgot password view with validation errors

---

### Reset Password

**Method**: `GET` / `POST`  
**Path**: `/reset-password/{token}`  
**Authentication**: Not required  
**Rate Limit**: None

#### GET Request

Returns the reset password view.

**Query Parameters**:
- `email`: User's email address
- `token`: Password reset token

**Response**: Inertia.js view (`auth/reset-password`) with:
- `email`: string
- `token`: string

#### POST Request

**Body**:
```json
{
  "email": "john@example.com",
  "password": "NewSecurePassword123!",
  "password_confirmation": "NewSecurePassword123!",
  "token": "reset_token_here"
}
```

**Validation Rules**:
- `email`: required, string, email, exists:users
- `password`: required, string, min:8 (default Laravel password rules)
- `password_confirmation`: required, must match password
- `token`: required, string, valid reset token

**Success Response**: Redirect to login page

**Error Response (422 Validation Error)**: Returns to reset password view with validation errors

---

### Email Verification

**Method**: `GET`  
**Path**: `/email/verify`  
**Authentication**: Required  
**Rate Limit**: None

#### Request

Returns the email verification view.

**Response**: Inertia.js view (`auth/verify-email`)

---

### Email Verification Notification

**Method**: `POST`  
**Path**: `/email/verification-notification`  
**Authentication**: Required  
**Rate Limit**: None

#### Request

Sends a new email verification link.

**Success Response**: Returns with success message

---

### Verify Email

**Method**: `GET`  
**Path**: `/email/verify/{id}/{hash}`  
**Authentication**: Required  
**Rate Limit**: None

#### Request

Verifies the user's email address using the verification link.

**URL Parameters**:
- `id`: User ID
- `hash`: Verification hash

**Success Response**: Redirect to dashboard

---

### Two-Factor Challenge

**Method**: `GET` / `POST`  
**Path**: `/two-factor-challenge`  
**Authentication**: Not required (but requires 2FA challenge)  
**Rate Limit**: 5 requests per minute

#### GET Request

Returns the two-factor challenge view.

**Response**: Inertia.js view (`auth/two-factor-challenge`)

#### POST Request

**Body**:
```json
{
  "code": "123456"
}
```

**Validation Rules**:
- `code`: required, string, valid 2FA code

**Success Response**: Redirect to intended URL

**Error Response (422 Validation Error)**: Returns to challenge view with validation errors

---

### Password Confirmation

**Method**: `GET` / `POST`  
**Path**: `/user/confirm-password`  
**Authentication**: Required  
**Rate Limit**: None

#### GET Request

Returns the password confirmation view.

**Response**: Inertia.js view (`auth/confirm-password`)

#### POST Request

**Body**:
```json
{
  "password": "CurrentPassword123!"
}
```

**Validation Rules**:
- `password`: required, string, current_password

**Success Response**: Redirect to intended URL

**Error Response (422 Validation Error)**: Returns to confirmation view with validation errors

---

## Authorization

- **Public Endpoints**: Registration, Login, Forgot Password, Reset Password, Two-Factor Challenge
- **Authenticated Endpoints**: Logout, Email Verification, Password Confirmation
- **Session-Based**: All authentication uses Laravel's session-based authentication (web guard)

## Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `VALIDATION_ERROR` | 422 | Validation failed |
| `UNAUTHORIZED` | 401 | User not authenticated |
| `TOO_MANY_ATTEMPTS` | 429 | Rate limit exceeded |

## Rate Limiting

- **Login**: 5 requests per minute per email/IP combination
- **Two-Factor Challenge**: 5 requests per minute per session
- Rate limits are handled by Laravel's rate limiter configured in `FortifyServiceProvider`

## Security Considerations

- All passwords are hashed using bcrypt
- CSRF protection is enabled for all POST requests
- Rate limiting prevents brute force attacks
- Password reset tokens expire after a set time
- Email verification ensures valid email addresses
- Two-factor authentication adds an extra security layer
- Password confirmation required for sensitive operations
