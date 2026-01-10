# [Feature Name] Module - Data Model

## Database Tables

### `table_name`

Description of the table.

**Schema**:
```sql
CREATE TABLE table_name (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    field_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_table_name_field (field_name)
);
```

**Fields**:
- `id` - Primary key
- `field_name` - Description
- `created_at`, `updated_at` - Timestamps

**Indexes**:
- Primary key on `id`
- Index on `field_name`

## Models

### [ModelName]

**Location**: `app/Modules/[ModuleName]/Models/[ModelName].php`

**Key Attributes**:
```php
protected $fillable = [
    'field1',
    'field2',
];
```

**Relationships**:
- `relationshipName()` - Description

## Relationships

### [Model] → [RelatedModel]

**Type**: Has Many / Belongs To  
**Model**: `RelatedModel`  
**Foreign Key**: `foreign_key_id`  
**Relationship**: `$model->relationshipName()`

## Data Flow

### [Operation] Data Flow

```
1. Request Data
   ↓
2. Validation
   ↓
3. Service Processing
   ↓
4. Database Update
```

## Migrations

### [MigrationName]

**Location**: `database/migrations/YYYY_MM_DD_HHMMSS_description.php`

Description of migration.

## Data Integrity

### Constraints

1. **Constraint 1**: Description
2. **Constraint 2**: Description

## Indexes for Performance

1. Index on `field_name` for fast lookups

## Security Considerations

1. **Consideration 1**: Description

