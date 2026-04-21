# CLAUDE.md

This file provides strict guidance for AI agents and developers working on this repository.
All rules are mandatory and must be followed exactly.

---

## Project Overview

This is a Laravel 13 application for managing a cosmetics marketing network.

The system uses a **multi-auth architecture** with strict separation between:

* Admin users (web guard)
* Marketers (marketer guard)

The architecture enforces clean separation of concerns, security, and scalability.

---

## Tech Stack

* Laravel 13
* PHP 8.3+
* MySQL
* Blade (no Livewire, no Inertia)

---

## Commands

```bash
composer setup
composer dev
composer test
php artisan test --filter TestClassName
./vendor/bin/pint
php artisan migrate
php artisan db:seed
```

Default admin:

* username: hala
* password: 123123123

---

## Core Architecture Rules

* Controllers must be thin
* No business logic inside controllers
* All business logic must live in Services
* Use dependency injection everywhere
* Validation must use FormRequest classes
* No direct database queries inside controllers
* Use Middleware for access control
* Do not duplicate logic across layers

---

## Authentication

### Guards

| Guard    | Model    | Area            |
| -------- | -------- | --------------- |
| web      | User     | Admin panel     |
| marketer | Marketer | Marketer portal |

### Rules

* Never mix guards
* Always explicitly use guard (Auth::guard('web'))
* Shared login form (username + password)
* AuthService handles login flow

### Marketer Access Rules

Marketer must:

* status = approved
* is_active = true

Enforced via middleware:

* auth:marketer
* marketer.active

---

## Routes Structure

Routes must be split by domain:

* routes/web.php

  * Public routes
  * Authentication
  * Admin (web guard)

* routes/marketer.php

  * Marketer routes only
  * Must use:

    * auth:marketer
    * marketer.active

* routes/api.php

  * API routes only
  * Stateless

### Rules

* Never mix user and marketer routes
* Always use route groups
* Always use prefixes and naming

Example:

Route::middleware(['auth:marketer', 'marketer.active'])
->prefix('marketer')
->name('marketer.')
->group(base_path('routes/marketer.php'));

---

## Route Naming

* Always use named routes
* Never hardcode URLs

Examples:

Admin:

* dashboard
* users.index

Marketer:

* marketer.dashboard
* marketer.products.index

---

## Middleware Rules

Use middleware for:

* authentication
* authorization
* status validation

Do not place these checks inside controllers

Examples:

* auth:web
* auth:marketer
* marketer.active
* permission:users

---

## Controllers

* Must only handle HTTP layer
* Must not contain business logic
* Must not call external APIs
* Must not perform complex queries

Controllers should only:

* validate request (via FormRequest)
* call service
* return response

---

## Services

All business logic must live in Services.

Examples:

* AuthService
* ProductService
* MarketerService
* PaymentService
* QrService

### Rules

* No DB logic in controllers
* No external API calls in controllers
* Services must be reusable
* Keep methods small and focused

---

## External APIs

* Never call external APIs inside controllers

* Never call APIs inside Blade

* Use Laravel HTTP Client (Http::)

* All integrations must be inside Services

### Must handle:

* timeouts
* retries
* failures

### Rules

* Never trust external responses
* Always validate response structure
* Use structured responses (arrays or DTOs)

---

## Blade Layout Structure

layouts/

* app.blade.php → admin users
* marketer.blade.php → marketers
* auth.blade.php → login/register

### Usage

Admin:
@extends('layouts.app')

Marketer:
@extends('layouts.marketer')

Auth:
@extends('layouts.auth')

### Rules

* Never mix layouts between user types
* Layouts must contain:

  * navigation
  * assets
  * sections

---

## Blade Rules

* Blade must not contain business logic
* Blade is for display only

### Forbidden:

* DB queries
* Service calls
* complex logic

### Required:

* Use components for reusable UI

Examples:

* <x-button />
* <x-input />
* <x-card />

---

## Code Style

* No comments inside code
* Always return full code
* Use strict typing (bool, string, etc.)
* Prefer early return pattern
* Avoid nested if statements
* Keep methods small
* Use clear naming

---

## Security Rules

* Never expose if user exists
* Use RateLimiter on login
* Always hash passwords
* Prevent timing attacks where possible
* Do not leak internal errors

---

## Database Rules

* Always use migrations
* Use snake_case for columns
* Use enums for status fields
* Avoid nullable unless necessary

---

## Inventory Rules

* Product quantity must not be edited directly
* Use dedicated actions:

POST products/{product}/add
POST products/{product}/subtract

* Every change must create ProductQuantityLog

---

## System Logging (Observers)

System logging must be handled ONLY through Observers.

### Observers

* MarketerObserver
* ProductObserver

Registered via:
#[ObservedBy(...)] on models

### Rules

* Do not write logs inside controllers

* Do not write logs inside services

* Do not duplicate logging logic

* Logs must be stored in:
  system_logs table

* Logs must be polymorphic:

  * loggable_type
  * loggable_id

* Logs must only be created if admin is authenticated:

Use:
Auth::guard('web')->user()

Do not use:
Auth::user()

### Events to log

* created
* updated
* deleted

### Each log must include:

* user_id
* action
* description
* changes (optional JSON)

### Observer Responsibility

* Observers must only handle logging
* No business logic inside observers

### Goal

Centralized, automatic, consistent logging system.

---

## File Organization

Controllers:

* HTTP only

Services:

* business logic

Requests:

* validation

Middleware:

* access control

Models:

* relationships and scopes only

---

## Query Builder

Admin pages use:

* spatie/laravel-query-builder

Marketer pages:

* manual query logic

---

## File Storage

* Use public disk
* products/ for product images
* passports/ for marketer documents

Run:
php artisan storage:link

---

## AI Instructions

* Always follow architecture rules
* Never add logic inside controllers
* Always use Services
* Always use FormRequest
* Do not use comments in code
* Always return full code unless explicitly told otherwise
* Respect multi-auth separation
* Do not mix admin and marketer logic
* If a change violates architecture, do not implement it

---

## Final Rule

Architecture is strict.
Any code that violates these rules must not be written.