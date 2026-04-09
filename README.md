# Task Management System

A simple, clean, and intuitive task management system built with Laravel 11.

## Technologies Used

- **Backend**: Laravel 11
- **Authentication**: Laravel Breeze (Blade stack)
- **Frontend**: Bootstrap 5 (Layout & Components), Tailwind CSS (Utility styling), Vanilla JS (AJAX interactions)
- **Database**: MySQL
- **Testing**: PHPUnit (Feature tests)

## Features

- **User Authentication**: Secure registration, login, and logout.
- **Task CRUD**: Create, view, edit, and delete tasks.
- **Task Organization**: Categorize tasks by status (Pending, In Progress, Completed) and priority (Low, Medium, High).
- **Dashboard Overview**: Quick stats on task counts and recent activity.
- **AJAX Status Updates**: Change task status directly from the list view without page reloads.
- **Multi-user Isolation**: Each user can only access and manage their own tasks.
- **Overdue Tracking**: Visual indicators for overdue tasks.

## Setup Instructions

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL Server

### Installation Steps

1. **Clone the repository** (or navigate to the project directory).
2. **Install Composer dependencies**:
   ```bash
   composer install
   ```
3. **Install NPM dependencies and build assets**:
   ```bash
   npm install
   npm run build
   ```
4. **Configure Environment**:
   - Rename `.env.example` to `.env` if not already present.
   - Update your MySQL database credentials:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=task_manager
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```
5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```
6. **Run Migrations**:
   ```bash
   php artisan migrate
   ```
7. **Serve the Application**:
   ```bash
   php artisan serve
   ```

## Testing Approach

The system includes comprehensive feature tests to ensure reliability:

- **AuthTest**: Validates registration, login, failed login attempts, and logout functionality.
- **TaskTest**: Validates:
    - Guests are redirected to login.
    - Authenticated users can create, read, update, and delete tasks.
    - Tasks are isolated per user (a user cannot see or modify another user's tasks).
    - Status updates via AJAX work correctly.

To run the tests:
```bash
php artisan test
```

## Assumptions & Decisions

- **Bootstrap + Tailwind**: Used Bootstrap for the core grid and standard components (navbar, cards, forms) while leveraging Tailwind for fine-tuned utility styling and color consistency.
- **Minimal Dependencies**: Kept the JavaScript footprint small by using Vanilla JS for the AJAX status toggle instead of bringing in a heavy framework like Vue or React for a simple requirement.
- **Security**: Implemented standard Laravel authorization checks in the `TaskController` to prevent unauthorized access to tasks by ID.
- **UX**: Added a "Danger Zone" in the edit view to prevent accidental deletions and used a progress bar on the dashboard for quick visual feedback.
