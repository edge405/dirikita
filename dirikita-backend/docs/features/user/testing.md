# User Module - Testing

## Test Coverage

### Feature Tests

**Location**: `tests/Feature/Settings/`

**Coverage**: All user settings endpoints are tested

**Files**:
- `ProfileUpdateTest.php` - Tests profile update and account deletion
- `PasswordUpdateTest.php` - Tests password update
- `TwoFactorAuthenticationTest.php` - Tests two-factor authentication settings

## Feature Tests

### ProfileUpdateTest

**Location**: `tests/Feature/Settings/ProfileUpdateTest.php`

Tests profile update and account deletion functionality.

#### Test: `test_profile_page_is_displayed`

Verifies that the profile settings page can be displayed.

```php
public function test_profile_page_is_displayed(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/settings/profile');

    $response->assertStatus(200);
}
```

#### Test: `test_profile_information_can_be_updated`

Verifies that users can update their profile information.

```php
public function test_profile_information_can_be_updated(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response->assertSessionHasNoErrors();

    $user->refresh();

    $this->assertEquals('Test User', $user->name);
    $this->assertEquals('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
}
```

#### Test: `test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged`

Verifies that email verification status remains unchanged when email is not modified.

```php
public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
{
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $this->assertNotNull($user->refresh()->email_verified_at);
}
```

#### Test: `test_user_can_delete_their_account`

Verifies that users can delete their accounts.

```php
public function test_user_can_delete_their_account(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->delete('/settings/profile', [
            'password' => 'password',
        ]);

    $this->assertGuest();
    $this->assertNull($user->fresh());
    $response->assertRedirect('/');
}
```

#### Test: `test_correct_password_must_be_provided_to_delete_account`

Verifies that users must provide correct password to delete account.

```php
public function test_correct_password_must_be_provided_to_delete_account(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/settings/profile')
        ->delete('/settings/profile', [
            'password' => 'wrong-password',
        ]);

    $response->assertSessionHasErrorsIn('password', 'current_password');

    $this->assertNotNull($user->fresh());
}
```

---

### PasswordUpdateTest

**Location**: `tests/Feature/Settings/PasswordUpdateTest.php`

Tests password update functionality.

#### Test: `test_password_can_be_updated`

Verifies that users can update their passwords.

```php
public function test_password_can_be_updated(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->put('/settings/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response->assertSessionHasNoErrors();

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
}
```

#### Test: `test_correct_password_must_be_provided_to_update_password`

Verifies that users must provide correct current password to update password.

```php
public function test_correct_password_must_be_provided_to_update_password(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response->assertSessionHasErrorsIn('updatePassword', 'current_password');

    $this->assertTrue(Hash::check('password', $user->refresh()->password));
}
```

---

### TwoFactorAuthenticationTest

**Location**: `tests/Feature/Settings/TwoFactorAuthenticationTest.php`

Tests two-factor authentication settings functionality.

#### Test: `test_two_factor_authentication_settings_screen_can_be_rendered`

Verifies that the two-factor authentication settings screen can be displayed.

```php
public function test_two_factor_authentication_settings_screen_can_be_rendered(): void
{
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/two-factor');

    $response->assertStatus(200);
}
```

---

## Key Test Cases

- ✅ User can view profile settings page
- ✅ User can update profile information
- ✅ Email verification status is reset when email changes
- ✅ Email verification status is unchanged when email is unchanged
- ✅ User can delete their account with correct password
- ✅ User cannot delete account with incorrect password
- ✅ User can update password with correct current password
- ✅ User cannot update password with incorrect current password
- ✅ User can view two-factor authentication settings page

## Running Tests

```bash
# Run all user settings tests
php artisan test tests/Feature/Settings

# Run specific test file
php artisan test tests/Feature/Settings/ProfileUpdateTest.php

# Run with coverage
php artisan test --coverage tests/Feature/Settings
```

## Test Best Practices

1. **Use RefreshDatabase trait**: Ensures clean database state for each test
2. **Use factories**: Create test users using UserFactory
3. **Test both success and failure cases**: Verify validation and error handling
4. **Test authentication state**: Use `actingAs()` to authenticate users
5. **Test redirects**: Verify correct redirects after actions
6. **Test session errors**: Verify validation errors are displayed
7. **Test data changes**: Verify database is updated correctly
8. **Test edge cases**: Email changes, password mismatches, etc.
