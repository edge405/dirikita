# Backend Rules

## Architecture Boundaries

### Controllers
- **Responsibilities**: Request parsing, Form Request validation, authorization checks, call service/action, return response
- **Thin Controllers**: Controllers should be thin - delegate to services
- **No Business Logic**: Business logic belongs in services, not controllers
- **Use Resources**: Always transform responses using Resources

### Services/Actions
- **Responsibilities**: Business logic + orchestration
- **Single Responsibility**: Each service should have one clear purpose
- **Transactions**: Wrap database operations in `DB::transaction()` when needed
- **Return Domain Objects**: Return models or DTOs, not arrays
- **Handle Exceptions**: Throw appropriate exceptions for errors

### Models
- **Responsibilities**: Relationships, casts, scopes only
- **No Business Workflows**: Business logic belongs in services
- **Mass Assignment**: Always use `$fillable` or `$guarded`
- **Relationships**: Define relationships in models
- **Query Scopes**: Use scopes for reusable queries

### Resources
- **Responsibilities**: API response transformation
- **Consistent Format**: All resources follow same structure
- **Hide Sensitive Data**: Never expose passwords, tokens, or sensitive info
- **Format Dates**: Use ISO 8601 format (YYYY-MM-DDTHH:mm:ssZ)

## Validation

- **Form Requests**: All input validated via Form Requests
- **Validation Errors**: Follow existing API error format
- **Custom Messages**: Provide clear, user-friendly error messages
- **Authorization**: Check authorization in `authorize()` method

## Authorization

- **Policies/Gates**: Must use Policies or Gates for authorization
- **Service Layer**: Enforce authorization in service/action layer, not just UI
- **Sensitive Operations**: Always check authorization for sensitive operations
- **Policy Methods**: Use descriptive policy method names (`view`, `update`, `delete`)

## API Standards

- **Response Format**: Consistent response shapes (Resources preferred)
- **Error Shape**: `{ success: false, error: { code, message, details? } }`
- **Success Shape**: `{ success: true, data: {...}, message?: string }`
- **Stable Sorting**: Lists should have stable, predictable sorting
- **Pagination**: Use Laravel's pagination (cursor preferred for large datasets)
- **Versioning**: All endpoints prefixed with `/api/v1`

## Data & Migrations

- **Production-Safe**: All migrations must be production-safe
- **Indexing**: Add indexes for frequently queried columns
- **Foreign Keys**: Use foreign keys for referential integrity
- **Transaction Boundaries**: Use `DB::transaction()` in services/actions for multi-step operations
- **Rollback Support**: All migrations must have working `down()` method

## Queues & Async

- **Jobs**: Use Jobs for long-running tasks
- **Idempotency**: Jobs should be idempotent (safe to retry)
- **Retry Logic**: Configure retry/backoff for failed jobs
- **Queue Names**: Use descriptive queue names
- **Failed Jobs**: Handle failed jobs appropriately

## Observability

- **Structured Logging**: Use structured logging with context
- **No Secrets/PII**: Never log passwords, tokens, or PII
- **Log Levels**: Use appropriate log levels (DEBUG, INFO, WARNING, ERROR, CRITICAL)
- **Audit Logs**: Log sensitive changes (user updates, deletions, etc.)
- **Error Tracking**: Send critical errors to error tracking service

## Testing (Definition of Done)

- **Unit Tests**: Unit tests for business rules in services
- **Feature Tests**: Feature tests for all endpoints
- **Test Coverage**: Aim for >80% coverage in business logic layers
- **Mock Dependencies**: Mock external dependencies in tests
- **Test Data**: Use factories for test data
- **Code Formatting**: Run `vendor/bin/pint --dirty` before committing

## Module Structure

Each module must follow this structure:

```
app/Modules/<ModuleName>/
├── Routes/
│   └── api.php          # Module routes
├── Controllers/         # Request handlers
├── Requests/            # Form validation
├── Services/            # Business logic
├── Models/              # Eloquent models (if needed)
├── Resources/           # API transformers
├── Policies/            # Authorization (if needed)
├── Events/              # Domain events (if needed)
├── Listeners/           # Event handlers (if needed)
├── Jobs/                # Queued tasks (if needed)
├── Migrations/          # Database migrations (if needed)
├── Seeders/             # Test data (if needed)
└── Tests/               # Tests
    ├── Unit/
    └── Feature/
```

## Cross-Module Communication

- **No Direct Model Access**: Other modules should not directly access another module's models
- **Use Services**: Access other modules through their services
- **Events**: Use events for cross-module communication
- **Service Interfaces**: Expose service interfaces for inter-module communication

## Error Handling

- **Use Shared Exceptions**: Use exceptions from `App\Shared\Exceptions\`
- **ApiException**: Base exception for API errors
- **ValidationException**: For validation errors
- **UnauthorizedException**: For authentication/authorization errors
- **Exception Handler**: Configured in `bootstrap/app.php` for consistent error responses

## Code Quality

- **PSR-12**: Follow PSR-12 coding standard
- **Laravel Pint**: Use Laravel Pint for code formatting
- **Type Hints**: Use type hints and return types
- **Docblocks**: Add docblocks for public methods
- **Naming**: Use descriptive names (camelCase for variables/methods, PascalCase for classes)

