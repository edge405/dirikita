# Auth Module - Testing

## Test Coverage

### Feature Tests

**Location**: `tests/Feature/Auth/`

**Coverage**: All authentication endpoints and flows are tested

**Files**:
- `AuthenticationTest.php` - Tests login and logout
- `RegistrationTest.php` - Tests user registration
- `PasswordResetTest.php` - Tests password reset flow
- `EmailVerificationTest.php` - Tests email verification
- `VerificationNotificationTest.php` - Tests verification email sending
- `PasswordConfirmationTest.php` - Tests password confirmation
- `TwoFactorChallengeTest.php` - Tests two-factor authentication

## Feature Tests

### AuthenticationTest

**Location**: `tests/Feature/Auth/AuthenticationTest.php`

Tests user login and logout functionality.

#### Test: `test_login_screen_can_be_rendered`

Verifies that the login screen can be displayed.

```php
public function test_login_screen_can_be_rendered(): void
{
    $response = $this->get('/login');

    $response->assertStatus(200);
}
```

#### Test: `test_users_can_authenticate_using_the_login_screen`

Verifies that users can successfully log in with valid credentials.

```php
public function test_users_can_authenticate_using_the_login_screen(): void
{
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
}
```

#### Test: `test_users_can_not_authenticate_with_invalid_password`

Verifies that users cannot log in with invalid credentials.

```php
public function test_users_can_not_authenticate_with_invalid_password(): void
{
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
}
```

#### Test: `test_users_can_logout`

Verifies that authenticated users can log out.

```php
public function test_users_can_logout(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
}
```

---

### RegistrationTest

**Location**: `tests/Feature/Auth/RegistrationTest.php`

Tests user registration functionality.

#### Test: `test_registration_screen_can_be_rendered`

Verifies that the registration screen can be displayed.

```php
public function test_registration_screen_can_be_rendered(): void
{
    $response = $this->get('/register');

    $response->assertStatus(200);
}
```

#### Test: `test_new_users_can_register`

Verifies that new users can successfully register.

```php
public function test_new_users_can_register(): void
{
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
}
```

---

### PasswordResetTest

**Location**: `tests/Feature/Auth/PasswordResetTest.php`

Tests password reset functionality.

#### Test: `test_reset_password_link_screen_can_be_rendered`

Verifies that the password reset link screen can be displayed.

```php
public function test_reset_password_link_screen_can_be_rendered(): void
{
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
}
```

#### Test: `test_reset_password_link_can_be_requested`

Verifies that users can request a password reset link.

```php
public function test_reset_password_link_can_be_requested(): void
{
    $user = User::factory()->create();

    $response = $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPasswordNotification::class);
}
```

#### Test: `test_password_can_be_reset_with_valid_token`

Verifies that users can reset their password with a valid token.

```php
public function test_password_can_be_reset_with_valid_token(): void
{
    $user = User::factory()->create();

    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token.'?email='.$user->email);

        $response->assertStatus(200);

        return true;
    });

    $this->post('/reset-password', [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertTrue(Hash::check('password', $user->fresh()->password));
}
```

---

### EmailVerificationTest

**Location**: `tests/Feature/Auth/EmailVerificationTest.php`

Tests email verification functionality.

#### Test: `test_email_verification_screen_can_be_rendered`

Verifies that the email verification screen can be displayed.

```php
public function test_email_verification_screen_can_be_rendered(): void
{
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/email/verify');

    $response->assertStatus(200);
}
```

#### Test: `test_email_can_be_verified`

Verifies that users can verify their email addresses.

```php
public function test_email_can_be_verified(): void
{
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    $this->assertTrue($user->fresh()->hasVerifiedEmail());
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
}
```

---

### TwoFactorChallengeTest

**Location**: `tests/Feature/Auth/TwoFactorChallengeTest.php`

Tests two-factor authentication challenge functionality.

#### Test: `test_two_factor_challenge_screen_can_be_rendered`

Verifies that the two-factor challenge screen can be displayed.

```php
public function test_two_factor_challenge_screen_can_be_rendered(): void
{
    $user = User::factory()->create();

    $this->actingAs($user)->get('/two-factor-challenge');

    $response->assertStatus(200);
}
```

---

## Key Test Cases

- ✅ User can view login screen
- ✅ User can successfully log in with valid credentials
- ✅ User cannot log in with invalid credentials
- ✅ User can log out
- ✅ User can view registration screen
- ✅ New users can register
- ✅ User can request password reset link
- ✅ User can reset password with valid token
- ✅ User can view email verification screen
- ✅ User can verify email address
- ✅ User can request verification email
- ✅ User can confirm password
- ✅ User can view two-factor challenge screen

## Running Tests

```bash
# Run all auth tests
php artisan test tests/Feature/Auth

# Run specific test file
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Run with coverage
php artisan test --coverage tests/Feature/Auth
```

## Test Best Practices

1. **Use RefreshDatabase trait**: Ensures clean database state for each test
2. **Use factories**: Create test users using UserFactory
3. **Test both success and failure cases**: Verify validation and error handling
4. **Test authentication state**: Use `assertAuthenticated()` and `assertGuest()`
5. **Test redirects**: Verify correct redirects after authentication actions
6. **Test notifications**: Verify emails are sent when appropriate
7. **Test rate limiting**: Verify rate limits are enforced
8. **Test edge cases**: Invalid tokens, expired tokens, etc.
