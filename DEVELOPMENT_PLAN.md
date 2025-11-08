# INSTAAPP API - MVP DEVELOPMENT PLAN

> **Technical Test - Software Engineer Interview**
>
> **Project Type:** Full REST API (Laravel 12)
>
> **Target:** Complete MVP dengan implementasi SOLID principles & PHPUnit testing

---

## PROJECT OVERVIEW

### Features Scope
- ✅ Register & Login (dengan email verification)
- ✅ Posting text + gambar (base64 di MySQL)
- ✅ Like & Comment pada post
- ✅ Authentication pengguna (Laravel Sanctum)
- ✅ Authorization/hak akses (owner-only untuk update/delete)
- ✅ Activity logging untuk semua action
- ✅ PHPUnit testing (Feature + Unit tests)
- ✅ SOLID principles implementation

### Tech Stack
- **Framework:** Laravel 12
- **Database:** MySQL (port 3307 local dev)
- **Authentication:** Laravel Sanctum (API tokens)
- **Email Service:** Brevo SMTP (email verification)
- **Image Storage:** Base64 di MySQL (temporary MVP solution)
- **Testing:** PHPUnit 11.5.3
- **Code Style:** Laravel Pint

---

## 📊 PROGRESS SUMMARY

**Last Updated:** Nov 8, 2025

| Phase | Status | Completion Date |
|-------|--------|-----------------|
| Phase 1: Environment & Auth Setup | ✅ COMPLETED | Nov 8, 2025 |
| Phase 2: Database Migrations | ✅ COMPLETED | Nov 8, 2025 |
| Phase 3: Models & Relationships | ✅ COMPLETED | Nov 8, 2025 |
| Phase 4: API Routes | ✅ COMPLETED | Nov 8, 2025 |
| Phase 5: Controllers | ✅ COMPLETED | Nov 8, 2025 |
| Phase 6: Services Layer | ✅ COMPLETED | Nov 8, 2025 |
| Phase 7: Form Requests | ⏳ PENDING | - |
| Phase 8: Middleware | ⏳ PENDING | - |
| Phase 9: Policies | ⏳ PENDING | - |
| Phase 10: API Resources | ⏳ PENDING | - |
| Phase 11: Testing | ⏳ PENDING | - |
| Phase 12: Documentation | ⏳ PENDING | - |
| Phase 13: QA | ⏳ PENDING | - |
| Phase 14: Final Prep | ⏳ PENDING | - |

**Overall Progress:** 6/14 phases (43%) ✅

---

## PHASE 1: ENVIRONMENT & AUTHENTICATION SETUP ✅ **COMPLETED**

### 1.1 Environment Configuration
- [x] Create `.env` file dari `.env.example`
- [x] Configure MySQL database:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3307
  DB_DATABASE=instaApp
  DB_USERNAME=root
  DB_PASSWORD=husin123
  ```
- [ ] Configure Brevo SMTP untuk email verification:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp-relay.brevo.com
  MAIL_PORT=587
  MAIL_USERNAME=9b12b3001@smtp-brevo.com
  MAIL_PASSWORD=LOwCJc5N4B6rG29v
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=husinassegaff15@gmail.com
  MAIL_FROM_NAME="${APP_NAME}"
  ```
- [x] Set `APP_URL` dan `FRONTEND_URL` (untuk email verification redirect)
- [x] Generate application key: `php artisan key:generate`

### 1.2 Install Laravel Sanctum (API Authentication)
- [x] Install Sanctum: `composer require laravel/sanctum`
- [x] Publish config: `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
- [x] Run migrations: `php artisan migrate`
- [x] Configure Sanctum di `config/auth.php`:
  ```php
  'guards' => [
      'api' => [
          'driver' => 'sanctum',
          'provider' => 'users',
      ],
  ],
  ```

### 1.3 User Model Email Verification Setup
- [x] Update `app/Models/User.php`:
  - Implement `MustVerifyEmail` interface
  - Add `username`, `bio`, `profile_image` ke `$fillable`
  - Add HasApiTokens trait (Sanctum)

**Estimasi Waktu:** 30 menit ✅ **Completed: Nov 8, 2025**

---

## PHASE 2: DATABASE MIGRATIONS ✅ **COMPLETED**

> **Note:** Foreign key constraints removed - handled at model level only (no database FK constraints)

### 2.1 Update Users Table Migration
- [x] File: `database/migrations/0001_01_01_000000_create_users_table.php`
- [x] Add columns:
  - `username` (string, unique, nullable) - untuk future use
  - `bio` (text, nullable)
  - `profile_image` (longText, nullable) - base64 storage
- [x] Keep existing: `email_verified_at` for email verification

### 2.2 Create Posts Migration
- [x] Command: `php artisan make:migration create_posts_table`
- [x] File: `database/migrations/2025_11_07_100000_create_posts_table.php`
- [x] Structure:
  ```php
  $table->id();
  $table->foreignId('user_id')->constrained()->cascadeOnDelete();
  $table->text('caption')->nullable();
  $table->longText('image'); // base64 image storage
  $table->timestamps();

  $table->index(['user_id', 'created_at']);
  ```

### 2.3 Create Likes Migration
- [x] Command: `php artisan make:migration create_likes_table`
- [x] File: `database/migrations/2025_11_07_110000_create_likes_table.php`
- [x] Structure:
  ```php
  $table->id();
  $table->foreignId('user_id')->constrained()->cascadeOnDelete();
  $table->foreignId('post_id')->constrained()->cascadeOnDelete();
  $table->timestamps();

  // Prevent duplicate likes
  $table->unique(['user_id', 'post_id']);
  $table->index('post_id');
  ```

### 2.4 Create Comments Migration
- [x] Command: `php artisan make:migration create_comments_table`
- [x] File: `database/migrations/2025_11_08_100000_create_comments_table.php`
- [x] Structure:
  ```php
  $table->id();
  $table->foreignId('user_id')->constrained()->cascadeOnDelete();
  $table->foreignId('post_id')->constrained()->cascadeOnDelete();
  $table->text('content');
  $table->timestamps();

  $table->index(['post_id', 'created_at']);
  ```

### 2.5 Create Activity Logs Migration
- [x] Command: `php artisan make:migration create_activity_logs_table`
- [x] File: `database/migrations/2025_11_08_110000_create_activity_logs_table.php`
- [x] Structure:
  ```php
  $table->id();
  $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
  $table->string('log_name'); // 'auth', 'post', 'like', 'comment'
  $table->text('description');
  $table->string('subject_type')->nullable(); // Polymorphic
  $table->unsignedBigInteger('subject_id')->nullable(); // Polymorphic
  $table->json('properties')->nullable(); // Additional data
  $table->string('ip_address')->nullable();
  $table->text('user_agent')->nullable();
  $table->timestamps();

  $table->index(['user_id', 'created_at']);
  $table->index('log_name');
  ```

### 2.6 Run Migrations
- [x] Execute: `php artisan migrate`
- [x] Verify tables di MySQL: `SHOW TABLES;`

**Estimasi Waktu:** 45 menit ✅ **Completed: Nov 8, 2025**

---

## PHASE 3: ELOQUENT MODELS & RELATIONSHIPS ✅ **COMPLETED**

### 3.1 Create Post Model
- [x] Command: `php artisan make:model Post`
- [x] File: `app/Models/Post.php`
- [x] Implementation:
  ```php
  protected $fillable = ['user_id', 'caption', 'image'];

  protected $casts = [
      'created_at' => 'datetime',
      'updated_at' => 'datetime',
  ];

  // Relationships
  public function user() { return $this->belongsTo(User::class); }
  public function likes() { return $this->hasMany(Like::class); }
  public function comments() { return $this->hasMany(Comment::class); }

  // Accessors
  protected function likesCount(): Attribute { ... }
  protected function commentsCount(): Attribute { ... }
  ```

### 3.2 Create Like Model
- [x] Command: `php artisan make:model Like`
- [x] File: `app/Models/Like.php`
- [x] Implementation:
  ```php
  protected $fillable = ['user_id', 'post_id'];

  public function user() { return $this->belongsTo(User::class); }
  public function post() { return $this->belongsTo(Post::class); }
  ```

### 3.3 Create Comment Model
- [x] Command: `php artisan make:model Comment`
- [x] File: `app/Models/Comment.php`
- [x] Implementation:
  ```php
  protected $fillable = ['user_id', 'post_id', 'content'];

  protected $casts = ['created_at' => 'datetime'];

  public function user() { return $this->belongsTo(User::class); }
  public function post() { return $this->belongsTo(Post::class); }
  ```

### 3.4 Create ActivityLog Model
- [x] Command: `php artisan make:model ActivityLog`
- [x] File: `app/Models/ActivityLog.php`
- [x] Implementation:
  ```php
  protected $fillable = [
      'user_id', 'log_name', 'description',
      'subject_type', 'subject_id', 'properties',
      'ip_address', 'user_agent'
  ];

  protected $casts = [
      'properties' => 'array',
      'created_at' => 'datetime',
  ];

  public function user() { return $this->belongsTo(User::class); }
  public function subject() { return $this->morphTo(); }
  ```

### 3.5 Update User Model Relationships
- [x] File: `app/Models/User.php`
- [x] Add relationships:
  ```php
  public function posts() { return $this->hasMany(Post::class); }
  public function likes() { return $this->hasMany(Like::class); }
  public function comments() { return $this->hasMany(Comment::class); }
  public function activityLogs() { return $this->hasMany(ActivityLog::class); }
  ```

**Estimasi Waktu:** 40 menit ✅ **Completed: Nov 8, 2025**

---

## PHASE 4: API ROUTES SETUP ✅ **COMPLETED**

### 4.1 Create API Routes File
- [x] Create file: `routes/api.php`
- [x] Register di `bootstrap/app.php`:
  ```php
  ->withRouting(
      api: __DIR__.'/../routes/api.php',
      apiPrefix: 'api',
      ...
  )
  ```

### 4.2 Public Routes (Unauthenticated)
- [x] `POST /api/register` → AuthController@register
- [x] `POST /api/login` → AuthController@login
- [x] `GET /api/email/verify/{id}/{hash}` → AuthController@verify
- [x] `POST /api/email/verification-notification` → AuthController@resend

### 4.3 Protected Routes (auth:sanctum)
```php
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    - [x] POST /api/logout → AuthController@logout
    - [x] GET /api/user → AuthController@user

    // Posts (Resource Controller)
    - [x] GET /api/posts → PostController@index
    - [x] POST /api/posts → PostController@store
    - [x] GET /api/posts/{post} → PostController@show
    - [x] PUT/PATCH /api/posts/{post} → PostController@update
    - [x] DELETE /api/posts/{post} → PostController@destroy

    // Likes
    - [x] POST /api/posts/{post}/like → LikeController@like
    - [x] DELETE /api/posts/{post}/unlike → LikeController@unlike

    // Comments
    - [x] GET /api/posts/{post}/comments → CommentController@index
    - [x] POST /api/posts/{post}/comments → CommentController@store
    - [x] PUT/PATCH /api/comments/{comment} → CommentController@update
    - [x] DELETE /api/comments/{comment} → CommentController@destroy

    // Activity Logs
    - [x] GET /api/activity-logs → ActivityLogController@index
});
```

**Estimasi Waktu:** 30 menit ✅ **Completed: Nov 8, 2025**

---

## PHASE 5: CONTROLLERS (API - SOLID Principles) ✅ **COMPLETED**

### 5.1 Authentication Controller
- [x] Command: `php artisan make:controller Api/AuthController`
- [x] File: `app/Http/Controllers/Api/AuthController.php`
- [x] Methods:
  - `register(RegisterRequest $request)` - Create user, send verification email
  - `login(LoginRequest $request)` - Validate & return token
  - `logout(Request $request)` - Revoke token
  - `verify(EmailVerificationRequest $request)` - Mark email verified
  - `resend(Request $request)` - Resend verification email
- [x] **SOLID:** Single Responsibility (handle auth HTTP only), inject AuthService

### 5.2 Post Controller
- [x] Command: `php artisan make:controller Api/PostController --api`
- [x] File: `app/Http/Controllers/Api/PostController.php`
- [x] Methods:
  - `index()` - List all posts (paginated, with likes/comments count)
  - `store(StorePostRequest $request)` - Create post
  - `show(Post $post)` - Show single post
  - `update(UpdatePostRequest $request, Post $post)` - Update post (authorize)
  - `destroy(Post $post)` - Delete post (authorize)
- [x] **SOLID:** Inject PostService (DIP)

### 5.3 Like Controller
- [x] Command: `php artisan make:controller Api/LikeController`
- [x] File: `app/Http/Controllers/Api/LikeController.php`
- [x] Methods:
  - `like(Post $post)` - Like a post
  - `unlike(Post $post)` - Unlike a post
- [x] **SOLID:** Inject LikeService

### 5.4 Comment Controller
- [x] Command: `php artisan make:controller Api/CommentController`
- [x] File: `app/Http/Controllers/Api/CommentController.php`
- [x] Methods:
  - `index(Post $post)` - List comments on post
  - `store(StoreCommentRequest $request, Post $post)` - Add comment
  - `update(UpdateCommentRequest $request, Comment $comment)` - Update (authorize)
  - `destroy(Comment $comment)` - Delete (authorize)
- [x] **SOLID:** Inject CommentService

### 5.5 Activity Log Controller
- [x] Command: `php artisan make:controller Api/ActivityLogController`
- [x] File: `app/Http/Controllers/Api/ActivityLogController.php`
- [x] Methods:
  - `index()` - List authenticated user's activity logs

**Estimasi Waktu:** 1 jam 30 menit ✅ **Completed: Nov 8, 2025**

---

## PHASE 6: SERVICES LAYER (Business Logic - SOLID) ✅ **COMPLETED**

> **Purpose:** Separate business logic from controllers (SRP + DIP)

### 6.1 Create Services Directory
- [x] Create folder: `app/Services/`

### 6.2 Authentication Service
- [x] File: `app/Services/AuthService.php`
- [x] Methods:
  - `register(array $data): User` - Hash password, create user, log activity
  - `login(array $credentials): array` - Validate, create token, log activity
  - `logout(User $user): void` - Revoke tokens, log activity
  - `sendVerificationEmail(User $user): void`
- [x] **SOLID:** Single responsibility (auth business logic only)

### 6.3 Post Service
- [x] File: `app/Services/PostService.php`
- [x] Methods:
  - `getAllPosts(int $perPage = 15): LengthAwarePaginator`
  - `createPost(User $user, array $data): Post` - Validate base64, log activity
  - `updatePost(Post $post, array $data): Post` - Log activity
  - `deletePost(Post $post): bool` - Log activity
- [x] **SOLID:** Handle post business logic, delegate to ActivityLoggerService

### 6.4 Like Service
- [x] File: `app/Services/LikeService.php`
- [x] Methods:
  - `likePost(User $user, Post $post): Like` - Check duplicate, log activity
  - `unlikePost(User $user, Post $post): bool` - Log activity
  - `isLikedByUser(Post $post, User $user): bool`

### 6.5 Comment Service
- [x] File: `app/Services/CommentService.php`
- [x] Methods:
  - `getComments(Post $post): Collection`
  - `createComment(User $user, Post $post, string $content): Comment`
  - `updateComment(Comment $comment, string $content): Comment`
  - `deleteComment(Comment $comment): bool`

### 6.6 Activity Logger Service
- [x] File: `app/Services/ActivityLoggerService.php`
- [x] Methods:
  - `log(User $user = null, string $logName, string $description, $subject = null, array $properties = []): ActivityLog`
  - Automatically capture IP & User Agent from request
- [x] **SOLID:** Single responsibility (logging only), Open/Closed (extensible)

**Estimasi Waktu:** 2 jam ✅ **Completed: Nov 8, 2025**

---

## PHASE 7: FORM REQUESTS (Validation - SRP)

### 7.1 Authentication Requests
- [ ] Command: `php artisan make:request RegisterRequest`
- [ ] File: `app/Http/Requests/RegisterRequest.php`
- [ ] Validation rules:
  ```php
  'name' => 'required|string|max:255',
  'email' => 'required|email|unique:users,email',
  'password' => 'required|string|min:8|confirmed',
  ```

- [ ] Command: `php artisan make:request LoginRequest`
- [ ] File: `app/Http/Requests/LoginRequest.php`
- [ ] Validation rules:
  ```php
  'email' => 'required|email',
  'password' => 'required|string',
  ```

### 7.2 Post Requests
- [ ] Command: `php artisan make:request StorePostRequest`
- [ ] File: `app/Http/Requests/StorePostRequest.php`
- [ ] Validation rules:
  ```php
  'caption' => 'nullable|string|max:2200',
  'image' => 'required|string', // Validate base64 format
  ```
- [ ] Add custom validation for base64 image

- [ ] Command: `php artisan make:request UpdatePostRequest`
- [ ] File: `app/Http/Requests/UpdatePostRequest.php`
- [ ] Validation rules:
  ```php
  'caption' => 'nullable|string|max:2200',
  'image' => 'nullable|string', // base64
  ```

### 7.3 Comment Requests
- [ ] Command: `php artisan make:request StoreCommentRequest`
- [ ] File: `app/Http/Requests/StoreCommentRequest.php`
- [ ] Validation rules:
  ```php
  'content' => 'required|string|max:500',
  ```

- [ ] Command: `php artisan make:request UpdateCommentRequest`
- [ ] File: `app/Http/Requests/UpdateCommentRequest.php`
- [ ] Validation rules: (same as StoreCommentRequest)

**Estimasi Waktu:** 40 menit

---

## PHASE 8: MIDDLEWARE

### 8.1 Email Verification Middleware
- [ ] Command: `php artisan make:middleware EnsureEmailIsVerified`
- [ ] File: `app/Http/Middleware/EnsureEmailIsVerified.php`
- [ ] Logic: Check `email_verified_at`, return 403 if null
- [ ] Apply to protected routes yang butuh verified email

### 8.2 Activity Logging Middleware (Optional)
- [ ] Command: `php artisan make:middleware LogApiActivity`
- [ ] File: `app/Http/Middleware/LogApiActivity.php`
- [ ] Logic: Log semua API requests ke activity_logs

### 8.3 Register Middleware
- [ ] File: `bootstrap/app.php`
- [ ] Register middleware aliases:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias([
          'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
      ]);
  })
  ```

**Estimasi Waktu:** 30 menit

---

## PHASE 9: AUTHORIZATION POLICIES

### 9.1 Post Policy
- [ ] Command: `php artisan make:policy PostPolicy --model=Post`
- [ ] File: `app/Policies/PostPolicy.php`
- [ ] Methods:
  - `update(User $user, Post $post): bool` - Check ownership
  - `delete(User $user, Post $post): bool` - Check ownership

### 9.2 Comment Policy
- [ ] Command: `php artisan make:policy CommentPolicy --model=Comment`
- [ ] File: `app/Policies/CommentPolicy.php`
- [ ] Methods:
  - `update(User $user, Comment $comment): bool` - Check ownership
  - `delete(User $user, Comment $comment): bool` - Check ownership

### 9.3 Use Policies in Controllers
- [ ] PostController: `$this->authorize('update', $post);`
- [ ] CommentController: `$this->authorize('update', $comment);`

**Estimasi Waktu:** 30 menit

---

## PHASE 10: API RESOURCES (Data Transformation)

### 10.1 User Resource
- [ ] Command: `php artisan make:resource UserResource`
- [ ] File: `app/Http/Resources/UserResource.php`
- [ ] Transform:
  ```php
  'id' => $this->id,
  'name' => $this->name,
  'username' => $this->username,
  'email' => $this->email,
  'email_verified_at' => $this->email_verified_at,
  'profile_image' => $this->profile_image,
  'bio' => $this->bio,
  ```

### 10.2 Post Resource
- [ ] Command: `php artisan make:resource PostResource`
- [ ] File: `app/Http/Resources/PostResource.php`
- [ ] Transform:
  ```php
  'id' => $this->id,
  'user' => new UserResource($this->whenLoaded('user')),
  'caption' => $this->caption,
  'image' => $this->image, // base64
  'likes_count' => $this->likes_count ?? $this->likes()->count(),
  'comments_count' => $this->comments_count ?? $this->comments()->count(),
  'is_liked_by_user' => $this->when(auth()->check(), fn() => ...),
  'created_at' => $this->created_at,
  ```

### 10.3 Comment Resource
- [ ] Command: `php artisan make:resource CommentResource`
- [ ] File: `app/Http/Resources/CommentResource.php`
- [ ] Transform:
  ```php
  'id' => $this->id,
  'user' => new UserResource($this->whenLoaded('user')),
  'content' => $this->content,
  'created_at' => $this->created_at,
  ```

### 10.4 Activity Log Resource
- [ ] Command: `php artisan make:resource ActivityLogResource`
- [ ] File: `app/Http/Resources/ActivityLogResource.php`
- [ ] Transform:
  ```php
  'id' => $this->id,
  'log_name' => $this->log_name,
  'description' => $this->description,
  'properties' => $this->properties,
  'created_at' => $this->created_at,
  ```

**Estimasi Waktu:** 45 menit

---

## PHASE 11: PHPUNIT TESTING

### 11.1 Test Database Setup
- [ ] Create `.env.testing` file
- [ ] Configure SQLite for testing:
  ```env
  DB_CONNECTION=sqlite
  DB_DATABASE=:memory:
  ```
- [ ] Update `phpunit.xml`:
  ```xml
  <env name="DB_CONNECTION" value="sqlite"/>
  <env name="DB_DATABASE" value=":memory:"/>
  ```

### 11.2 Feature Tests - Authentication
- [ ] File: `tests/Feature/Auth/RegisterTest.php`
  - Test successful registration
  - Test validation errors (email required, password min 8, etc.)
  - Test email verification sent
  - Test duplicate email

- [ ] File: `tests/Feature/Auth/LoginTest.php`
  - Test successful login dengan verified email
  - Test login dengan unverified email (should fail)
  - Test invalid credentials
  - Test token returned

- [ ] File: `tests/Feature/Auth/EmailVerificationTest.php`
  - Test email verification link
  - Test resend verification email

- [ ] File: `tests/Feature/Auth/LogoutTest.php`
  - Test token revoked after logout

### 11.3 Feature Tests - Posts
- [ ] File: `tests/Feature/Post/CreatePostTest.php`
  - Test authenticated user can create post
  - Test unauthenticated user cannot create
  - Test validation (image required, caption max length)
  - Test base64 image validation
  - Test activity logged

- [ ] File: `tests/Feature/Post/UpdatePostTest.php`
  - Test owner can update post
  - Test non-owner cannot update (403)
  - Test validation

- [ ] File: `tests/Feature/Post/DeletePostTest.php`
  - Test owner can delete
  - Test non-owner cannot delete

- [ ] File: `tests/Feature/Post/ListPostsTest.php`
  - Test pagination works
  - Test posts ordered by created_at DESC
  - Test includes likes/comments count

### 11.4 Feature Tests - Likes
- [ ] File: `tests/Feature/Like/LikePostTest.php`
  - Test authenticated user can like post
  - Test cannot like same post twice (unique constraint)
  - Test like count increments
  - Test activity logged

- [ ] File: `tests/Feature/Like/UnlikePostTest.php`
  - Test user can unlike post
  - Test like count decrements

### 11.5 Feature Tests - Comments
- [ ] File: `tests/Feature/Comment/CreateCommentTest.php`
  - Test authenticated user can comment
  - Test validation (content required, max 500 chars)
  - Test activity logged

- [ ] File: `tests/Feature/Comment/UpdateCommentTest.php`
  - Test owner can update comment
  - Test non-owner cannot update

- [ ] File: `tests/Feature/Comment/DeleteCommentTest.php`
  - Test owner can delete comment
  - Test non-owner cannot delete

### 11.6 Unit Tests - Services
- [ ] File: `tests/Unit/Services/AuthServiceTest.php`
  - Test password hashed correctly
  - Test token generated
  - Test activity logged

- [ ] File: `tests/Unit/Services/PostServiceTest.php`
  - Test post creation
  - Test base64 validation

- [ ] File: `tests/Unit/Services/ActivityLoggerServiceTest.php`
  - Test log created with correct data
  - Test IP and user agent captured

### 11.7 Unit Tests - Models
- [ ] File: `tests/Unit/Models/PostTest.php`
  - Test relationships (user, likes, comments)
  - Test accessors (likes_count, comments_count)

- [ ] File: `tests/Unit/Models/UserTest.php`
  - Test relationships

### 11.8 Run All Tests
- [ ] Execute: `php artisan test`
- [ ] Target: 100% pass rate
- [ ] Check coverage: `php artisan test --coverage`

**Estimasi Waktu:** 3-4 jam (paling lama, tapi penting!)

---

## PHASE 12: DOCUMENTATION & CONFIGURATION

### 12.1 API Documentation
- [ ] Create file: `docs/API.md`
- [ ] Document endpoints:
  - Authentication flow
  - Request/response examples
  - Error responses
  - Example tokens

### 12.2 Update README.md
- [ ] Installation steps
- [ ] Database setup (MySQL port 3307)
- [ ] Brevo SMTP configuration guide
- [ ] Running migrations
- [ ] Running tests
- [ ] SOLID principles yang diimplementasikan
- [ ] Project structure

### 12.3 Environment Example
- [ ] Update `.env.example` dengan semua required variables:
  - Database config
  - Brevo SMTP config
  - App URL
  - Frontend URL (untuk redirect setelah verify email)

**Estimasi Waktu:** 1 jam

---

## PHASE 13: TESTING & QUALITY ASSURANCE

### 13.1 Run All Tests
- [ ] `php artisan test`
- [ ] Ensure semua tests pass
- [ ] Check test coverage minimal 80%

### 13.2 Code Quality
- [ ] Run Laravel Pint: `./vendor/bin/pint`
- [ ] Review code untuk SOLID principles:
  - Single Responsibility ✓
  - Open/Closed ✓
  - Liskov Substitution ✓
  - Interface Segregation ✓
  - Dependency Inversion ✓

### 13.3 Security Review
- [ ] Test authorization pada semua protected endpoints
- [ ] Test email verification requirement
- [ ] Test SQL injection prevention (Eloquent ORM)
- [ ] Test XSS prevention (API responses)
- [ ] Test CSRF (not needed for API with Sanctum tokens)

### 13.4 Manual API Testing (Postman/Thunder Client)
- [ ] Test registration flow
- [ ] Test email verification
- [ ] Test login & token
- [ ] Test create post dengan base64 image
- [ ] Test like/unlike
- [ ] Test comment CRUD
- [ ] Test authorization (non-owner cannot delete)

**Estimasi Waktu:** 2 jam

---

## PHASE 14: FINAL DEPLOYMENT PREPARATION

### 14.1 Performance Optimization
- [ ] Add database indexes (sudah ada di migrations)
- [ ] Implement eager loading di controllers:
  ```php
  Post::with(['user', 'likes', 'comments'])->get();
  ```
- [ ] Add pagination ke semua list endpoints
- [ ] Consider caching (optional untuk MVP):
  - Cache posts list
  - Cache user data

### 14.2 Error Handling
- [ ] Implement global exception handler di `bootstrap/app.php`
- [ ] Return consistent JSON error responses:
  ```json
  {
    "message": "Error message",
    "errors": { ... }
  }
  ```
- [ ] Log errors ke activity_logs

### 14.3 Final Checklist
- [ ] All migrations run successfully
- [ ] All tests passing
- [ ] Code formatted dengan Pint
- [ ] Documentation complete
- [ ] `.env.example` updated
- [ ] Git commits clean & descriptive

**Estimasi Waktu:** 1 jam

---

## SOLID PRINCIPLES IMPLEMENTATION CHECKLIST

### Single Responsibility Principle (SRP)
- [ ] Each controller handles only one resource
- [ ] Form Requests separate validation logic from controllers
- [ ] Services handle business logic separately from controllers
- [ ] Models handle only data representation & relationships
- [ ] ActivityLoggerService only handles logging

### Open/Closed Principle (OCP)
- [ ] Services can be extended without modifying existing code
- [ ] Use interfaces untuk services (optional untuk MVP)
- [ ] Policies extensible untuk new authorization rules

### Liskov Substitution Principle (LSP)
- [ ] Service implementations interchangeable via interfaces (future)
- [ ] Polymorphic relationships (ActivityLog morphTo)

### Interface Segregation Principle (ISP)
- [ ] Controllers depend only on methods they use
- [ ] Specific interfaces untuk each service (optional untuk MVP)
- [ ] No fat interfaces

### Dependency Inversion Principle (DIP)
- [ ] Controllers depend on Service classes (injected via constructor)
- [ ] Services injected, not instantiated dalam controllers
- [ ] Use Laravel service container untuk dependency injection
- [ ] High-level modules (controllers) don't depend on low-level modules (database), both depend on abstractions (models/services)

---

## SUMMARY: FILES TO CREATE

### Total Files to Create: ~55 files

#### Migrations (5 files)
1. Update `0001_01_01_000000_create_users_table.php`
2. `YYYY_MM_DD_create_posts_table.php`
3. `YYYY_MM_DD_create_likes_table.php`
4. `YYYY_MM_DD_create_comments_table.php`
5. `YYYY_MM_DD_create_activity_logs_table.php`

#### Models (5 files)
1. Update `app/Models/User.php`
2. `app/Models/Post.php`
3. `app/Models/Like.php`
4. `app/Models/Comment.php`
5. `app/Models/ActivityLog.php`

#### Controllers (5 files)
1. `app/Http/Controllers/Api/AuthController.php`
2. `app/Http/Controllers/Api/PostController.php`
3. `app/Http/Controllers/Api/LikeController.php`
4. `app/Http/Controllers/Api/CommentController.php`
5. `app/Http/Controllers/Api/ActivityLogController.php`

#### Services (5 files)
1. `app/Services/AuthService.php`
2. `app/Services/PostService.php`
3. `app/Services/LikeService.php`
4. `app/Services/CommentService.php`
5. `app/Services/ActivityLoggerService.php`

#### Form Requests (6 files)
1. `app/Http/Requests/RegisterRequest.php`
2. `app/Http/Requests/LoginRequest.php`
3. `app/Http/Requests/StorePostRequest.php`
4. `app/Http/Requests/UpdatePostRequest.php`
5. `app/Http/Requests/StoreCommentRequest.php`
6. `app/Http/Requests/UpdateCommentRequest.php`

#### Policies (2 files)
1. `app/Policies/PostPolicy.php`
2. `app/Policies/CommentPolicy.php`

#### Resources (4 files)
1. `app/Http/Resources/UserResource.php`
2. `app/Http/Resources/PostResource.php`
3. `app/Http/Resources/CommentResource.php`
4. `app/Http/Resources/ActivityLogResource.php`

#### Middleware (2 files)
1. `app/Http/Middleware/EnsureEmailIsVerified.php`
2. `app/Http/Middleware/LogApiActivity.php` (optional)

#### Routes (1 file)
1. `routes/api.php`

#### Tests (15+ files)
**Feature Tests:**
1. `tests/Feature/Auth/RegisterTest.php`
2. `tests/Feature/Auth/LoginTest.php`
3. `tests/Feature/Auth/EmailVerificationTest.php`
4. `tests/Feature/Auth/LogoutTest.php`
5. `tests/Feature/Post/CreatePostTest.php`
6. `tests/Feature/Post/UpdatePostTest.php`
7. `tests/Feature/Post/DeletePostTest.php`
8. `tests/Feature/Post/ListPostsTest.php`
9. `tests/Feature/Like/LikePostTest.php`
10. `tests/Feature/Like/UnlikePostTest.php`
11. `tests/Feature/Comment/CreateCommentTest.php`
12. `tests/Feature/Comment/UpdateCommentTest.php`
13. `tests/Feature/Comment/DeleteCommentTest.php`

**Unit Tests:**
14. `tests/Unit/Services/AuthServiceTest.php`
15. `tests/Unit/Services/PostServiceTest.php`
16. `tests/Unit/Services/ActivityLoggerServiceTest.php`
17. `tests/Unit/Models/PostTest.php`
18. `tests/Unit/Models/UserTest.php`

#### Documentation (2+ files)
1. `docs/API.md`
2. Update `README.md`

#### Configuration (3 files)
1. `.env` (local development)
2. `.env.testing` (for tests)
3. Update `.env.example`

---

## ESTIMATED TOTAL TIME

| Phase | Estimated Time |
|-------|----------------|
| Phase 1: Environment & Auth Setup | 30 minutes |
| Phase 2: Migrations | 45 minutes |
| Phase 3: Models | 40 minutes |
| Phase 4: Routes | 30 minutes |
| Phase 5: Controllers | 1.5 hours |
| Phase 6: Services | 2 hours |
| Phase 7: Form Requests | 40 minutes |
| Phase 8: Middleware | 30 minutes |
| Phase 9: Policies | 30 minutes |
| Phase 10: Resources | 45 minutes |
| Phase 11: Testing | 3-4 hours |
| Phase 12: Documentation | 1 hour |
| Phase 13: QA | 2 hours |
| Phase 14: Final Prep | 1 hour |

**TOTAL:** ~15-16 jam development time

**Untuk interview technical test:** Bisa diselesaikan dalam 2-3 hari kerja dengan fokus penuh.

---

## QUICK TIPS UNTUK TECHNICAL TEST

### 1. Prioritas Implementasi (Jika waktu terbatas)
**Must Have (Core MVP):**
- ✅ Auth (register, login, email verification)
- ✅ Posts CRUD
- ✅ Like/Unlike
- ✅ Comments CRUD
- ✅ Authorization (policies)
- ✅ Basic testing (minimal feature tests untuk happy path)

**Nice to Have:**
- Activity logging (bisa simplified)
- Comprehensive testing (unit tests)
- Advanced error handling

### 2. Time-Saving Strategies
- Use Laravel built-in features (Sanctum, Policies, Form Requests)
- Copy-paste similar controllers/services & modify
- Use Laravel Pint untuk auto-format (save time)
- Focus on feature tests first (more value)

### 3. Demonstrating SOLID Principles
**Key Points to Highlight:**
- Services layer (SRP + DIP) ⭐
- Form Requests (SRP for validation) ⭐
- Policies (SRP for authorization) ⭐
- Constructor injection (DIP) ⭐
- Comments di code explaining SOLID decisions

### 4. What Reviewers Look For
1. **Clean code structure** (folders organized)
2. **Proper validation** (Form Requests)
3. **Authorization implemented** (Policies)
4. **Tests written** (at least feature tests)
5. **API documentation** (README or Postman collection)
6. **Database design** (proper relationships, indexes)
7. **Security** (auth required, owner-only access)
8. **SOLID principles** (evidence in code structure)

---

## EXECUTION TRACKING

### Session 1 (Target: Environment + Database) ✅ **COMPLETED - Nov 8, 2025**
- [x] Phase 1 completed - Environment & Authentication Setup
- [x] Phase 2 completed - Database Migrations (NO FK constraints)
- [x] Phase 3 completed - Eloquent Models & Relationships
- [ ] Commit: "Add database migrations and models"

### Session 2 (Target: Controllers + Services) ✅ **COMPLETED - Nov 8, 2025**
- [x] Phase 4 completed
- [x] Phase 5 completed
- [x] Phase 6 completed
- [x] Commit: "Add controllers and services layer"

### Session 3 (Target: Validation + Authorization)
- [ ] Phase 7 completed
- [ ] Phase 8 completed
- [ ] Phase 9 completed
- [ ] Phase 10 completed
- [ ] Commit: "Add validation, middleware, policies, and resources"

### Session 4 (Target: Testing)
- [ ] Phase 11 completed (at least feature tests)
- [ ] Commit: "Add comprehensive testing suite"

### Session 5 (Target: Documentation + QA)
- [ ] Phase 12 completed
- [ ] Phase 13 completed
- [ ] Phase 14 completed
- [ ] Commit: "Add documentation and final optimizations"
- [ ] **READY FOR SUBMISSION** 🚀

---

## NOTES & LEARNINGS

_Gunakan section ini untuk catatan selama development:_

### Issues Encountered:
-

### Solutions Found:
-

### Code Improvements Made:
-

### Interview Talking Points:
-

---

**Good luck dengan technical test! 🚀**

**Remember:** Focus on clean, readable, well-tested code. Quality over quantity!
