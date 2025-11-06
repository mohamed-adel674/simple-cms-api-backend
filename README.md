# 🚀 Simple Headless CMS Backend API (Laravel Sanctum)

This project is a streamlined, **API-only Content Management System (CMS)** built using **Laravel**. It serves as a robust backend (Headless CMS) ready to be consumed by any modern frontend application (React, Vue, mobile apps, etc.). The focus is on implementing **RESTful principles, robust authentication (Sanctum),** and **granular authorization (Policies)**.

## ✨ Key Features

* **Authentication & Authorization:** Secure API access via **Laravel Sanctum** using Bearer Tokens.
* **User Roles:** Implemented basic roles (`Admin`, `Author`) for clear privilege separation.
* **Posts Management:** Full CRUD (Create, Read, Update, Delete) for articles, including status (`draft`, `published`).
* **Categories Management:** CRUD operations for categorizing content (Admin-only).
* **Comments System:** Public commenting feature with mandatory **Admin approval** before publication.
* **Security:** HTML content sanitization is assumed/required for WYSIWYG inputs (`Post body`) to prevent XSS attacks.
* **RESTful Design:** Clean, resource-based API endpoints.

---

## 🛠️ Installation and Setup

Follow these steps to get the project running locally:

### 1. Clone the Repository
```bash
git clone [YOUR_GITHUB_REPO_URL]
cd simple-cms-api

2. Install Dependencies
composer install

3. Environment Configuration
Create your environment file and generate an application key:

cp .env.example .env
php artisan key:generate
Important: Update the DB_DATABASE, DB_USERNAME, and DB_PASSWORD variables in your .env file to match your local database settings (e.g., MySQL or PostgreSQL).

4. Database Setup

Run migrations to create the necessary tables:

php artisan migrate


5. Running the Server
Start the Laravel development server:

php artisan serve
The API will typically be available at http://127.0.0.1:8000/api.

##🧪 API Endpoints for Postman Testing
The following table summarizes the core API endpoints. All protected routes require a Bearer Token obtained via the /api/login endpoint.

1. Authentication (Public)
Description	Method	Endpoint	Body Data
Register	POST	/api/register	name, email, password, password_confirmation
Login	POST	/api/login	email, password
Logout	POST	/api/logout	(Requires Token)

2. Posts & Public Viewing
Description	Method	Endpoint	Authorization / Role
View All Posts	GET	/api/posts	Public (Only shows published posts)
View Single Post	GET	/api/posts/{slug}	Public (Checks post status)
Create Post	POST	/api/posts	Author or Admin
Update Post	PATCH	/api/posts/{id}	Post Owner or Admin (via Policy)
Delete Post	DELETE	/api/posts/{id}	Post Owner or Admin (via Policy)

3. Categories and Comments Management (Admin Focus)
Description	Method	Endpoint	Role Required
View All Categories	GET	/api/categories	Public
Create Category	POST	/api/categories	Admin
Update/Delete Category	PATCH/DELETE	/api/categories/{id}	Admin
Add Comment	POST	/api/posts/{id}/comments	Public / Guest
Approve Comment	PATCH	/api/comments/{id}/approve	Admin
Delete Comment	DELETE	/api/comments/{id}	Admin


🔑 Authorization Logic (Laravel Policies)
The following permissions are enforced via Laravel's built-in Policies and the role column in the users table:

Action              	Guest	Author	Admin
View Published Posts	✅   	✅	     ✅
Create Post	            ❌  	✅	     ✅
Update/Delete Own Post	❌   	✅      ✅
Update/Delete Other's Post❌   ❌  	✅
Manage Categories (CRUD)❌	   ❌	    ✅
Approve Comments	    ❌	   ❌   	✅
