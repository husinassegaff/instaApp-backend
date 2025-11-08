# InstaApp Backend

A Laravel-based Instagram-like social media application with comprehensive API and web interfaces. Built with SOLID principles and modern Laravel practices.

## Tech Stack

### Backend
- PHP 8.2
- Laravel 12.0
- Laravel Sanctum 4.2 (API Authentication)
- MySQL (Database)
- PHPUnit 11.5.3 (Testing)

### Frontend
- Tailwind CSS 4.1.17
- Alpine.js 3.15.1
- Vite 7.0.7
- Axios 1.11.0

### Development Tools
- Laravel Sail 1.41 (Docker environment)
- Laravel Tinker 2.10.1 (REPL)
- Laravel Pint 1.24.0 (Code formatting)
- Laravel Pail 1.2.2 (Log viewing)

## Features

### Authentication
- User registration with email verification
- Dual authentication system (API token-based via Sanctum, Web session-based)
- Login/logout functionality
- Email verification with resend capability
- Password hashing with bcrypt

### Post Management
- Create posts with image upload (base64 encoding)
- Edit and delete own posts
- View all posts with pagination
- Image storage in database (MVP implementation)
- Caption support (max 2200 characters)
- Authorization via Laravel Policies

### Like System
- Like/unlike posts
- Unique constraint (one like per user per post)
- Duplicate prevention with error handling
- Optimistic UI support

### Comment System
- Create comments on posts
- Edit and delete own comments
- Maximum 500 characters per comment
- Owner-only modifications via CommentPolicy

### Activity Logging
- Comprehensive logging of all user actions
- Categories: auth, post, like, comment
- Polymorphic relationships for flexible logging
- IP address and user agent tracking
- Custom properties storage (JSON)
- Timeline view with pagination

### User Profiles
- Profile image upload (base64)
- Username, bio, and email management
- View user posts
- Activity history

## SOLID Principles Implementation

### Single Responsibility Principle (SRP)

Each class has one clearly defined responsibility:

**Service Layer** - Each service handles only one domain:
- `AuthService` - Authentication business logic only
- `PostService` - Post business logic only
- `LikeService` - Like business logic only
- `CommentService` - Comment business logic only
- `ActivityLoggerService` - Activity logging only

**Form Requests** - Validation separated from controllers:
- `StorePostRequest`, `UpdatePostRequest` - Post validation
- `RegisterRequest`, `LoginRequest` - Auth validation
- `StoreCommentRequest`, `UpdateCommentRequest` - Comment validation

**Policies** - Authorization separated from controllers:
- `PostPolicy` - Post authorization (update, delete)
- `CommentPolicy` - Comment authorization (update, delete)

**Controllers** - HTTP layer only, no business logic:
- Controllers handle HTTP request/response
- Delegate business logic to services
- Use FormRequests for validation
- Use Policies for authorization

### Dependency Inversion Principle (DIP)

High-level modules depend on abstractions, not concrete implementations:

**Constructor Injection** throughout the application:
```php
// Controllers depend on services
PostController -> PostService (injected)
AuthController -> AuthService (injected)

// Services depend on other services
PostService -> ActivityLoggerService (injected)
AuthService -> ActivityLoggerService (injected)
```

**Service Layer Architecture:**
- All dependencies injected via constructor
- Laravel's service container manages dependencies
- No manual instantiation of dependencies
- Easy to swap implementations for testing

### Open/Closed Principle (OCP)

Classes are open for extension but closed for modification:

**Extensible Services:**
- `ActivityLoggerService` can be extended for custom log types without modification
- `PostService` can be extended for image processing without modifying core logic

**Polymorphic Relationships:**
- `ActivityLog::subject()` uses morphTo() to log any model type
- Can add new models without changing ActivityLog

### Liskov Substitution Principle (LSP)

Derived classes can substitute base classes without breaking functionality:

**Consistent Service Interfaces:**
- All services follow the same pattern
- Accept standard parameters (User, array, Model)
- Return standard types (Model, Collection, bool)
- Throw same exception types

**Interchangeable Authentication Services:**
- `AuthService` (API token authentication)
- `WebAuthService` (session-based authentication)
- Both provide: register(), login(), logout()
- Can be swapped based on context

### Interface Segregation Principle (ISP)

Clients are not forced to depend on interfaces they don't use:

**Focused Service Methods:**
- Services provide only methods clients need
- No unnecessary methods forced on clients
- Example: `LikeService` provides only `likePost()`, `unlikePost()`, `isLikedByUser()`

**Specific Form Requests:**
- `StorePostRequest` - Only create validation rules
- `UpdatePostRequest` - Only update validation rules
- Clients implement only what they need

## Architecture

### Layered Architecture

```
HTTP Request
    ↓
Controller (HTTP Layer)
    ↓ [Dependency Injection]
Service (Business Logic Layer)
    ↓ [Dependency Injection]
ActivityLogger (Cross-cutting Concern)
    ↓
Model (Data Layer)
    ↓
Database
```

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/              # API endpoints (Sanctum)
│   │   └── Web/              # Web endpoints (Session)
│   ├── Requests/             # Form validation
│   └── Middleware/
├── Models/                   # Eloquent models
├── Services/                 # Business logic layer
└── Policies/                 # Authorization logic

database/
└── migrations/               # Database schema

resources/
├── views/                    # Blade templates
├── css/
└── js/

routes/
├── api.php                   # API routes
└── web.php                   # Web routes
```

### Key Patterns

- Service Layer Pattern
- Repository Pattern (via Eloquent)
- Dependency Injection
- Policy Pattern
- Form Request Pattern
- Strategy Pattern (dual auth systems)

## Database Schema

### Users
- id, name, username (unique), email (unique), email_verified_at
- password, bio, profile_image (base64)

### Posts
- id, user_id, caption, image (base64)

### Likes
- id, user_id, post_id
- Unique constraint: (user_id, post_id)

### Comments
- id, user_id, post_id, content

### Activity Logs
- id, user_id, log_name, description
- subject_type, subject_id (polymorphic)
- properties (JSON), ip_address, user_agent

## API Endpoints

### Authentication
```
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/user
GET    /api/email/verify/{id}/{hash}
POST   /api/email/verification-notification
```

### Posts
```
GET    /api/posts
POST   /api/posts
GET    /api/posts/{post}
PUT    /api/posts/{post}
DELETE /api/posts/{post}
```

### Likes
```
POST   /api/posts/{post}/like
DELETE /api/posts/{post}/unlike
```

### Comments
```
GET    /api/posts/{post}/comments
POST   /api/posts/{post}/comments
PUT    /api/comments/{comment}
DELETE /api/comments/{comment}
```

### Activity Logs
```
GET    /api/activity-logs
```

## Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL

### Installation

1. Clone the repository
```bash
git clone <repository-url>
cd instaApp-backend
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Configure email service (Brevo SMTP)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_username
MAIL_PASSWORD=your_brevo_password
```

6. Run migrations
```bash
php artisan migrate
```

7. Start development servers
```bash
npm run dev
php artisan serve
```

### Using Docker (Laravel Sail)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

## Development

### Code Formatting
```bash
./vendor/bin/pint
```

### Running Tests
```bash
php artisan test
```

### Viewing Logs
```bash
php artisan pail
```

## License

This project is proprietary software.
