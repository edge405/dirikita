# [Feature Name] Module - Testing

## Test Coverage

### Unit Tests

**Location**: `app/Modules/[ModuleName]/Tests/Unit/`

**Coverage**: X% for services

**Files**:
- `[Service]Test.php` - Tests service methods

### Feature Tests

**Location**: `app/Modules/[ModuleName]/Tests/Feature/`

**Coverage**: All endpoints tested

**Files**:
- `[Feature]Test.php` - Tests endpoints

## Unit Tests

### [Service]Test

**Location**: `app/Modules/[ModuleName]/Tests/Unit/[Service]Test.php`

#### Test: `test_method_name`

Description of test.

```php
public function test_method_name(): void
{
    // Arrange
    $data = [...];
    
    // Act
    $result = $this->service->method($data);
    
    // Assert
    $this->assertEquals(expected, $result);
}
```

## Feature Tests

### [Feature]Test

**Location**: `app/Modules/[ModuleName]/Tests/Feature/[Feature]Test.php`

#### Test: `test_endpoint_name`

Description of test.

```php
public function test_endpoint_name(): void
{
    $response = $this->postJson('/api/v1/endpoint', [
        'field' => 'value',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
        ]);
}
```

## Key Test Cases

- ✅ Test case 1
- ✅ Test case 2
- ✅ Test case 3

## Running Tests

```bash
# Run all tests
php artisan test app/Modules/[ModuleName]/Tests

# Run unit tests
php artisan test app/Modules/[ModuleName]/Tests/Unit

# Run feature tests
php artisan test app/Modules/[ModuleName]/Tests/Feature
```

## Test Best Practices

1. Use RefreshDatabase trait
2. Mock external dependencies
3. Test edge cases
4. Assert database state
5. Test response structure

