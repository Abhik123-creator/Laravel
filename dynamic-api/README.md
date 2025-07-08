# Dynamic API System

A Laravel-based dynamic API system that allows you to create flexible content types and store data dynamically through a REST API. Built with Laravel 11, Filament 3 for admin interface, and SQLite for database.

## 🚀 Features

- **Dynamic Forms**: Create flexible form structures on the fly
- **Field Management**: Define custom fields for each form with validation
- **REST API**: Automatically generated API endpoints for each form
- **Admin Interface**: Beautiful Filament-powered admin panel
- **Dynamic Validation**: Automatic validation based on field definitions
- **Type Safety**: Support for multiple field types (string, integer, boolean, date, email, text)

## 📋 Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Reference](#api-reference)
- [Admin Interface](#admin-interface)
- [Database Schema](#database-schema)
- [Examples](#examples)
- [Troubleshooting](#troubleshooting)

## 🛠️ Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js (for frontend assets)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd dynamic-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   ```

5. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## ⚙️ Configuration

### Database Configuration

The system uses SQLite by default. The database configuration is in `config/database.php`:

```php
'default' => env('DB_CONNECTION', 'sqlite'),
'connections' => [
    'sqlite' => [
        'driver' => 'sqlite',
        'database' => database_path('database.sqlite'),
        // ...
    ],
],
```

### Authentication

Default admin credentials:
- **Email**: admin@ad.com
- **Password**: password123

**⚠️ Important**: Change these credentials in production!

## 🎯 Usage

### Step 1: Create Forms

1. Access the admin panel at `http://localhost:8000/admin`
2. Navigate to "Forms"
3. Click "New" to create a form
4. Enter a name (e.g., "Student") - the slug will auto-generate
5. Save the form

### Step 2: Define Fields

1. Click "Edit" on your newly created form
2. Navigate to the "Fields" tab
3. Add fields by clicking "New Field"
4. Configure each field:
   - **Name**: Machine name (e.g., "age", "email")
   - **Label**: Human-readable label (e.g., "Age", "Email Address")
   - **Type**: Field type for validation

### Step 3: Use the API

Once you have forms with fields, you can use the API to store data:

```bash
POST /api/content/{slug}
Content-Type: application/json

{
    "field_name": "value",
    "another_field": "another_value"
}
```

## 📡 API Reference

### Base URL
```
http://localhost:8000/api
```

### Endpoints

#### Store Content Entry
```http
POST /api/content/{slug}
```

**Parameters:**
- `{slug}`: The slug of the form

**Request Body:**
```json
{
    "field_name": "value",
    "field_name_2": "value2"
}
```

**Response (Success):**
```json
{
    "message": "Entry saved successfully.",
    "id": 1
}
```

**Response (Validation Error):**
```json
{
    "errors": {
        "field_name": ["The field name is required."],
        "field_name_2": ["The field name 2 must be an integer."]
    }
}
```

### Field Types and Validation

| Field Type | Validation Rule | Description |
|------------|----------------|-------------|
| `string` | `required\|string` | Text input |
| `integer` | `required\|integer` | Numeric input |
| `boolean` | `required\|boolean` | True/false values |
| `date` | `required\|date` | Date format (YYYY-MM-DD) |
| `email` | `required\|email` | Valid email address |
| `text` | `required\|string` | Long text input |

## 🖥️ Admin Interface

### Forms Management

**URL**: `http://localhost:8000/admin/content-types`

**Features:**
- Create, edit, and delete forms
- Auto-generate slugs from names
- View creation and modification dates

### Fields Management

**URL**: `http://localhost:8000/admin/content-types/{id}/edit` (Fields tab)

**Features:**
- Add/edit/delete fields for each form
- Color-coded field types
- Searchable and sortable fields
- Copy field names for API reference

## 🗄️ Database Schema

### Forms Table
```sql
CREATE TABLE content_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Field Definitions Table
```sql
CREATE TABLE field_definitions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_type_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    label VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (content_type_id) REFERENCES content_types(id) ON DELETE CASCADE
);
```

### Content Entries Table
```sql
CREATE TABLE content_entries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_type_id INTEGER NOT NULL,
    data JSON NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (content_type_id) REFERENCES content_types(id) ON DELETE CASCADE
);
```

## 💡 Examples

### Example 1: Student Management System

1. **Create Form**
   - Name: "Student"
   - Slug: "student"

2. **Add Fields**
   - `name` (String) - "Student Name"
   - `age` (Integer) - "Age"
   - `email` (Email) - "Email Address"
   - `is_active` (Boolean) - "Active Status"

3. **API Usage**
   ```bash
   curl -X POST http://localhost:8000/api/content/student \
     -H "Content-Type: application/json" \
     -d '{
       "name": "John Doe",
       "age": 20,
       "email": "john@example.com",
       "is_active": true
     }'
   ```

### Example 2: Product Catalog

1. **Create Form**
   - Name: "Product"
   - Slug: "product"

2. **Add Fields**
   - `name` (String) - "Product Name"
   - `price` (Integer) - "Price"
   - `description` (Text) - "Description"
   - `launch_date` (Date) - "Launch Date"
   - `is_featured` (Boolean) - "Featured Product"

3. **API Usage**
   ```bash
   curl -X POST http://localhost:8000/api/content/product \
     -H "Content-Type: application/json" \
     -d '{
       "name": "Awesome Product",
       "price": 2999,
       "description": "This is an amazing product that will change your life.",
       "launch_date": "2025-12-01",
       "is_featured": true
     }'
   ```

## 🔧 Development

### Project Structure

```
app/
├── Http/Controllers/Api/
│   └── ContentEntryController.php    # API endpoint controller
├── Models/
│   ├── ContentType.php               # Form model
│   ├── FieldDefinition.php           # Field definition model
│   └── ContentEntry.php              # Content entry model
├── Filament/Resources/
│   ├── ContentTypeResource.php       # Admin interface for forms
│   └── ContentTypeResource/RelationManagers/
│       └── FieldsRelationManager.php # Fields management interface
database/
├── migrations/
│   ├── *_create_content_types_table.php
│   ├── *_create_field_definitions_table.php
│   └── *_create_content_entries_table.php
routes/
├── api.php                           # API routes
└── web.php                           # Web routes
```

### Key Models

#### ContentType Model
```php
class ContentType extends Model
{
    protected $fillable = ['name', 'slug'];
    
    public function fields()
    {
        return $this->hasMany(FieldDefinition::class);
    }
    
    public function entries()
    {
        return $this->hasMany(ContentEntry::class);
    }
}
```

#### FieldDefinition Model
```php
class FieldDefinition extends Model
{
    protected $fillable = ['content_type_id', 'name', 'label', 'type'];
    
    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }
}
```

### Adding New Field Types

To add a new field type:

1. **Update the field type options** in `FieldsRelationManager.php`:
   ```php
   Forms\Components\Select::make('type')
       ->options([
           'string' => 'String',
           'integer' => 'Integer',
           'boolean' => 'Boolean',
           'date' => 'Date',
           'text' => 'Text Area',
           'email' => 'Email',
           'url' => 'URL',        // New field type
       ])
   ```

2. **Add validation rules** in `ContentEntryController.php`:
   ```php
   switch ($field->type) {
       case 'url':
           $rules[$field->name] = 'required|url';
           break;
       // ... other cases
   }
   ```

3. **Update the color scheme** in `FieldsRelationManager.php`:
   ```php
   ->color(fn (string $state): string => match ($state) {
       'url' => 'purple',
       // ... other colors
   })
   ```

## 🐛 Troubleshooting

### Common Issues

#### 1. 419 Page Expired Error
**Problem**: Getting 419 error when accessing admin panel.

**Solution**:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### 2. Field 'name' doesn't have a default value
**Problem**: Database error when creating forms.

**Solution**: Make sure the ContentTypeResource form has proper field definitions.

#### Form Not Found
**Problem**: API returns 404 for existing forms.

**Solution**: Check that the slug matches exactly (case-sensitive).

#### 4. Permission Denied
**Problem**: Cannot access admin panel.

**Solution**: Make sure user has proper permissions:
```php
public function canAccessPanel(\Filament\Panel $panel): bool
{
    return true; // or your custom logic
}
```

### Debug Mode

Enable debug mode in `.env`:
```
APP_DEBUG=true
```

### Logs

Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)
- [JSON API Specification](https://jsonapi.org/)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [Troubleshooting](#troubleshooting) section
2. Review the [Examples](#examples)
3. Check Laravel and Filament documentation
4. Create an issue in the repository

---

**Happy coding!** 🚀

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
