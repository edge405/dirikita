# Documentation

## Rules (Source of Truth)

The authoritative engineering standards:

- **[Backend Rules](./rules/backend-rules.md)** - Backend architecture, patterns, and standards
- **[Documentation Rules](./rules/docs-rules.md)** - How to write and maintain documentation
- **[API Rules](./rules/api-rules.md)** - API conventions and standards

## Features

Each feature/module has comprehensive documentation in `/docs/features/<feature-name>/`:

- **Auth Module** (`/docs/features/auth/`) - Authentication, registration, login, password reset, email verification, and two-factor authentication
- **User Module** (`/docs/features/user/`) - User profile management, password updates, and two-factor authentication settings
- **Template** (`/docs/features/_template/`) - Template for new features

Each feature folder contains:
- `README.md` - Overview and summary
- `api.md` - Complete API documentation
- `business-logic.md` - Services, flows, and domain rules
- `data-model.md` - Database schema and relationships
- `testing.md` - Test coverage and examples

Feature docs are mandatory when:
- Adding a new module
- Changing behavior of existing feature
- Adding/changing endpoints
- Changing data model

## Quick Links

- [Backend Rules](./rules/backend-rules.md) - Start here for development standards
- [Feature Template](./features/_template/README.md) - Template for new features
- [API Rules](./rules/api-rules.md) - API endpoint conventions
