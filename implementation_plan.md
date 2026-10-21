# Implementation Plan - EVENTUNWIM

## Goal
Initialize and build the core foundation of the EVENTUNWIM system, including the Laravel framework setup, SQLite database implementation based on the provided ERD, and AdminLTE integration for the admin panel.

## User Review Required
> [!IMPORTANT]
> - Confirm the Laravel version is 11.x.
> - Confirm the use of SQLite as the primary database.
> - Review the AdminLTE integration strategy (using a package or manual asset integration).

## Proposed Changes

### 1. Project Initialization
#### [NEW] [Laravel Project](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM)
- Initialize new Laravel project: `composer create-project laravel/laravel EVENTUNWIM`.
- Configure `.env` for SQLite.
- Create empty `database/database.sqlite`.

### 2. Database Implementation
#### [NEW] [Migrations](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM/database/migrations)
- Create migration files for all 13 tables defined in the System Design / ERD.
- Ensure correct data types and relationships (Foreign Keys).
- Run `php artisan migrate`.

#### [NEW] [Models](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM/app/Models)
- Create Eloquent Models for: `User`, `Event`, `EventRundown`, `Participant`, `Invitation`, `AttendanceLog`, `Payment`, `CertificateTemplate`, `CertificateSignature`, `Certificate`, `CertificateDownloadLog`, `TracerStudy`, `WaLog`.
- Define relationships (`hasMany`, `belongsTo`, `hasOne`) in each model.
- Define `fillable` attributes.

### 3. Admin Panel Setup (AdminLTE)
#### [NEW] [Layouts](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM/resources/views/layouts/admin.blade.php)
- Setup master layout using AdminLTE 3.
- Include Bootstrap 4/5 assets (AdminLTE dependency).
- Create sidebar menu structure matching the design.

### 4. Public Frontend Implementation (Blade + Alpine.js)
#### [NEW] [Components](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM/resources/views/components)
- **Shared**:
    - `x-hero-section` (Title, Dynamic Subtitle, Actions).
    - `x-event-card` (Badges for Type, Paid/Free status).
    - `x-quick-access` (Shortcuts).
    - `x-input-code-modal` (Alpine.js validation).
    - **Footer**: Replicate style from `http://unwim2026.dev.unwim.ac.id/`.

#### [NEW] [Public Pages & Logic](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM/resources/views/public)
- **Home**: Implement Hero, Quick Access, Highlight Events (Filter `status=published`).
- **Event List**: Filter Bar (Type, Internal, Price, Date) with Realtime/Submit logic.
- **Event Detail**: Accordion Rundown, CTA Logic.
- **Invitation View**: Dynamic Rundown, QR Cards (Check-in/Out/Consumption).
- **Certificate**: Search by WhatsApp -> List -> Download.
- **Verification**: Input/QR -> Status Badge.

### 5. Admin Panel Implementation (AdminLTE)
#### [NEW] [Dashboard & Events](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM/resources/views/admin)
- **Dashboard**: Stat Cards, Attendance Chart.
- **Event Management**: Form with **Rundown Builder** (Dynamic JavaScript).
- **Participant**: Filterable Table, Import CSV, Whatsapp Blast Logic.
- **Invitation**: Code Generation & Status Monitor.
- **Attendance**: Realtime Log Table.
- **Certificate**: Template Editor & Live Preview.
- **Reports**: Export logic.

### 6. Authentication (Admin)
#### [MODIFY] [AuthController](file:///C:/Users/BEBEN/Documents/GAntigrafity/EVENTUNWIM/app/Http/Controllers/AuthController.php)
- Implement login logic for Admins.
- Ensure role-based redirection if necessary.

## Verification Plan

### Automated Tests
- **Database**: Run `php artisan migrate:refresh` to verify schema integrity without errors.
- **Models**: Create a basic seeder `DatabaseSeeder` to insert dummy data for Events and Participants to verify relationships.

### Manual Verification
1.  **Project Load**: Open browser to `http://localhost:8000` to see Laravel Welcome.
2.  **Admin Login**: Navigate to `/admin/login`, enter credentials, and verify redirection to the Admin Dashboard with AdminLTE UI.
3.  **Database Check**: Use a DB viewer (or `php artisan tinker`) to inspect the created tables in `database.sqlite`.
