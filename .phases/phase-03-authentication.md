## Phase 3: Authentication

> Implements user registration, login, password reset, and logout.
> Reference: US-1.1, US-1.2, US-1.3, US-1.4

### Phase 3.1: Registration Page (US-1.1)

- [ ] Create Livewire full-page component `pages::auth.register`
- [ ] Create `RegisterForm` Livewire Form Object with validation:
    - `username`: required, string, min:3, max:20, unique:users
    - `email`: required, email, unique:users
    - `password`: required, min:8, confirmed
- [ ] Implement `save()` method: create user, send verification email, log in, redirect to arena
- [ ] Create Blade view using guest layout and design components
- [ ] Register route: `Route::livewire('/register', 'pages::auth.register')`

**Tests (`tests/Feature/Auth/RegistrationTest.php`):**
- [ ] Test registration page renders successfully (GET /register returns 200)
- [ ] Test user can register with valid data (username, email, password, password_confirmation)
- [ ] Test registration fails with duplicate username
- [ ] Test registration fails with duplicate email
- [ ] Test registration fails with short username (< 3 chars)
- [ ] Test registration fails with long username (> 20 chars)
- [ ] Test registration fails with short password (< 8 chars)
- [ ] Test registration fails with mismatched password confirmation
- [ ] Test registration fails with invalid email format
- [ ] Test user is redirected to arena dashboard after successful registration
- [ ] Test verification email is sent upon registration

### Phase 3.2: Login Page (US-1.2)

- [ ] Create Livewire full-page component `pages::auth.login`
- [ ] Create `LoginForm` Livewire Form Object with fields: `email`, `password`, `remember`
- [ ] Implement `authenticate()` method using Laravel's auth guard
- [ ] Create Blade view using guest layout
- [ ] Register route: `Route::livewire('/login', 'pages::auth.login')`
- [ ] Add "Forgot password?" link

**Tests (`tests/Feature/Auth/LoginTest.php`):**
- [ ] Test login page renders successfully (GET /login returns 200)
- [ ] Test user can log in with valid credentials
- [ ] Test login fails with wrong password
- [ ] Test login fails with non-existent email
- [ ] Test "remember me" sets persistent session
- [ ] Test authenticated user is redirected to arena dashboard
- [ ] Test already-authenticated user accessing /login is redirected to arena

### Phase 3.3: Password Reset (US-1.3)

- [ ] Create Livewire full-page component `pages::auth.forgot-password`
- [ ] Create Livewire full-page component `pages::auth.reset-password`
- [ ] Implement forgot password flow: validate email, send reset link
- [ ] Implement reset password flow: validate token, update password
- [ ] Create Blade views using guest layout
- [ ] Register routes for forgot-password and reset-password

**Tests (`tests/Feature/Auth/PasswordResetTest.php`):**
- [ ] Test forgot password page renders (GET /forgot-password returns 200)
- [ ] Test reset link is sent for valid email
- [ ] Test reset link is not sent for non-existent email (but no error shown for security)
- [ ] Test password can be reset with valid token
- [ ] Test password reset fails with invalid token
- [ ] Test password reset fails with expired token
- [ ] Test user is redirected to login after successful reset

### Phase 3.4: Logout (US-1.4)

- [ ] Add logout action to the authenticated layout sidebar
- [ ] Implement logout via POST route that destroys session
- [ ] Redirect to login page after logout

**Tests (`tests/Feature/Auth/LogoutTest.php`):**
- [ ] Test authenticated user can log out
- [ ] Test session is destroyed after logout
- [ ] Test user is redirected to login page after logout
- [ ] Test guest cannot access logout route

### Phase 3.5: Auth Middleware & Route Protection

- [ ] Apply `auth` middleware to all arena/game routes
- [ ] Apply `guest` middleware to login/register routes
- [ ] Set up `verified` middleware for email verification requirement
- [ ] Create email verification notice page
- [ ] Configure redirect paths (login → /arena, register → /arena)

**Tests (`tests/Feature/Auth/AuthMiddlewareTest.php`):**
- [ ] Test unauthenticated user is redirected to /login when accessing /arena
- [ ] Test authenticated user can access /arena
- [ ] Test unverified user is redirected to verification notice

---

