# User Module - Data Model

## Database Tables

### `users`

Stores user account information. This table is shared with the Auth module.

**Schema**: See [Auth Module Data Model](../auth/data-model.md) for complete schema.

**Key Fields for User Module**:
- `id` - Primary key
- `name` - User's full name (updatable via profile)
- `email` - User's email address (updatable via profile, requires re-verification if changed)
- `email_verified_at` - Timestamp when email was verified (reset to null if email changes)
- `password` - Hashed password (updatable via password settings)
- `two_factor_secret` - Encrypted 2FA secret key
- `two_factor_recovery_codes` - Encrypted recovery codes for 2FA
- `two_factor_confirmed_at` - Timestamp when 2FA was confirmed
- `created_at`, `updated_at` - Timestamps

## Models

### User

**Location**: `app/Modules/User/Models/User.php`

**Key Attributes**:
```php
protected $fillable = [
    'name',
    'email',
    'password',
];

protected $hidden = [
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
    ];
}
```

**Traits**:
- `HasFactory` - For model factories
- `Notifiable` - For sending notifications
- `TwoFactorAuthenticatable` - For 2FA functionality (from Laravel Fortify)

**Key Methods** (from traits):
- `hasEnabledTwoFactorAuthentication()` - Checks if 2FA is enabled
- `twoFactorQrCodeSvg()` - Generates QR code for 2FA setup
- `recoveryCodes()` - Gets recovery codes

## Relationships

The User model in this module doesn't define relationships yet. Relationships may be added in future modules (e.g., Organization module, Asset module).

## Data Flow

### Profile Update Data Flow

```
1. User submits profile update form
   ↓
2. ProfileUpdateRequest validates:
   - name: required, string, max:255
   - email: required, email, unique (ignoring current user)
   ↓
3. User model updated with:
   - name (plain text)
   - email (plain text, lowercase)
   ↓
4. If email changed:
   - email_verified_at set to null
   ↓
5. User record saved to database
   ↓
6. Redirect to profile settings
```

### Password Update Data Flow

```
1. User submits password update form
   ↓
2. Validate:
   - current_password: must match user's password
   - password: min 8 characters
   - password_confirmation: must match password
   ↓
3. Check rate limiting (6 requests/minute)
   ↓
4. User password updated:
   - password (hashed automatically via cast)
   ↓
5. User record saved to database
   ↓
6. Redirect to password settings
```

### Account Deletion Data Flow

```
1. User submits account deletion form
   ↓
2. Validate password matches current password
   ↓
3. User logged out
   ↓
4. User record deleted from database
   ↓
5. Session invalidated
   ↓
6. CSRF token regenerated
   ↓
7. Redirect to home page
```

### Two-Factor Authentication Data Flow

**Note**: 2FA enable/disable is handled by Laravel Fortify, not directly by User module controllers. The User module only provides the settings view.

```
1. User views 2FA settings page
   ↓
2. Check if 2FA is enabled (from user model)
   ↓
3. Display 2FA status and configuration options
   ↓
4. User enables/disables 2FA (handled by Fortify)
   ↓
5. User model updated:
   - two_factor_secret (if enabling)
   - two_factor_recovery_codes (if enabling)
   - two_factor_confirmed_at (if enabling)
   ↓
6. Changes saved to database
```

## Migrations

### Create Users Table

**Location**: `app/Modules/User/Migrations/0001_01_01_000000_create_users_table.php`

Creates the `users` table. See [Auth Module Data Model](../auth/data-model.md) for details.

### Add Two Factor Columns

**Location**: `app/Modules/User/Migrations/2025_08_26_100418_add_two_factor_columns_to_users_table.php`

Adds two-factor authentication columns to the users table.

**Columns Added**:
- `two_factor_secret` - After `password` column
- `two_factor_recovery_codes` - After `two_factor_secret` column
- `two_factor_confirmed_at` - After `two_factor_recovery_codes` column

**Migration Details**:
```php
Schema::table('users', function (Blueprint $table) {
    $table->text('two_factor_secret')->after('password')->nullable();
    $table->text('two_factor_recovery_codes')->after('two_factor_secret')->nullable();
    $table->timestamp('two_factor_confirmed_at')->after('two_factor_recovery_codes')->nullable();
});
```

## Data Integrity

### Constraints

1. **Email Uniqueness**: Enforced by unique index on `email` column (with exception for current user during updates)
2. **Password Hashing**: Passwords are automatically hashed using bcrypt via Laravel's `hashed` cast
3. **Email Verification Reset**: When email changes, `email_verified_at` is set to null
4. **2FA Data Encryption**: Two-factor secrets and recovery codes are encrypted before storage

### Indexes for Performance

1. **Users Email Index**: Unique index on `email` for fast lookups and uniqueness enforcement
2. **Primary Key**: Index on `id` for fast user lookups

## Security Considerations

1. **Password Hashing**: Passwords are automatically hashed using bcrypt via Laravel's `hashed` cast
2. **2FA Secret Encryption**: Two-factor secrets are encrypted before storage
3. **Recovery Codes Encryption**: Recovery codes are encrypted before storage
4. **Email Re-verification**: Email changes require re-verification for security
5. **Password Verification**: Password changes and account deletion require current password verification
6. **Rate Limiting**: Password updates are rate-limited to prevent brute force attacks
7. **Session Invalidation**: Account deletion invalidates all user sessions
