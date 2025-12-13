# Flutter + Laravel Modular Architecture Documentation

## 1. System Overview

### 1.1 High-Level Architecture

The application follows a **client-server architecture** with clear separation between the Flutter mobile frontend and the Laravel REST API backend. The two components communicate exclusively through HTTP/HTTPS using a well-defined API contract.

```
┌─────────────────────────────────────────────────────────────┐
│                    Flutter Mobile App                       │
│  (Presentation → Application → Domain → Data Layers)        │
└──────────────────────────┬──────────────────────────────────┘
                           │ HTTP/HTTPS
                           │ REST API Calls
                           │
┌──────────────────────────▼──────────────────────────────────┐
│                  Laravel REST API Backend                   │
│  (Routes → Controllers → Services → Models → Database)      │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Key Architectural Principles

**Modularity**: Both frontend and backend are organized into independent, self-contained modules that encapsulate related functionality. Each module has clear boundaries and minimal cross-module dependencies.

**Separation of Concerns**: Responsibilities are strictly divided into layers. The presentation layer handles UI logic, the application layer orchestrates use cases, the domain layer contains business rules, and the data layer manages persistence and external communication.

**Scalability**: The modular structure allows teams to work independently on different features without causing integration conflicts. New features are added as new modules following established patterns.

**Testability**: Clear layer boundaries and dependency injection enable comprehensive unit and integration testing at each layer without requiring the entire application to be running.

**Maintainability**: Consistent conventions across the codebase make it easier for new developers to understand and modify the system. Well-documented interfaces and contracts reduce cognitive load.

### 1.3 Definition of "Module"

**Frontend Module (Flutter)**: A feature module is a self-contained unit that includes all layers (Presentation, Application, Domain, Data) for a specific feature. Examples: `auth`, `profile`, `products`, `orders`. Each module can be developed, tested, and maintained independently.

**Backend Module (Laravel)**: A module is a logical grouping of related functionality organized under `app/Modules/<ModuleName>/`. It contains routes, controllers, services, models, migrations, and tests specific to that domain. Examples: `Auth`, `User`, `Product`, `Order`.

---

## 2. Flutter Frontend Architecture

### 2.1 Architectural Style: Feature-First Clean Architecture

The Flutter frontend uses **Clean Architecture** principles organized by **feature modules**. This approach combines the benefits of layered architecture with feature-based organization.

#### Layer Definitions

**Presentation Layer**:
- Responsible for rendering UI and handling user interactions
- Contains Widgets, Screens, and State Management (Bloc/Cubit or Riverpod)
- Converts user actions into application requests
- Displays data from the Application layer in a user-friendly format
- No business logic; purely concerned with UI logic and state representation
- Depends on: Application and Domain layers only

**Application Layer (Use Cases)**:
- Orchestrates the flow of data and business logic
- Contains Use Cases (or Interactors) that represent specific user actions
- Coordinates between Presentation and Domain layers
- Handles application-specific logic (e.g., validation, transformation)
- Does not directly access data sources; delegates to Domain layer
- Depends on: Domain layer only

**Domain Layer**:
- Contains core business logic and rules
- Defines Entities (business objects) and Repositories (abstract interfaces)
- Independent of any framework or external library
- Represents the "pure business logic" of the application
- Does not depend on any other layer
- Depends on: Nothing (only Dart standard library)

**Data Layer**:
- Implements Repository interfaces defined in the Domain layer
- Manages communication with external sources (APIs, local database, caches)
- Contains Data Sources (Remote and Local), DTOs, and Mappers
- Handles data transformation from external formats to Domain Entities
- Depends on: Domain layer only

### 2.2 Dependency Rules

The following rules enforce clean architecture principles and prevent circular dependencies:

1. **Presentation → Application & Domain**: Presentation layer can import from Application and Domain layers. It must never directly import from Data layer.

2. **Application → Domain**: Application layer can only import from Domain layer. It must never import from Presentation or Data layers.

3. **Domain → Nothing**: Domain layer is completely independent. It imports only Dart standard library and pure business logic packages.

4. **Data → Domain**: Data layer implements interfaces defined in Domain. It can import Domain but never Presentation or Application.

5. **Cross-Feature Imports**: Features should not import from each other's layers. If cross-feature communication is needed, use a shared event bus, service locator, or dedicated shared module.

6. **Core/Shared Access**: All layers can import from `core/` and `shared/` modules, but these must follow the same layering rules internally.

### 2.3 Flutter Module Structure

#### Complete Project Folder Tree

```
lib/
├── core/
│   ├── constants/
│   │   ├── app_constants.dart
│   │   ├── api_constants.dart
│   │   └── string_constants.dart
│   ├── di/
│   │   ├── service_locator.dart
│   │   └── injection_container.dart
│   ├── network/
│   │   ├── api_client.dart
│   │   ├── interceptors.dart
│   │   ├── error_handler.dart
│   │   └── network_exception.dart
│   ├── storage/
│   │   ├── secure_storage.dart
│   │   ├── local_storage.dart
│   │   └── cache_manager.dart
│   ├── theme/
│   │   ├── app_theme.dart
│   │   ├── colors.dart
│   │   └── text_styles.dart
│   ├── utils/
│   │   ├── date_formatter.dart
│   │   ├── validators.dart
│   │   └── extensions.dart
│   └── widgets/
│       ├── app_button.dart
│       ├── app_text_field.dart
│       ├── error_widget.dart
│       └── loading_widget.dart
│
├── shared/
│   ├── domain/
│   │   ├── entities/
│   │   │   ├── user_entity.dart
│   │   │   └── error_entity.dart
│   │   └── repositories/
│   │       └── auth_repository.dart
│   ├── data/
│   │   ├── datasources/
│   │   │   ├── auth_remote_datasource.dart
│   │   │   └── auth_local_datasource.dart
│   │   ├── models/
│   │   │   └── user_model.dart
│   │   ├── repositories/
│   │   │   └── auth_repository_impl.dart
│   │   └── mappers/
│   │       └── user_mapper.dart
│   └── presentation/
│       └── providers/
│           └── auth_provider.dart
│
├── features/
│   ├── auth/
│   │   ├── domain/
│   │   │   ├── entities/
│   │   │   │   ├── login_params.dart
│   │   │   │   └── register_params.dart
│   │   │   ├── repositories/
│   │   │   │   └── auth_repository.dart
│   │   │   └── usecases/
│   │   │       ├── login_usecase.dart
│   │   │       ├── register_usecase.dart
│   │   │       ├── logout_usecase.dart
│   │   │       └── refresh_token_usecase.dart
│   │   ├── application/
│   │   │   └── bloc/ (or cubit/)
│   │   │       ├── auth_bloc.dart
│   │   │       ├── auth_event.dart
│   │   │       └── auth_state.dart
│   │   ├── data/
│   │   │   ├── datasources/
│   │   │   │   ├── auth_remote_datasource.dart
│   │   │   │   └── auth_local_datasource.dart
│   │   │   ├── models/
│   │   │   │   ├── login_request_model.dart
│   │   │   │   ├── login_response_model.dart
│   │   │   │   └── token_model.dart
│   │   │   ├── repositories/
│   │   │   │   └── auth_repository_impl.dart
│   │   │   └── mappers/
│   │   │       ├── login_mapper.dart
│   │   │       └── token_mapper.dart
│   │   └── presentation/
│   │       ├── pages/
│   │       │   ├── login_page.dart
│   │       │   ├── register_page.dart
│   │       │   └── splash_page.dart
│   │       ├── widgets/
│   │       │   ├── login_form.dart
│   │       │   └── register_form.dart
│   │       └── routes/
│   │           └── auth_routes.dart
│   │
│   ├── profile/
│   │   ├── domain/
│   │   │   ├── entities/
│   │   │   │   └── profile_entity.dart
│   │   │   ├── repositories/
│   │   │   │   └── profile_repository.dart
│   │   │   └── usecases/
│   │   │       ├── get_profile_usecase.dart
│   │   │       ├── update_profile_usecase.dart
│   │   │       └── upload_avatar_usecase.dart
│   │   ├── application/
│   │   │   └── bloc/
│   │   │       ├── profile_bloc.dart
│   │   │       ├── profile_event.dart
│   │   │       └── profile_state.dart
│   │   ├── data/
│   │   │   ├── datasources/
│   │   │   │   └── profile_remote_datasource.dart
│   │   │   ├── models/
│   │   │   │   ├── profile_model.dart
│   │   │   │   └── update_profile_request_model.dart
│   │   │   ├── repositories/
│   │   │   │   └── profile_repository_impl.dart
│   │   │   └── mappers/
│   │   │       └── profile_mapper.dart
│   │   └── presentation/
│   │       ├── pages/
│   │       │   ├── profile_page.dart
│   │       │   └── edit_profile_page.dart
│   │       ├── widgets/
│   │       │   ├── profile_header.dart
│   │       │   └── profile_form.dart
│   │       └── routes/
│   │           └── profile_routes.dart
│   │
│   └── [other features follow same structure]
│
├── main.dart
└── app.dart
```

#### Feature Module Template

Each feature follows this consistent structure:

```
features/<feature_name>/
├── domain/
│   ├── entities/
│   │   └── [business objects]
│   ├── repositories/
│   │   └── [abstract repository interfaces]
│   └── usecases/
│       └── [use case implementations]
├── application/
│   └── bloc/ (or cubit/)
│       ├── [bloc/cubit files]
│       ├── [event files]
│       └── [state files]
├── data/
│   ├── datasources/
│   │   ├── [remote_datasource]
│   │   └── [local_datasource]
│   ├── models/
│   │   └── [DTO classes]
│   ├── repositories/
│   │   └── [repository implementations]
│   └── mappers/
│       └── [entity ↔ model mappers]
└── presentation/
    ├── pages/
    │   └── [full-screen widgets]
    ├── widgets/
    │   └── [reusable components]
    └── routes/
        └── [route definitions for this feature]
```

#### Core vs Shared Modules

**Core Module** (`lib/core/`):
- Framework-agnostic utilities and infrastructure
- Network client, storage, theme, constants
- Dependency injection setup
- Common exceptions and error handling
- Reusable widgets and utilities used across features
- No feature-specific logic

**Shared Module** (`lib/shared/`):
- Cross-feature domain entities and repositories (e.g., User entity used in multiple features)
- Shared data sources and models
- Shared providers or state management for global concerns (e.g., authentication state)
- Utilities specific to business domain but not tied to a single feature

**Distinction**: Core is technical infrastructure; Shared is business-domain infrastructure.

### 2.4 State Management, Routing, and Dependency Injection

#### State Management: BLoC/Cubit

**Selection Rationale**: BLoC/Cubit provides clear separation between business logic and UI, excellent testability, and a mature ecosystem. Cubit is simpler for straightforward state, BLoC for complex event-driven scenarios.

**Rules**:

1. **One BLoC/Cubit per Feature**: Each feature has one or more BLoCs/Cubits in the `application/` layer, never in Presentation.

2. **BLoC Responsibilities**:
   - Receives events from the Presentation layer
   - Calls Use Cases from the Domain layer
   - Emits states that the Presentation layer listens to
   - Never directly accesses Data layer

3. **State Structure**:
   - Initial state (e.g., `AuthInitial`)
   - Loading state (e.g., `AuthLoading`)
   - Success state (e.g., `AuthSuccess`)
   - Error state (e.g., `AuthError`)

4. **Registration**: BLoCs/Cubits are registered in the Service Locator during app initialization.

5. **Cross-Feature Communication**: Features should not directly access other features' BLoCs. Use events or a shared event bus for inter-feature communication.

#### Routing: GoRouter

**Selection Rationale**: GoRouter provides declarative routing, deep linking support, and type-safe navigation. It integrates well with state management and handles complex navigation scenarios.

**Rules**:

1. **Centralized Route Configuration**: All routes are defined in a single `app_routes.dart` file or per-feature route files that are composed together.

2. **Feature Routes**: Each feature defines its routes in `presentation/routes/<feature>_routes.dart`.

3. **Route Naming Convention**: Routes follow the pattern `/feature/action`, e.g., `/auth/login`, `/profile/edit`.

4. **Deep Linking**: All routes must support deep linking. Parameters are passed via URL segments or query parameters.

5. **Navigation Example**:
   ```
   context.go('/auth/login?redirect=/profile');
   context.push('/profile/edit');
   ```

6. **Route Guards**: Authentication and authorization checks are performed using GoRouter's redirect logic, not in individual screens.

#### Dependency Injection: GetIt

**Selection Rationale**: GetIt is a simple, lightweight service locator that provides lazy initialization, singletons, and factory patterns. It's widely used in the Flutter community.

**Rules**:

1. **Service Locator Setup**: All dependencies are registered in `core/di/service_locator.dart` during app initialization.

2. **Registration Pattern**:
   - Repositories: Registered as singletons
   - Use Cases: Registered as factories or singletons
   - BLoCs/Cubits: Registered as factories (new instance per use)
   - Data Sources: Registered as singletons

3. **Feature Registration**: Each feature can have a registration function (e.g., `_registerAuthFeature()`) that is called during initialization.

4. **Access Pattern**: Dependencies are accessed via `getIt<ClassName>()` or injected into constructors.

5. **Preventing Cross-Feature Coupling**: Features register their own dependencies. If Feature A needs something from Feature B, Feature B must expose it as a public interface (repository or use case).

---

### 2.5 Data & Networking

#### API Client Abstraction

The API client is a singleton that wraps HTTP communication and provides a consistent interface for all data sources.

**Responsibilities**:
- HTTP request/response handling
- Request interceptors (auth token injection, logging)
- Response interceptors (error mapping, token refresh)
- Timeout and retry logic
- Base URL and headers management

**Error Mapping Strategy**:
- HTTP errors (4xx, 5xx) are caught and mapped to domain-level exceptions
- Network errors are wrapped in `NetworkException`
- Server validation errors are mapped to `ValidationException`
- Unknown errors are wrapped in `UnknownException`

**Interceptors**:
- **Auth Interceptor**: Injects the stored auth token into request headers
- **Logging Interceptor**: Logs all requests and responses for debugging
- **Error Interceptor**: Catches errors and attempts token refresh if needed
- **Timeout Interceptor**: Enforces request timeouts

#### Authentication Token Storage and Refresh

**Storage Strategy**:
- Access tokens are stored in **secure storage** (Flutter Secure Storage)
- Refresh tokens are stored in **secure storage**
- Token expiration time is stored in **secure storage** for client-side validation
- No tokens are stored in SharedPreferences (insecure)

**Refresh Behavior**:
- Before each request, the app checks if the access token is expired
- If expired and a refresh token exists, the app automatically refreshes the token
- If refresh fails, the user is logged out and redirected to the login screen
- Token refresh happens transparently without user intervention

**Logout Strategy**:
- Access and refresh tokens are deleted from secure storage
- The user is redirected to the login screen
- Any pending requests are cancelled

#### Local Caching Strategy

**What is Cached**:
- User profile data (invalidated on logout or explicit refresh)
- Product lists (invalidated after 5 minutes or on explicit refresh)
- Search results (invalidated after 10 minutes)
- Static data (invalidated after 1 hour)

**Where Caching Happens**:
- Caching is implemented in the Data layer, specifically in the Repository implementation
- The Repository checks the local cache before making a network request
- The Repository updates the cache after a successful network response

**Invalidation Rules**:
- Time-based: Cache expires after a configured duration
- Event-based: Cache is invalidated when related data is modified (e.g., profile cache invalidated when profile is updated)
- Manual: User can explicitly refresh to bypass cache
- Logout: All caches are cleared on logout

#### DTO ↔ Domain Mapping

**DTO (Data Transfer Object)**:
- Models that represent the structure of API responses
- Located in `data/models/`
- Named with `Model` suffix, e.g., `UserModel`, `LoginResponseModel`
- Contain JSON serialization/deserialization logic

**Domain Entity**:
- Pure business objects representing domain concepts
- Located in `domain/entities/`
- Named without suffix, e.g., `User`, `LoginResponse`
- Contain no serialization logic

**Mapping Rules**:
- Mappers are located in `data/mappers/`
- One mapper per entity, e.g., `UserMapper`
- Mappers provide `toDomain()` (Model → Entity) and `toModel()` (Entity → Model) methods
- Mappers handle field transformations (e.g., snake_case to camelCase)
- Mappers are called in Repository implementations

**Naming Convention**:
- Models: `<EntityName>Model` or `<EntityName>Dto`
- Entities: `<EntityName>Entity` or just `<EntityName>`
- Mappers: `<EntityName>Mapper`

---

### 2.6 Testing Strategy

**Unit Tests** (Domain & Application Layers):
- Test Use Cases with mocked Repositories
- Test BLoCs/Cubits with mocked Use Cases
- Located in `test/features/<feature>/domain/` and `test/features/<feature>/application/`
- No external dependencies; all are mocked

**Widget Tests** (Presentation Layer):
- Test individual widgets and screens
- Mock BLoCs/Cubits and test UI behavior
- Located in `test/features/<feature>/presentation/`
- Verify that UI renders correctly and responds to state changes

**Integration Tests**:
- Test complete user flows (e.g., login → profile view)
- Use a test API server or mock HTTP responses
- Located in `integration_test/`
- Run on actual device or emulator

**Data Layer Tests**:
- Test Repository implementations with mocked data sources
- Test mappers for correct transformations
- Located in `test/features/<feature>/data/`

**Testing Best Practices**:
- Use `mockito` for mocking dependencies
- Use `bloc_test` for testing BLoCs
- Aim for >80% code coverage in business logic layers
- Test error scenarios, not just happy paths

---

## 3. Laravel Backend Architecture

### 3.1 Modular Backend Approach

The Laravel backend uses a **Domain-Driven Design (DDD) inspired modular structure** where the application is organized into independent modules, each representing a distinct business domain.

**Module Structure**:
```
app/Modules/
├── Auth/
├── User/
├── Product/
├── Order/
└── [other modules]
```

**What Each Module Contains**:
- **Routes**: API routes specific to the module
- **Controllers**: Request handlers and response formatting
- **Requests**: Form requests for validation
- **Services/Actions**: Business logic and use cases
- **Models**: Eloquent models
- **Resources**: API response transformers
- **Policies**: Authorization logic
- **Events/Listeners**: Domain events and their handlers
- **Jobs**: Queued tasks
- **Migrations**: Database schema changes
- **Seeders**: Test data
- **Tests**: Unit and feature tests

**Ownership Rules**:

1. **Module-Specific Code**: Each module owns its routes, controllers, models, and business logic.

2. **Shared Code**: Shared utilities, base classes, and helpers are located in `app/Shared/` or `app/Core/`.

3. **Database Tables**: A module owns the database tables it manages. Other modules access data through the module's API (controllers) or through shared repositories.

4. **Cross-Module Communication**: Modules communicate through:
   - Events: One module fires an event, another listens
   - Service Interfaces: A module exposes a service interface that others can use
   - Repositories: A module exposes a repository interface for data access

5. **No Direct Model Access**: Other modules should not directly access another module's Eloquent models. Instead, they use repositories or API endpoints.

### 3.2 Folder Structure

#### Complete Laravel Folder Tree

```
laravel-app/
├── app/
│   ├── Modules/
│   │   ├── Auth/
│   │   │   ├── Routes/
│   │   │   │   └── api.php
│   │   │   ├── Controllers/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── LogoutController.php
│   │   │   │   └── RefreshTokenController.php
│   │   │   ├── Requests/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Services/
│   │   │   │   ├── AuthService.php
│   │   │   │   └── TokenService.php
│   │   │   ├── Models/
│   │   │   │   └── Token.php
│   │   │   ├── Resources/
│   │   │   │   ├── LoginResource.php
│   │   │   │   └── TokenResource.php
│   │   │   ├── Events/
│   │   │   │   ├── UserRegistered.php
│   │   │   │   └── UserLoggedIn.php
│   │   │   ├── Listeners/
│   │   │   │   └── SendWelcomeEmail.php
│   │   │   ├── Migrations/
│   │   │   │   └── [migration files]
│   │   │   ├── Seeders/
│   │   │   │   └── AuthSeeder.php
│   │   │   └── Tests/
│   │   │       ├── Unit/
│   │   │       │   └── AuthServiceTest.php
│   │   │       └── Feature/
│   │   │           ├── LoginTest.php
│   │   │           └── RegisterTest.php
│   │   │
│   │   ├── User/
│   │   │   ├── Routes/
│   │   │   │   └── api.php
│   │   │   ├── Controllers/
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Requests/
│   │   │   │   ├── UpdateProfileRequest.php
│   │   │   │   └── UpdateAvatarRequest.php
│   │   │   ├── Services/
│   │   │   │   ├── UserService.php
│   │   │   │   └── AvatarService.php
│   │   │   ├── Models/
│   │   │   │   └── User.php
│   │   │   ├── Resources/
│   │   │   │   ├── UserResource.php
│   │   │   │   └── ProfileResource.php
│   │   │   ├── Policies/
│   │   │   │   └── UserPolicy.php
│   │   │   ├── Events/
│   │   │   │   ├── UserUpdated.php
│   │   │   │   └── AvatarUploaded.php
│   │   │   ├── Migrations/
│   │   │   │   └── [migration files]
│   │   │   └── Tests/
│   │   │       ├── Unit/
│   │   │       │   └── UserServiceTest.php
│   │   │       └── Feature/
│   │   │           └── ProfileTest.php
│   │   │
│   │   └── [other modules follow same structure]
│   │
│   ├── Shared/
│   │   ├── Exceptions/
│   │   │   ├── ApiException.php
│   │   │   ├── ValidationException.php
│   │   │   └── UnauthorizedException.php
│   │   ├── Traits/
│   │   │   ├── ApiResponse.php
│   │   │   └── HasTimestamps.php
│   │   ├── Middleware/
│   │   │   ├── ApiAuthentication.php
│   │   │   └── RateLimiting.php
│   │   ├── Repositories/
│   │   │   └── [shared repository interfaces]
│   │   └── Helpers/
│   │       └── [utility functions]
│   │
│   ├── Core/
│   │   ├── Providers/
│   │   │   ├── AppServiceProvider.php
│   │   │   ├── RouteServiceProvider.php
│   │   │   └── EventServiceProvider.php
│   │   ├── Exceptions/
│   │   │   └── Handler.php
│   │   └── Middleware/
│   │       └── [global middleware]
│   │
│   └── Http/
│       ├── Middleware/
│       │   └── [HTTP middleware]
│       └── Kernel.php
│
├── routes/
│   ├── api.php
│   ├── web.php
│   └── channels.php
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── auth.php
│   └── [other configs]
│
├── tests/
│   ├── Unit/
│   ├── Feature/
│   └── TestCase.php
│
└── [other standard Laravel directories]
```

#### Module Template Folder Tree

```
app/Modules/<ModuleName>/
├── Routes/
│   └── api.php
├── Controllers/
│   └── <ModuleName>Controller.php
├── Requests/
│   └── <Action>Request.php
├── Services/
│   └── <ModuleName>Service.php
├── Models/
│   └── <Model>.php
├── Resources/
│   └── <Model>Resource.php
├── Policies/
│   └── <Model>Policy.php
├── Events/
│   └── <Event>.php
├── Listeners/
│   └── <Listener>.php
├── Jobs/
│   └── <Job>.php
├── Migrations/
│   └── [migration files]
├── Seeders/
│   └── <ModuleName>Seeder.php
└── Tests/
    ├── Unit/
    │   └── <ModuleName>ServiceTest.php
    └── Feature/
        └── <Feature>Test.php
```

### 3.3 Request Flow & Responsibilities

#### Routes (API v1 Versioning)

**Route Organization**:
- All API routes are prefixed with `/api/v1/`
- Each module defines its routes in `Modules/<ModuleName>/Routes/api.php`
- Routes are registered in the main `routes/api.php` file

**Route Naming Convention**:
```
POST   /api/v1/auth/register          → RegisterController@register
POST   /api/v1/auth/login             → LoginController@login
POST   /api/v1/auth/logout            → LogoutController@logout
POST   /api/v1/auth/refresh           → RefreshTokenController@refresh
GET    /api/v1/me                     → ProfileController@getProfile
PUT    /api/v1/me                     → ProfileController@updateProfile
GET    /api/v1/users/{id}             → UserController@show
PUT    /api/v1/users/{id}             → UserController@update
DELETE /api/v1/users/{id}             → UserController@destroy
```

**Versioning Strategy**:
- Current API version is `v1`
- When breaking changes are needed, create `v2` routes alongside `v1`
- Both versions can coexist temporarily for backward compatibility
- Deprecation notices are provided in response headers

#### Controllers (Thin Controllers Rule)

**Responsibilities**:
- Receive HTTP requests
- Validate input (delegate to Form Requests)
- Call appropriate Services/Actions
- Transform responses using Resources
- Return HTTP responses

**Rules**:
1. Controllers should be thin; business logic belongs in Services
2. Controllers should not directly query models; use Services or Repositories
3. Each controller method should handle one action
4. Controllers should not contain validation logic; use Form Requests
5. Controllers should not contain response formatting; use Resources

**Controller Example Structure**:
```
class LoginController
{
    public function __construct(private AuthService $authService) {}
    
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );
        
        return response()->json(
            new LoginResource($result),
            200
        );
    }
}
```

#### Form Requests (Validation)

**Responsibilities**:
- Validate incoming request data
- Authorize the request (optional)
- Transform/sanitize data

**Rules**:
1. One Form Request per action (e.g., `LoginRequest`, `RegisterRequest`)
2. Validation rules are defined in the `rules()` method
3. Custom validation messages are defined in the `messages()` method
4. Authorization is checked in the `authorize()` method
5. Data transformation happens in the `validated()` method if needed

**Form Request Example**:
```
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or check guest status
    }
    
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ];
    }
    
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
        ];
    }
}
```

#### Services/Actions (Business Logic)

**Responsibilities**:
- Implement business logic and use cases
- Orchestrate multiple models and repositories
- Handle transactions
- Fire domain events
- Validate business rules

**Rules**:
1. One Service per major feature or domain concept
2. Services should be focused and single-responsibility
3. Services should not directly access HTTP requests; receive data as parameters
4. Services should return domain objects or DTOs, not Eloquent models
5. Services should be testable with mocked dependencies

**Service Example**:
```
class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenService $tokenService
    ) {}
    
    public function login(string $email, string $password): LoginResult
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !Hash::check($password, $user->password)) {
            throw new UnauthorizedException('Invalid credentials');
        }
        
        $tokens = $this->tokenService->generateTokens($user);
        
        event(new UserLoggedIn($user));
        
        return new LoginResult($user, $tokens);
    }
}
```

#### Resources/Transformers (Response Shaping)

**Responsibilities**:
- Transform Eloquent models to JSON
- Control which fields are exposed
- Format data for API response
- Hide sensitive information

**Rules**:
1. One Resource per model or response type
2. Resources should inherit from `JsonResource`
3. Resources define the `toArray()` method with the response structure
4. Resources should not contain business logic
5. Resources can use other Resources for nested data

**Resource Example**:
```
class LoginResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user' => new UserResource($this->user),
            'tokens' => [
                'access_token' => $this->accessToken,
                'refresh_token' => $this->refreshToken,
                'expires_in' => $this->expiresIn,
            ],
        ];
    }
}
```

#### Policies/Gates (Authorization)

**Responsibilities**:
- Define authorization rules
- Check if a user can perform an action

**Rules**:
1. One Policy per model
2. Policies define methods for each action (e.g., `view()`, `update()`, `delete()`)
3. Policies should check user roles and permissions
4. Policies are used in controllers via `$this->authorize()`

**Policy Example**:
```
class UserPolicy
{
    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id || $user->isAdmin();
    }
    
    public function delete(User $user, User $target): bool
    {
        return $user->isAdmin();
    }
}
```

#### Jobs/Queues (Asynchronous Tasks)

**When to Use**:
- Sending emails
- Processing large files
- Calling external APIs
- Generating reports
- Any long-running task

**Rules**:
1. Jobs are dispatched from Services or Controllers
2. Jobs should be idempotent (safe to retry)
3. Jobs should have a timeout and retry count
4. Failed jobs are logged and can be retried

**Job Example**:
```
class SendWelcomeEmailJob implements ShouldQueue
{
    public function __construct(private User $user) {}
    
    public function handle(MailService $mailService): void
    {
        $mailService->sendWelcomeEmail($this->user);
    }
}
```

#### Events/Listeners (Domain Events)

**When to Use**:
- When an important business event occurs (e.g., user registered)
- To decouple modules (one module fires an event, another listens)
- To trigger side effects (e.g., send email, update cache)

**Rules**:
1. Events represent something that happened (past tense: `UserRegistered`, not `RegisterUser`)
2. Listeners react to events
3. One event can have multiple listeners
4. Listeners should be fast; use Jobs for slow operations
5. Listeners should not depend on each other

**Event/Listener Example**:
```
class UserRegistered
{
    public function __construct(public User $user) {}
}

class SendWelcomeEmailListener
{
    public function handle(UserRegistered $event): void
    {
        SendWelcomeEmailJob::dispatch($event->user);
    }
}
```

### 3.4 Authentication for Mobile: Sanctum

**Selection Rationale**: Laravel Sanctum is purpose-built for mobile and SPA authentication. It's simpler than Passport for mobile use cases, provides token-based authentication, and includes built-in CSRF protection for web clients.

**Token Issuance**:
- When a user logs in, the `AuthService` calls `Sanctum::token()` to generate a token
- The token is returned to the client in the response
- The client stores the token in secure storage

**Token Structure**:
- Sanctum generates opaque tokens (no JWT)
- Tokens are stored in the `personal_access_tokens` table
- Each token is associated with a user and has an expiration time
- Tokens can have scopes/abilities for fine-grained permissions

**Expiration & Rotation**:
- Access tokens expire after 24 hours (configurable)
- Refresh tokens are used to obtain new access tokens
- The client automatically refreshes the token before expiration
- If refresh fails, the user is logged out

**Token Refresh Flow**:
1. Client detects that access token is expired
2. Client sends refresh token to `/api/v1/auth/refresh`
3. Backend validates refresh token and issues new access token
4. Client stores new access token and continues

**Logout/Revoke Strategy**:
- When a user logs out, the backend revokes the token by deleting it from `personal_access_tokens`
- The client deletes the token from secure storage
- Subsequent requests without a token are rejected with 401 Unauthorized

**Security Practices**:
- Tokens are never logged or exposed in error messages
- Tokens are transmitted only over HTTPS
- Tokens are stored in secure storage on the client
- Token expiration is enforced on the backend
- Rate limiting is applied to token endpoints

### 3.5 API Response Contract (Standard)

All API responses follow a consistent envelope format to ensure predictability and ease of client integration.

#### Success Response Envelope

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "message": "Operation completed successfully"
}
```

**Fields**:
- `success` (boolean): Always `true` for successful responses
- `data` (object/array): The actual response payload
- `message` (string): Optional human-readable message

#### Error Response Envelope

```json
{
  "success": false,
  "error": {
    "code": "INVALID_CREDENTIALS",
    "message": "The provided credentials are invalid",
    "details": null
  }
}
```

**Fields**:
- `success` (boolean): Always `false` for error responses
- `error` (object):
  - `code` (string): Machine-readable error code
  - `message` (string): Human-readable error message
  - `details` (object/null): Additional error context (e.g., field-level validation errors)

#### Validation Error Response

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "email": ["Email is required", "Email must be a valid email address"],
      "password": ["Password must be at least 8 characters"]
    }
  }
}
```

**Fields**:
- `success` (boolean): Always `false`
- `error` (object):
  - `code` (string): Always `VALIDATION_ERROR`
  - `message` (string): Always "Validation failed"
  - `details` (object): Field-level error messages (field name → array of messages)

#### Pagination Response

```json
{
  "success": true,
  "data": [
    { "id": 1, "name": "Product 1" },
    { "id": 2, "name": "Product 2" }
  ],
  "meta": {
    "total": 100,
    "per_page": 10,
    "current_page": 1,
    "last_page": 10,
    "from": 1,
    "to": 10
  },
  "links": {
    "first": "https://api.example.com/api/v1/products?page=1",
    "last": "https://api.example.com/api/v1/products?page=10",
    "prev": null,
    "next": "https://api.example.com/api/v1/products?page=2"
  }
}
```

**Fields**:
- `success` (boolean): Always `true`
- `data` (array): Array of items
- `meta` (object): Pagination metadata
- `links` (object): Navigation links for pagination

### 3.6 Observability & Maintenance

#### Logging Strategy

**Log Levels**:
- **DEBUG**: Detailed information for debugging (e.g., SQL queries, variable values)
- **INFO**: General informational messages (e.g., user logged in, email sent)
- **WARNING**: Warning messages that don't prevent operation (e.g., slow query, deprecated API)
- **ERROR**: Error messages for recoverable errors (e.g., failed email, API timeout)
- **CRITICAL**: Critical errors that require immediate attention (e.g., database connection failed)

**What to Log**:
- User actions (login, logout, create, update, delete)
- External API calls (request/response, errors)
- Database operations (slow queries, connection errors)
- Business logic decisions (e.g., payment approved/rejected)
- Errors and exceptions

**What NOT to Log**:
- Passwords, tokens, or sensitive data
- Full request/response bodies (log only relevant fields)
- Personally identifiable information (PII) unless necessary

**Log Format**:
```
[2025-12-13 10:30:45] local.INFO: User logged in {"user_id": 1, "email": "john@example.com", "ip": "192.168.1.1"}
[2025-12-13 10:31:20] local.ERROR: Payment processing failed {"order_id": 123, "error": "Insufficient funds"}
```

#### Error Tracking

**Strategy**:
- Errors are logged to the application log
- Critical errors are sent to an error tracking service (e.g., Sentry)
- Error tracking includes stack traces, context, and user information
- Errors are monitored and alerts are sent for critical issues

**Implementation**:
- Use Laravel's exception handler to catch and log errors
- Send errors to Sentry via a middleware or service provider
- Include user context (user ID, email) in error reports
- Group similar errors for easier debugging

#### Environment Configuration

**Configuration Files**:
- `.env` file for local development (not committed)
- `.env.example` file as a template (committed)
- Environment-specific configs in `config/` directory

**Environment Variables**:
- `APP_ENV`: `local`, `testing`, `staging`, `production`
- `APP_DEBUG`: `true` (development) or `false` (production)
- `LOG_LEVEL`: Minimum log level to record
- `DB_*`: Database connection details
- `MAIL_*`: Email configuration
- `SANCTUM_*`: Sanctum configuration

**Environment-Specific Behavior**:
- Development: Debug mode on, detailed error messages, verbose logging
- Testing: In-memory database, mocked external services
- Staging: Production-like environment, full logging
- Production: Debug mode off, minimal error details, critical logging only

#### Migration & Seeding Strategy

**Migrations**:
- Each module has its own migrations in `Modules/<ModuleName>/Migrations/`
- Migrations are named with a timestamp and descriptive name
- Migrations are run in order: `php artisan migrate`
- Rollback is supported: `php artisan migrate:rollback`

**Seeders**:
- Each module has a seeder in `Modules/<ModuleName>/Seeders/`
- Seeders are run with: `php artisan db:seed`
- Seeders create test data for development and testing
- Seeders should be idempotent (safe to run multiple times)

**Module Setup Checklist**:
1. Create migration files for new tables
2. Create seeder for test data
3. Run migrations: `php artisan migrate`
4. Run seeders: `php artisan db:seed`
5. Verify data in database

---

## 4. Flutter ↔ Laravel Integration Contract

### 4.1 Endpoint Naming Conventions

**Resource-Based Naming**:
- Endpoints are named after resources, not actions
- Use nouns for resources, not verbs
- Examples: `/products`, `/users`, `/orders`

**HTTP Method Semantics**:
- `GET /resource`: Retrieve all resources (with pagination)
- `GET /resource/{id}`: Retrieve a specific resource
- `POST /resource`: Create a new resource
- `PUT /resource/{id}`: Replace a resource entirely
- `PATCH /resource/{id}`: Partially update a resource
- `DELETE /resource/{id}`: Delete a resource

**Nested Resources**:
- For related resources: `/resource/{id}/related`
- Example: `GET /users/1/orders` → Get all orders for user 1
- Avoid deep nesting (max 2 levels)

**Query Parameters**:
- `?page=1&per_page=10`: Pagination
- `?sort=name&order=asc`: Sorting
- `?filter[status]=active`: Filtering
- `?include=profile,orders`: Include related data

### 4.2 API Versioning Scheme

**Version in URL**:
- All endpoints are prefixed with `/api/v1/`
- Current version is `v1`
- Future versions will be `v2`, `v3`, etc.

**Backward Compatibility**:
- New features are added without breaking existing endpoints
- When breaking changes are necessary, a new version is created
- Old versions are supported for at least 6 months with deprecation warnings

**Deprecation Notice**:
- Deprecated endpoints include a `Deprecation` header
- Example: `Deprecation: true; sunset="2026-06-13T00:00:00Z"`

### 4.3 Field Casing Convention & Mapping Policy

**Backend (Laravel)**: Uses `snake_case` for database columns and API responses
- Example: `first_name`, `last_name`, `email_verified_at`

**Frontend (Flutter)**: Uses `camelCase` for Dart variables and JSON models
- Example: `firstName`, `lastName`, `emailVerifiedAt`

**Mapping Policy**:
- The API returns responses in `snake_case`
- The Flutter app automatically converts to `camelCase` in DTOs
- Mappers handle the conversion from DTO to Entity
- When sending requests, the Flutter app sends `camelCase` in JSON
- The Laravel backend automatically converts to `snake_case` via Form Requests

**Example Mapping**:
```
Backend Response (snake_case):
{
  "first_name": "John",
  "last_name": "Doe",
  "email_verified_at": "2025-12-13T10:00:00Z"
}

Flutter DTO (camelCase):
{
  "firstName": "John",
  "lastName": "Doe",
  "emailVerifiedAt": "2025-12-13T10:00:00Z"
}

Flutter Entity (camelCase):
{
  "firstName": "John",
  "lastName": "Doe",
  "emailVerifiedAt": "2025-12-13T10:00:00Z"
}
```

### 4.4 Timezone & Date Format Conventions

**Timezone**:
- All timestamps are stored and transmitted in **UTC**
- The client converts UTC to local timezone for display
- No timezone information is sent in the API; UTC is assumed

**Date Format**:
- All dates and times use **ISO 8601** format
- Format: `YYYY-MM-DDTHH:mm:ssZ` (with Z indicating UTC)
- Example: `2025-12-13T10:30:45Z`

**Date-Only Format**:
- For dates without time: `YYYY-MM-DD`
- Example: `2025-12-13`

**Duration Format**:
- For durations: ISO 8601 duration format
- Example: `PT1H30M` (1 hour 30 minutes)

### 4.5 File Upload Conventions

**Multipart Form Data**:
- File uploads use `multipart/form-data` content type
- The file is sent as a form field named `file`
- Additional metadata can be sent as additional form fields

**File Size Limits**:
- Maximum file size: 10 MB
- Supported image formats: JPEG, PNG, WebP
- Supported document formats: PDF, DOC, DOCX

**Upload Response**:
```json
{
  "success": true,
  "data": {
    "file_id": "abc123",
    "file_name": "avatar.jpg",
    "file_url": "https://api.example.com/files/abc123",
    "file_size": 102400,
    "uploaded_at": "2025-12-13T10:30:45Z"
  }
}
```

**Upload Example**:
```
POST /api/v1/me/avatar
Content-Type: multipart/form-data

file: <binary data>
```

### 4.6 Idempotency & Retry Guidelines

**Idempotent Operations**:
- `GET`, `DELETE` are naturally idempotent
- `POST` and `PUT` may not be idempotent; use idempotency keys for critical operations

**Idempotency Key**:
- For critical operations (e.g., payment), the client sends an `Idempotency-Key` header
- Format: UUID v4
- Example: `Idempotency-Key: 550e8400-e29b-41d4-a716-446655440000`
- The backend stores the key and returns the same response if the same key is sent again

**Retry Strategy**:
- Client retries failed requests with exponential backoff
- Retry delays: 1s, 2s, 4s, 8s (max 3 retries)
- Retry only on 5xx errors or network timeouts, not on 4xx errors
- Use idempotency keys for retries on `POST` and `PUT`

---

### 4.7 Sample Endpoint Specifications

#### POST /api/v1/auth/register

**Request**:
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!"
}
```

**Response (Success - 201)**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "email_verified_at": null,
      "created_at": "2025-12-13T10:30:45Z"
    },
    "tokens": {
      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "expires_in": 86400
    }
  },
  "message": "User registered successfully"
}
```

**Response (Validation Error - 422)**:
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "email": ["Email already exists"],
      "password": ["Password must be at least 8 characters"]
    }
  }
}
```

#### POST /api/v1/auth/login

**Request**:
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123!"
}
```

**Response (Success - 200)**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "email_verified_at": "2025-12-13T10:30:45Z",
      "created_at": "2025-12-13T10:30:45Z"
    },
    "tokens": {
      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
      "expires_in": 86400
    }
  },
  "message": "Login successful"
}
```

**Response (Invalid Credentials - 401)**:
```json
{
  "success": false,
  "error": {
    "code": "INVALID_CREDENTIALS",
    "message": "The provided credentials are invalid",
    "details": null
  }
}
```

#### GET /api/v1/me

**Request Headers**:
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

**Response (Success - 200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "email_verified_at": "2025-12-13T10:30:45Z",
    "avatar_url": "https://api.example.com/files/avatar123",
    "phone": "+1234567890",
    "bio": "Software developer",
    "created_at": "2025-12-13T10:30:45Z",
    "updated_at": "2025-12-13T10:30:45Z"
  },
  "message": "Profile retrieved successfully"
}
```

**Response (Unauthorized - 401)**:
```json
{
  "success": false,
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Unauthorized access",
    "details": null
  }
}
```

#### PUT /api/v1/me

**Request Headers**:
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json
```

**Request Body**:
```json
{
  "first_name": "John",
  "last_name": "Smith",
  "phone": "+1234567890",
  "bio": "Senior software developer"
}
```

**Response (Success - 200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Smith",
    "email": "john@example.com",
    "email_verified_at": "2025-12-13T10:30:45Z",
    "avatar_url": "https://api.example.com/files/avatar123",
    "phone": "+1234567890",
    "bio": "Senior software developer",
    "created_at": "2025-12-13T10:30:45Z",
    "updated_at": "2025-12-13T10:30:45Z"
  },
  "message": "Profile updated successfully"
}
```

**Response (Validation Error - 422)**:
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "phone": ["Phone must be a valid phone number"]
    }
  }
}
```

---

## 5. Cross-Cutting Conventions

### 5.1 Naming Conventions

#### Module Names

**Frontend (Flutter)**:
- Feature modules: lowercase, single word (e.g., `auth`, `profile`, `products`)
- If multi-word, use underscore (e.g., `user_management`)
- Core/Shared modules: lowercase (e.g., `core`, `shared`)

**Backend (Laravel)**:
- Module names: PascalCase (e.g., `Auth`, `User`, `Product`)
- Folder names: PascalCase to match namespace
- Database tables: snake_case, plural (e.g., `users`, `products`, `orders`)

#### Class Names

**Frontend (Flutter)**:
- Classes: PascalCase (e.g., `LoginBloc`, `UserEntity`, `AuthRepository`)
- Suffixes indicate purpose:
  - `*Bloc` or `*Cubit`: State management
  - `*Entity`: Domain objects
  - `*Model`: Data transfer objects
  - `*Repository`: Repository interfaces and implementations
  - `*UseCase`: Use case implementations
  - `*Service`: Services (in data layer)
  - `*DataSource`: Data sources
  - `*Mapper`: Mappers
  - `*Page`: Full-screen widgets
  - `*Widget`: Reusable components

**Backend (Laravel)**:
- Classes: PascalCase (e.g., `LoginController`, `AuthService`, `User`)
- Suffixes indicate purpose:
  - `*Controller`: Controllers
  - `*Service`: Services
  - `*Repository`: Repositories
  - `*Request`: Form requests
  - `*Resource`: API resources
  - `*Policy`: Authorization policies
  - `*Job`: Queued jobs
  - `*Listener`: Event listeners
  - `*Event`: Domain events

#### File Names

**Frontend (Flutter)**:
- Files: snake_case (e.g., `login_bloc.dart`, `user_entity.dart`)
- Match class name to file name (e.g., `LoginBloc` in `login_bloc.dart`)

**Backend (Laravel)**:
- Files: PascalCase (e.g., `LoginController.php`, `AuthService.php`)
- Match class name to file name (e.g., `LoginController` in `LoginController.php`)

### 5.2 Branching & Commit Conventions

**Branch Naming**:
- Feature branches: `feature/<feature-name>` (e.g., `feature/user-authentication`)
- Bug fix branches: `bugfix/<bug-name>` (e.g., `bugfix/login-validation`)
- Hotfix branches: `hotfix/<issue-name>` (e.g., `hotfix/payment-crash`)
- Release branches: `release/<version>` (e.g., `release/1.0.0`)

**Commit Message Format**:
```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types**:
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, missing semicolons, etc.)
- `refactor`: Code refactoring without changing functionality
- `perf`: Performance improvements
- `test`: Adding or updating tests
- `chore`: Build process, dependencies, etc.

**Examples**:
```
feat(auth): implement user registration

- Add registration form
- Validate email and password
- Send verification email

Closes #123
```

```
fix(profile): fix avatar upload error

The avatar upload was failing due to incorrect file size validation.

Closes #456
```

### 5.3 Lint & Format Conventions

**Frontend (Flutter)**:
- Use `flutter analyze` to check for issues
- Use `flutter format` to format code
- Follow Dart style guide: https://dart.dev/guides/language/effective-dart/style
- Use `pedantic` or `lints` package for lint rules
- Line length: 80 characters (soft), 120 characters (hard)

**Backend (Laravel)**:
- Use `php-cs-fixer` for code formatting
- Use `phpstan` for static analysis
- Follow PSR-12 coding standard
- Use `laravel-pint` (Laravel's code formatter)
- Line length: 100 characters

**Pre-Commit Hooks**:
- Run linter and formatter before committing
- Use `husky` (Node.js) or `pre-commit` (Python) for git hooks
- Prevent commits that don't pass linting

### 5.4 Module Creation Checklist

#### Frontend (Flutter) Module Checklist

- [ ] Create feature folder structure under `lib/features/<feature_name>/`
- [ ] Create domain layer:
  - [ ] Define entities in `domain/entities/`
  - [ ] Define repository interfaces in `domain/repositories/`
  - [ ] Implement use cases in `domain/usecases/`
- [ ] Create application layer:
  - [ ] Create BLoC/Cubit in `application/bloc/` or `application/cubit/`
  - [ ] Define events (if using BLoC)
  - [ ] Define states
- [ ] Create data layer:
  - [ ] Implement remote data source in `data/datasources/`
  - [ ] Implement local data source in `data/datasources/` (if needed)
  - [ ] Create models in `data/models/`
  - [ ] Implement repository in `data/repositories/`
  - [ ] Create mappers in `data/mappers/`
- [ ] Create presentation layer:
  - [ ] Create pages in `presentation/pages/`
  - [ ] Create widgets in `presentation/widgets/`
  - [ ] Define routes in `presentation/routes/`
- [ ] Register dependencies in `core/di/service_locator.dart`
- [ ] Register routes in main app router
- [ ] Write tests:
  - [ ] Unit tests for use cases
  - [ ] Unit tests for BLoC/Cubit
  - [ ] Widget tests for pages and widgets
- [ ] Update documentation

#### Backend (Laravel) Module Checklist

- [ ] Create module folder structure under `app/Modules/<ModuleName>/`
- [ ] Create routes in `Routes/api.php`
- [ ] Create controllers in `Controllers/`
- [ ] Create form requests in `Requests/`
- [ ] Create services in `Services/`
- [ ] Create models in `Models/`
- [ ] Create resources in `Resources/`
- [ ] Create policies in `Policies/` (if needed)
- [ ] Create events in `Events/` (if needed)
- [ ] Create listeners in `Listeners/` (if needed)
- [ ] Create jobs in `Jobs/` (if needed)
- [ ] Create migrations in `Migrations/`
- [ ] Create seeders in `Seeders/`
- [ ] Register module in `RouteServiceProvider`
- [ ] Write tests:
  - [ ] Unit tests for services
  - [ ] Feature tests for API endpoints
- [ ] Update documentation

---

## 6. Implementation Guidelines

### Getting Started

1. **Initialize the Projects**:
   - Set up Flutter project with the folder structure defined in Section 2.3
   - Set up Laravel project with the folder structure defined in Section 3.2

2. **Set Up Dependency Injection**:
   - Configure GetIt in Flutter (Section 2.4)
   - Register all services, repositories, and use cases

3. **Implement Core Infrastructure**:
   - Set up API client with interceptors (Section 2.5)
   - Set up secure storage for tokens (Section 2.5)
   - Set up state management (BLoC/Cubit) (Section 2.4)
   - Set up routing (GoRouter) (Section 2.4)

4. **Implement Authentication**:
   - Create Auth module in both Flutter and Laravel
   - Implement login, registration, and token refresh
   - Follow the API contract in Section 4.7

5. **Implement Additional Features**:
   - Create additional modules following the checklist in Section 5.4
   - Ensure each module follows the architecture defined in Sections 2 and 3

### Testing Strategy

- **Unit Tests**: Test business logic in isolation with mocked dependencies
- **Integration Tests**: Test complete user flows with real or mocked APIs
- **Widget Tests**: Test UI components and their interactions
- **API Tests**: Test API endpoints with various inputs and error scenarios

### Documentation

- Keep architecture documentation up-to-date as the project evolves
- Document any deviations from the standard architecture
- Maintain a changelog of architectural decisions

---

## Conclusion

This documentation provides a comprehensive blueprint for building a scalable, maintainable Flutter and Laravel application. By following these principles and conventions, teams can work efficiently, reduce bugs, and create a codebase that is easy to understand and modify.

The key to success is consistency: ensure that all team members follow the same patterns and conventions throughout the project.
