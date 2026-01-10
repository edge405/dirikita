# Auth Module - Data Model

## Database Tables

### `users`

Stores user account information and authentication data.

**Schema**:
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    two_factor_secret TEXT NULL,
    two_factor_recovery_codes TEXT NULL,
    two_factor_confirmed_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_users_email (email)
);
```

**Fields**:
- `id` - Primary key, auto-incrementing
- `name` - User's full name, required, max 255 characters
- `email` - User's email address, required, unique, max 255 characters
- `email_verified_at` - Timestamp when email was verified, nullable
- `password` - Hashed password, required
- `two_factor_secret` - Encrypted 2FA secret key, nullable
- `two_factor_recovery_codes` - Encrypted recovery codes for 2FA, nullable
- `two_factor_confirmed_at` - Timestamp when 2FA was confirmed, nullable
- `remember_token` - Token for "remember me" functionality, nullable
- `created_at`, `updated_at` - Timestamps

**Indexes**:
- Primary key on `id`
- Unique index on `email`

---

### `password_reset_tokens`

Stores password reset tokens for users who request password resets.

**Schema**:
```sql
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);
```

**Fields**:
- `email` - User's email address, primary key
- `token` - Hashed password reset token
- `created_at` - Timestamp when token was created

**Indexes**:
- Primary key on `email`

**Note**: Tokens expire after a configured time period (default: 1 hour in Laravel)

---

### `sessions`

Stores user session data for web authentication.

**Schema**:
```sql
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_last_activity (last_activity)
);
```

**Fields**:
- `id` - Session ID, primary key
- `user_id` - Foreign key to users table, nullable (for guest sessions)
- `ip_address` - IP address of the session, nullable
- `user_agent` - User agent string, nullable
- `payload` - Serialized session data
- `last_activity` - Unix timestamp of last activity

**Indexes**:
- Primary key on `id`
- Index on `user_id`
- Index on `last_activity`

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

**Relationships**: None defined in Auth module (User module may define relationships)

## Relationships

The Auth module primarily deals with the User model in isolation. Relationships are defined in other modules (e.g., User module, Organization module).

## Data Flow

### Registration Data Flow

```
1. User submits registration form
   ↓
2. CreateNewUser service validates input
   ↓
3. User model creates record with:
   - name (plain text)
   - email (plain text, lowercase)
   - password (hashed automatically via cast)
   ↓
4. User record saved to database
   ↓
5. Email verification notification sent (if enabled)
```

### Password Reset Data Flow

```
1. User requests password reset
   ↓
2. Password reset token generated and hashed
   ↓
3. Token stored in password_reset_tokens table
   ↓
4. Email sent with reset link
   ↓
5. User submits new password
   ↓
6. ResetUserPassword service validates and updates
   ↓
7. User password updated (hashed automatically)
   ↓
8. Password reset token deleted
```

### Two-Factor Authentication Data Flow

```
1. User enables 2FA
   ↓
2. Secret key generated
   ↓
3. Recovery codes generated
   ↓
4. Secret and codes encrypted
   ↓
5. Stored in users table:
   - two_factor_secret
   - two_factor_recovery_codes
   ↓
6. User confirms 2FA with code
   ↓
7. two_factor_confirmed_at timestamp set
```

## Migrations

### Create Users Table

**Location**: `app/Modules/User/Migrations/0001_01_01_000000_create_users_table.php`

Creates the `users`, `password_reset_tokens`, and `sessions` tables.

**Key Points**:
- Users table includes standard authentication fields
- Password reset tokens table for password reset functionality
- Sessions table for session-based authentication

### Add Two Factor Columns

**Location**: `app/Modules/User/Migrations/2025_08_26_100418_add_two_factor_columns_to_users_table.php`

Adds two-factor authentication columns to the users table.

**Columns Added**:
- `two_factor_secret` - After `password`
- `two_factor_recovery_codes` - After `two_factor_secret`
- `two_factor_confirmed_at` - After `two_factor_recovery_codes`

## Data Integrity

### Constraints

1. **Email Uniqueness**: Enforced by unique index on `email` column
2. **Password Reset Token Expiry**: Tokens expire after configured time (handled by Laravel)
3. **Session Cleanup**: Old sessions are automatically cleaned up by Laravel's session garbage collection

### Indexes for Performance

1. **Users Email Index**: Unique index on `email` for fast lookups and uniqueness enforcement
2. **Sessions User ID Index**: Index on `user_id` for fast session lookups by user
3. **Sessions Last Activity Index**: Index on `last_activity` for efficient session cleanup

## Security Considerations

1. **Password Hashing**: Passwords are automatically hashed using bcrypt via Laravel's `hashed` cast
2. **2FA Secret Encryption**: Two-factor secrets are encrypted before storage
3. **Recovery Codes Encryption**: Recovery codes are encrypted before storage
4. **Token Hashing**: Password reset tokens are hashed before storage
5. **Session Security**: Sessions are stored securely with proper configuration
6. **Email Verification**: Email verification ensures users have access to their email addresses
