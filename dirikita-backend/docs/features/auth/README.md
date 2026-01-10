# Auth Module

## Summary

The Auth module provides comprehensive authentication functionality using Laravel Fortify. It handles user registration, login, logout, password reset, email verification, and two-factor authentication. All authentication flows are implemented as Inertia.js views for seamless SPA integration.

## Requirements / User Stories

- As a new user, I want to register an account so that I can access the application
- As a user, I want to log in to my account so that I can access protected features
- As a user, I want to log out so that I can securely end my session
- As a user, I want to reset my password if I forget it so that I can regain access to my account
- As a user, I want to verify my email address so that I can ensure account security
- As a user, I want to enable two-factor authentication so that I can add an extra layer of security
- As a user, I want to confirm my password before sensitive operations so that unauthorized access is prevented

## Module Structure

```
app/Modules/Auth/
├── Services/
│   ├── CreateNewUser.php
│   ├── PasswordValidationRules.php
│   └── ResetUserPassword.php
```

**Note**: Auth routes are handled by Laravel Fortify and configured in `app/Providers/FortifyServiceProvider.php`. The module provides custom services that implement Fortify contracts.

## Quick Links

- [API Endpoints](./api.md) - Complete endpoint documentation
- [Business Logic](./business-logic.md) - Services, flows, and domain rules
- [Data Model](./data-model.md) - Database schema and relationships
- [Testing](./testing.md) - Test coverage and examples

## Key Features

- **User Registration**: Create new user accounts with email and password validation
- **User Login**: Authenticate users with email and password, with rate limiting
- **User Logout**: Securely end user sessions
- **Password Reset**: Request password reset link via email and reset password with token
- **Email Verification**: Send verification emails and verify user email addresses
- **Two-Factor Authentication**: Enable/disable 2FA with TOTP support
- **Password Confirmation**: Require password confirmation for sensitive operations

## Dependencies

- **Laravel Fortify**: Core authentication package providing routes and middleware
- **Laravel Inertia**: Server-side rendering for SPA views
- **User Module**: Uses `App\Modules\User\Models\User` model
- **Laravel Notifications**: For sending password reset and verification emails

## Configuration

Authentication features are configured in `config/fortify.php`:

- Registration: Enabled
- Password Reset: Enabled
- Email Verification: Enabled
- Two-Factor Authentication: Enabled with confirmation and password confirmation

Rate limiting is configured in `FortifyServiceProvider`:
- Login: 5 requests per minute per email/IP combination
- Two-Factor: 5 requests per minute per session
