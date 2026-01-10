# [Feature Name] Module - Business Logic

## Domain Rules

### Business Invariants

1. **Rule 1**: Description
2. **Rule 2**: Description

### Validation Rules

- Field validation requirements
- Business logic constraints

## Services

### [ServiceName]

**Location**: `app/Modules/[ModuleName]/Services/[ServiceName].php`

**Responsibilities**:
- Responsibility 1
- Responsibility 2

**Dependencies**:
- Dependency 1

#### Methods

##### `methodName(Type $param): ReturnType`

Description of what this method does.

**Flow**:
1. Step 1
2. Step 2
3. Step 3

**Parameters**:
- `$param`: Description

**Returns**: Description

**Throws**:
- `ExceptionType` if condition

## Flows

### [Key Operation] Flow

```
1. Step 1: Description
   ↓
2. Step 2: Description
   ↓
3. Step 3: Description
```

## Controllers

### [ControllerName]

**Location**: `app/Modules/[ModuleName]/Controllers/[ControllerName].php`

**Responsibilities**:
- Responsibility 1
- Responsibility 2

**Methods**:
- `methodName()`: Description

## Resources

### [ResourceName]

**Location**: `app/Modules/[ModuleName]/Resources/[ResourceName].php`

**Transforms**: Description

**Structure**:
```json
{
  "field": "value"
}
```

## Events

### [EventName]

**Location**: `app/Modules/[ModuleName]/Events/[EventName].php`

**Fired When**: Description

**Event Data**:
```php
public Type $property
```

**Listeners**:
- `ListenerName` - Description

## Edge Cases

### [Edge Case 1]
- **Scenario**: Description
- **Handling**: How it's handled

### [Edge Case 2]
- **Scenario**: Description
- **Handling**: How it's handled

## Security Considerations

1. **Consideration 1**: Description
2. **Consideration 2**: Description

