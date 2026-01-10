# Auth Module - Business Logic

## Domain Rules

### Business Invariants

1. **Email Uniqueness**: Each email address can only be associated with one user account
2. **Password Strength**: Passwords must meet minimum security requirements (default Laravel rules: min 8 characters)
3. **Email Verification**: Users must verify their email before accessing protected features (if email verification is enabled)
4. **Two-Factor Authentication**: 2FA can only be enabled after email verification
5. **Password Reset Token Expiry**: Password reset tokens expire after a configured time period
6. **Rate Limiting**: Login attempts are limited to prevent brute force attacks

### Validation Rules

- **Registration**:
  - Name: required, string, max 255 characters
  - Email: required, valid email format, unique in users table, max 255 characters
  - Password: required, minimum 8 characters (default Laravel password rules)
  - Password confirmation: must match password

- **Login**:
  - Email: required, valid email format
  - Password: required
  - Remember: optional boolean

- **Password Reset**:
  - Email: required, valid email format, must exist in users table
  - Password: required, minimum 8 characters
  - Token: required, must be valid and not expired

## Services

### CreateNewUser

**Location**: `app/Modules/Auth/Services/CreateNewUser.php`

**Responsibilities**:
- Validate registration input data
- Create new user accounts
- Enforce password validation rules

**Dependencies**:
- `App\Modules\User\Models\User`
- `Laravel\Fortify\Contracts\CreatesNewUsers`
- `PasswordValidationRules` trait

#### Methods

##### `create(array $input): User`

Creates a new user account after validating input data.

**Flow**:
1. Validate input data (name, email, password)
2. Check email uniqueness
3. Validate password strength
4. Create user with hashed password
5. Return created user

**Parameters**:
- `$input`: Array containing `name`, `email`, `password`, and `password_confirmation`

**Returns**: `User` model instance

**Throws**:
- `ValidationException` if validation fails

---

### ResetUserPassword

**Location**: `app/Modules/Auth/Services/ResetUserPassword.php`

**Responsibilities**:
- Validate password reset input
- Reset user password
- Enforce password validation rules

**Dependencies**:
- `App\Modules\User\Models\User`
- `Laravel\Fortify\Contracts\ResetsUserPasswords`
- `PasswordValidationRules` trait

#### Methods

##### `reset(User $user, array $input): void`

Resets a user's password after validating the new password.

**Flow**:
1. Validate new password
2. Check password strength requirements
3. Update user password (automatically hashed)
4. Save user

**Parameters**:
- `$user`: User model instance
- `$input`: Array containing `password` and `password_confirmation`

**Returns**: void

**Throws**:
- `ValidationException` if validation fails

---

### PasswordValidationRules

**Location**: `app/Modules/Auth/Services/PasswordValidationRules.php`

**Responsibilities**:
- Provide consistent password validation rules across the application
- Centralize password strength requirements

**Methods**:
- `passwordRules()`: Returns array of password validation rules

## Flows

### Registration Flow

```
1. User submits registration form
   ↓
2. CreateNewUser service validates input
   ↓
3. Check email uniqueness
   ↓
4. Validate password strength
   ↓
5. Create user with hashed password
   ↓
6. Send email verification (if enabled)
   ↓
7. Redirect to email verification page or dashboard
```

### Login Flow

```
1. User submits login form
   ↓
2. Fortify validates credentials
   ↓
3. Check rate limiting (5 attempts/minute)
   ↓
4. If 2FA enabled, redirect to 2FA challenge
   ↓
5. Create session
   ↓
6. Redirect to dashboard or intended URL
```

### Password Reset Flow

```
1. User requests password reset
   ↓
2. Generate reset token
   ↓
3. Send reset email with token
   ↓
4. User clicks link in email
   ↓
5. User submits new password
   ↓
6. ResetUserPassword service validates and updates password
   ↓
7. Invalidate reset token
   ↓
8. Redirect to login
```

### Two-Factor Authentication Flow

```
1. User enables 2FA
   ↓
2. Generate secret key
   ↓
3. Display QR code for authenticator app
   ↓
4. User scans QR code and enters code
   ↓
5. Verify code matches
   ↓
6. Save 2FA secret and recovery codes
   ↓
7. On login, if 2FA enabled:
   ↓
8. Show 2FA challenge page
   ↓
9. User enters code from authenticator app
   ↓
10. Verify code and complete login
```

## Controllers

**Note**: Auth endpoints are handled by Laravel Fortify, not custom controllers. Custom services implement Fortify contracts to customize behavior.

## Configuration

### FortifyServiceProvider

**Location**: `app/Providers/FortifyServiceProvider.php`

**Responsibilities**:
- Register custom Fortify actions (CreateNewUser, ResetUserPassword)
- Configure Inertia.js views for all auth pages
- Configure rate limiting for login and 2FA

**Key Methods**:
- `configureActions()`: Registers custom user creation and password reset services
- `configureViews()`: Maps Fortify routes to Inertia.js views
- `configureRateLimiting()`: Sets up rate limiters for login and 2FA

## Edge Cases

### Duplicate Email Registration
- **Scenario**: User tries to register with an email that already exists
- **Handling**: Validation fails with unique email rule, error message displayed

### Invalid Password Reset Token
- **Scenario**: User uses expired or invalid reset token
- **Handling**: Validation fails, user must request new reset link

### Rate Limit Exceeded
- **Scenario**: User exceeds login attempt rate limit
- **Handling**: User must wait before attempting login again, error message displayed

### 2FA Code Mismatch
- **Scenario**: User enters incorrect 2FA code
- **Handling**: Validation fails, user can retry (with rate limiting)

### Email Verification Required
- **Scenario**: User tries to access protected route without verified email
- **Handling**: Redirected to email verification page

## Security Considerations

1. **Password Hashing**: All passwords are automatically hashed using bcrypt before storage
2. **CSRF Protection**: All POST requests require valid CSRF tokens
3. **Rate Limiting**: Login and 2FA attempts are rate-limited to prevent brute force attacks
4. **Token Expiry**: Password reset tokens expire after a configured time period
5. **Session Security**: Sessions are secured with proper configuration
6. **Two-Factor Authentication**: Provides additional security layer for sensitive accounts
7. **Email Verification**: Ensures users have access to their email addresses
