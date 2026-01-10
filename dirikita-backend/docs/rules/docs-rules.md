# Documentation Rules

## When Docs Are Required

Documentation is **mandatory** when:

- Adding a new feature/module
- Changing behavior of an existing feature
- Adding/changing endpoints
- Changing the data model or migrations
- Adding async/queue behavior
- Changing authorization rules
- Adding new events or listeners

## Where Docs Live

- **Feature docs**: `/docs/features/<feature>/` (folder with multiple files)
  - `README.md` - Overview and summary
  - `api.md` - API endpoints documentation
  - `business-logic.md` - Services, flows, domain rules
  - `data-model.md` - Database schema and relationships
  - `testing.md` - Test coverage and examples
- **Templates**: `/docs/features/_template/` (folder with template files)
- **Global standards**: `/docs/rules/*.md`
- **Main index**: `/docs/README.md`

## Feature Doc Structure

Every feature must have a folder in `/docs/features/<feature-name>/` with these files:

1. **README.md** - Overview, summary, requirements, module structure, quick links
2. **api.md** - Complete API endpoint documentation with examples
3. **business-logic.md** - Services, flows, domain rules, controllers, resources, events
4. **data-model.md** - Database tables, models, relationships, migrations
5. **testing.md** - Test coverage, unit tests, feature tests, examples

Each file should be comprehensive and cover all aspects of that topic.

## Documentation Standards

### Writing Style
- **Clear and Concise**: Use simple language
- **Active Voice**: "The system validates..." not "Validation is performed..."
- **Step-by-Step**: Break complex processes into steps
- **Examples**: Include real code examples

### Code Examples
- **Complete**: Show full, runnable examples
- **Context**: Include necessary imports and setup
- **Success & Error**: Show both success and error cases
- **Comments**: Add comments for non-obvious parts

### Structure
- **Headings**: Use clear heading hierarchy (H1 for title, H2 for main sections)
- **Tables**: Use tables for structured data
- **Lists**: Use lists for multiple items
- **Links**: Cross-reference related documentation

## Updating Documentation

### When to Update
- Immediately when code changes
- During code reviews
- Before major releases
- When onboarding new developers

### How to Update
1. Locate the relevant feature doc in `/docs/features/`
2. Update the affected sections
3. Verify examples still work
4. Update related docs if needed
5. Review for clarity

## Documentation Review

- **Code Reviews**: Documentation updates should be reviewed with code
- **Completeness**: Ensure all required sections are present
- **Accuracy**: Verify examples and code snippets are correct
- **Clarity**: Check that explanations are clear

## Template Usage

When creating new feature documentation:

1. Copy `/docs/features/_template/` folder
2. Rename to match feature name (e.g., `product/`)
3. Fill in all files:
   - `README.md` - Overview and summary
   - `api.md` - All endpoints
   - `business-logic.md` - Services and flows
   - `data-model.md` - Database schema
   - `testing.md` - Test documentation
4. Update `/docs/README.md` to include new feature
5. Follow the template structure exactly

## Maintenance

- **Keep Current**: Documentation must match code
- **Version Control**: All docs are version controlled
- **Regular Review**: Review docs during sprint planning
- **Feedback**: Incorporate feedback from developers

