# User Module - Business Logic

## Domain Rules

### Business Invariants

1. **Email Uniqueness**: Each email address can only be associated with one user account (excluding the current user when updating)
2. **Password Verification**: Users must provide current password to change password or delete account
3. **Email Re-verification**: When email is changed, the email verification status is reset
4. **Self-Service Only**: Users can only manage their own profile and settings
5. **Account Deletion**: Account deletion logs out the user and invalidates the session

### Validation Rules

- **Profile Update**:
  - Name: required, string, max 255 characters
  - Email: required, valid email format, unique in users table (ignoring current user), lowercase, max 255 characters

- **Password Update**:
  - Current password: required, must match user's current password
  - Password: required, minimum 8 characters (default Laravel password rules)
  - Password confirmation: required, must match password

- **Account Deletion**:
  - Password: required, must match user's current password

## Controllers

### ProfileController

**Location**: `app/Modules/User/Controllers/ProfileController.php`

**Responsibilities**:
- Display profile settings page
- Handle profile updates
- Handle account deletion

**Dependencies**:
- `App\Modules\User\Requests\ProfileUpdateRequest`
- `Illuminate\Contracts\Auth\MustVerifyEmail`
- `Inertia\Inertia`

#### Methods

##### `edit(Request $request): Response`

Displays the profile settings page.

**Flow**:
1. Get authenticated user
2. Check if user must verify email
3. Get session status message
4. Return Inertia view with data

**Returns**: Inertia response with profile settings view

---

##### `update(ProfileUpdateRequest $request): RedirectResponse`

Updates the user's profile information.

**Flow**:
1. Validate input using ProfileUpdateRequest
2. Fill user model with validated data
3. If email changed, set `email_verified_at` to null
4. Save user
5. Redirect to profile settings page

**Parameters**:
- `$request`: ProfileUpdateRequest instance

**Returns**: Redirect to profile settings page

**Business Logic**:
- Email changes trigger email re-verification requirement

---

##### `destroy(Request $request): RedirectResponse`

Deletes the user's account.

**Flow**:
1. Validate password matches current password
2. Get authenticated user
3. Logout user
4. Delete user account
5. Invalidate session
6. Regenerate CSRF token
7. Redirect to home page

**Parameters**:
- `$request`: Request instance with password

**Returns**: Redirect to home page

**Throws**:
- `ValidationException` if password doesn't match

---

### PasswordController

**Location**: `app/Modules/User/Controllers/PasswordController.php`

**Responsibilities**:
- Display password settings page
- Handle password updates

**Dependencies**:
- `Illuminate\Validation\Rules\Password`

#### Methods

##### `edit(): Response`

Displays the password settings page.

**Returns**: Inertia response with password settings view

---

##### `update(Request $request): RedirectResponse`

Updates the user's password.

**Flow**:
1. Validate input (current password, new password, confirmation)
2. Verify current password matches
3. Update user password (automatically hashed)
4. Redirect back to password settings

**Parameters**:
- `$request`: Request instance with password data

**Returns**: Redirect back to password settings page

**Throws**:
- `ValidationException` if validation fails

**Rate Limiting**: 6 requests per minute via throttle middleware

---

### TwoFactorAuthenticationController

**Location**: `app/Modules/User/Controllers/TwoFactorAuthenticationController.php`

**Responsibilities**:
- Display two-factor authentication settings page

**Dependencies**:
- `App\Modules\User\Requests\TwoFactorAuthenticationRequest`
- `Laravel\Fortify\Features`

#### Methods

##### `show(TwoFactorAuthenticationRequest $request): Response`

Displays the two-factor authentication settings page.

**Flow**:
1. Ensure two-factor state is valid
2. Check if 2FA is enabled for user
3. Check if confirmation is required
4. Return Inertia view with 2FA status

**Parameters**:
- `$request`: TwoFactorAuthenticationRequest instance

**Returns**: Inertia response with 2FA settings view

**Middleware**: 
- Conditionally applies `password.confirm` middleware if configured in Fortify

---

## Requests

### ProfileUpdateRequest

**Location**: `app/Modules/User/Requests/ProfileUpdateRequest.php`

**Responsibilities**:
- Validate profile update input
- Ensure email uniqueness (ignoring current user)

**Validation Rules**:
- `name`: required, string, max:255
- `email`: required, string, lowercase, email, max:255, unique:users (ignoring current user)

---

### TwoFactorAuthenticationRequest

**Location**: `app/Modules/User/Requests/TwoFactorAuthenticationRequest.php`

**Responsibilities**:
- Authorize two-factor authentication requests
- Ensure two-factor state is valid

**Authorization**: 
- Checks if two-factor authentication feature is enabled in Fortify

**Validation**: No validation rules (empty array)

**Traits**:
- `InteractsWithTwoFactorState` - Provides state validation methods

---

## Flows

### Profile Update Flow

```
1. User submits profile update form
   ↓
2. ProfileUpdateRequest validates input
   ↓
3. Check email uniqueness (ignoring current user)
   ↓
4. Fill user model with validated data
   ↓
5. If email changed:
   - Set email_verified_at to null
   ↓
6. Save user
   ↓
7. Redirect to profile settings
```

### Password Update Flow

```
1. User submits password update form
   ↓
2. Validate current password matches
   ↓
3. Validate new password strength
   ↓
4. Check rate limiting (6 requests/minute)
   ↓
5. Update user password (hashed automatically)
   ↓
6. Redirect back to password settings
```

### Account Deletion Flow

```
1. User submits account deletion form
   ↓
2. Validate password matches current password
   ↓
3. Logout user
   ↓
4. Delete user account
   ↓
5. Invalidate session
   ↓
6. Regenerate CSRF token
   ↓
7. Redirect to home page
```

### Two-Factor Settings View Flow

```
1. User navigates to 2FA settings
   ↓
2. Check if password confirmation required
   ↓
3. If required, show password confirmation
   ↓
4. Ensure 2FA state is valid
   ↓
5. Get 2FA status from user
   ↓
6. Display 2FA settings view
```

## Edge Cases

### Duplicate Email on Update
- **Scenario**: User tries to update email to one that already exists
- **Handling**: Validation fails with unique email rule (ignoring current user), error message displayed

### Invalid Current Password
- **Scenario**: User tries to update password or delete account with wrong current password
- **Handling**: Validation fails, error message displayed

### Rate Limit Exceeded
- **Scenario**: User exceeds password update rate limit
- **Handling**: User must wait before attempting password update again, error message displayed

### Email Change Without Verification
- **Scenario**: User changes email but doesn't verify new email
- **Handling**: Email verification status is reset, user must verify new email to access protected features

## Security Considerations

1. **Authentication Required**: All endpoints require authentication
2. **Password Verification**: Password changes and account deletion require current password
3. **Email Re-verification**: Email changes require re-verification for security
4. **Rate Limiting**: Password updates are rate-limited to prevent brute force attacks
5. **Session Invalidation**: Account deletion invalidates all sessions
6. **CSRF Protection**: All POST/PUT/DELETE requests require valid CSRF tokens
7. **Password Confirmation**: Two-factor settings may require password confirmation
