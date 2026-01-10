# User Module

## Summary

The User module provides user profile management, password updates, and two-factor authentication settings. It allows authenticated users to manage their account information, change passwords, and configure security settings. All features are implemented as Inertia.js views for seamless SPA integration.

## Requirements / User Stories

- As a user, I want to view my profile information so that I can see my account details
- As a user, I want to update my profile information so that I can keep my account details current
- As a user, I want to delete my account so that I can remove my data from the system
- As a user, I want to change my password so that I can maintain account security
- As a user, I want to configure two-factor authentication settings so that I can manage my security preferences

## Module Structure

```
app/Modules/User/
├── Controllers/
│   ├── ProfileController.php
│   ├── PasswordController.php
│   └── TwoFactorAuthenticationController.php
├── Models/
│   └── User.php
├── Requests/
│   ├── ProfileUpdateRequest.php
│   └── TwoFactorAuthenticationRequest.php
├── Migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   └── 2025_08_26_100418_add_two_factor_columns_to_users_table.php
├── Routes/
│   └── api.php
└── Seeders/
    └── UserSeeder.php
```

## Quick Links

- [API Endpoints](./api.md) - Complete endpoint documentation
- [Business Logic](./business-logic.md) - Services, flows, and domain rules
- [Data Model](./data-model.md) - Database schema and relationships
- [Testing](./testing.md) - Test coverage and examples

## Key Features

- **Profile Management**: View, update, and delete user profile information
- **Password Management**: Update user password with current password verification
- **Two-Factor Authentication Settings**: View and manage 2FA configuration
- **Email Verification Handling**: Automatically unverify email when email is changed

## Dependencies

- **Auth Module**: Uses authentication middleware and user model
- **Laravel Fortify**: Uses Fortify's two-factor authentication features
- **Laravel Inertia**: Server-side rendering for SPA views
- **User Model**: Core user model with authentication capabilities

## Configuration

User settings routes are configured in `app/Modules/User/Routes/api.php` and registered in the main routes file. All routes require authentication middleware.

Rate limiting is applied to password updates:
- Password Update: 6 requests per minute
