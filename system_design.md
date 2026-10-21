# EVENTUNWIM System Design Document

**Role**: Lead System Architect Plan
**Project**: Universitas Winaya Mukti Event Management System
**Version**: 1.2

---

## 1. System Architecture

The system is designed as a centralized Event Management Platform where **Only Admins Login** and **Participants Access via Unique Codes**.

### Technology Stack
*   **Backend**: Laravel 11.x (Latest Stable)
*   **Database**: SQLite (File-based, zero-config)
*   **Admin Panel**: AdminLTE v3 (Integrated via Laravel Blade)
*   **Frontend (Participant)**: Laravel Blade + Bootstrap 5 + Alpine.js (Lightweight interactivity)
*   **QR/Barcode**: `simplesoftwareio/simple-qrcode`

---

## 2. UI/UX & Feature Specifications

### A. Public Website (`event.unwim.ac.id`)

#### 1. Homepage (Beranda)
*   **Components**: Header/Navbar, Hero Section (Title, Dynamic Subtitle, Actions), Event Highlight Cards, Quick Access Cards, **Footer** (Style: `http://unwim2026.dev.unwim.ac.id/`).
*   **Data Source**: `events` (where `status = published`).
*   **Actions**:
    *   Click "Detail Event" -> Go to Detail Page.
    *   Click "Masukkan Kode" -> Open Input Code Modal.

#### 2. List Event Page
*   **Components**: Filter Bar (Select + Date Range), Event Card Grid, Pagination.
*   **Filters**: `event_type`, `is_paid`, `is_internal`, `date_range` (Realtime or Submit).
*   **View**: Grid/List toggle.

#### 3. Detail Event Page
*   **Components**: Event Header, Badge (Type), Description, **Accordion Rundown**, CTA "Masukkan Kode".
*   **Data Sources**: `events`, `event_rundowns`.

#### 4. Input Code (Modal)
*   **Components**: Input Text, Submit Button, Alerts (Error/Success).
*   **Logic**: Validate code exists, is active, matches event, not expired.
*   **Action**: Redirect to Invitation Page.

#### 5. Invitation Page (The "Ticket")
*   **Components**: Identity Card (Peserta), Rundown Table, QR Code Card (Hadir / Pulang / Konsumsi).
*   **Data Sources**: `participants`, `invitations`, `events`.
*   **Logic**:
    *   QR stays active based on event rules.
    *   Scan activity recorded in `attendance_logs`.

#### 6. Certificate Redemption (`/sertifikat`)
*   **Components**: Input WhatsApp, Search Button, List Certificates, Download Button.
*   **Logic**: Match WhatsApp number -> Show valid certificates.

#### 7. Verification (`/verifikasi`)
*   **Components**: Input Certificate No / QR Scan, Status Badge, Detail Info.
*   **Logic**: Match `certificate_number` -> Validate Status.

### B. Admin Panel (AdminLTE)

#### 1. Dashboard
*   **Components**: Stat Cards, Chart (Attendance), Recent Activity.
*   **Data Sources**: `events`, `attendance_logs`, `certificates`.

#### 2. Event Management
*   **Components**: Event Table, Add/Edit Button, **Rundown Builder**.
*   **Logic**: Fields shown depend on `event_type` (e.g., Wisuda has different fields than Rapat).

#### 3. Participant Management
*   **Components**: Filter (Event, Type), Participant Table, Import CSV, Manual Add.
*   **Actions**: Bulk Import, Individual Edit.

#### 4. Invitation Codes
*   **Logic**: Generate Unique Codes, Monitor Active/Expired Status.
*   **Components**: Code Table, "Send WA" Button, "Copy Code" Button.

#### 5. Attendance (Absensi)
*   **Components**: Filter (Event/Date), Log Table (Realtime).
*   **Data Source**: `attendance_logs`.

#### 6. Certificate Management
*   **Components**: Template Editor (bg, layout), Live Preview, Generate Button.
*   **Logic**: Template linked to Event Type.

#### 7. Finance
*   **Components**: Payment Table, Approve/Reject Actions.
*   **Logic**: Validate proof of payment -> Update Status.

#### 8. Integrations & Reports
*   **WhatsApp**: Log Table, Retry Button, Status Indicators.
*   **Reports**: Filterable Export (Excel/PDF) for Events, Attendance, Certificates, Finance.

---

## 3. Database Schema (SQLite)

### Core Tables

#### `users` (Admin)
*   `id` (PK)
*   `name`, `email` (unique), `password`
*   `role` (ENUM: `super_admin`, `admin_event`, `admin_keuangan`, `admin_sertifikat`)
*   `is_active` (boolean), `created_at`, `updated_at`

#### `events`
*   `id` (PK)
*   `slug` (unique), `title`
*   `event_type` (ENUM: `rapat`, `seminar`, `workshop`, `wisuda`)
*   `description`, `location`, `start_datetime`, `end_datetime`
*   `is_internal` (boolean), `is_paid` (boolean), `price` (nullable)
*   `status` (ENUM: `draft`, `published`, `closed`)
*   `created_by` (FK -> `users.id`)

#### `event_rundowns`
*   `id` (PK), `event_id`, `time_start`, `time_end`, `agenda`, `order_number`

#### `participants`
*   `id` (PK), `event_id`
*   `name`, `whatsapp`, `email`, `type` (ENUM: `mahasiswa`, `dosen`, `tendik`, `umum`)
*   `nim`, `program_studi`, `fakultas`, `jabatan`, `unit` (Nullable Metadata)
*   `seat_number` (Wisuda)

#### `invitations`
*   `id` (PK), `event_id`, `participant_id`
*   `unique_code` (unique, indexed), `is_active`, `expired_at`
*   `sent_via` (wa/manual)

#### `attendance_logs`
*   `id` (PK), `event_id`, `participant_id`, `invitation_id`
*   `type` (check_in/check_out), `scanned_at`, `device_info`

#### `payments`
*   `id` (PK), `event_id`, `participant_id`
*   `amount`, `method`, `proof_file`, `status` (pending/approved/rejected)
*   `approved_by` (FK -> `users.id`)

#### `certificates`
*   `id` (PK), `event_id`, `participant_id`, `template_id`
*   `certificate_number` (unique), `qr_code_path`, `is_active`

#### `certificate_templates`
*   `id` (PK), `event_type`, `orientation` (landscape/portrait), `layout` (center/left)
*   `background_file`

#### `certificate_signatures`
*   `id` (PK), `template_id`, `signer_name`, `signature_file`, `position`

#### `whatsapp_logs` & `tracer_studies`
*   Standard logging tables as per previous ERD.

---

## 4. Workflows (Condensed)

### General Flow
1.  **Participant** receives Code (WA/Manual).
2.  Opens `event.unwim.ac.id`, inputs Code.
3.  **System** validates -> Shows Invitation with Barcodes.
4.  **On Site**: Admin scans Barcode -> Entry Logged.

### Certificate Flow
1.  Event finishes.
2.  Participant goes to `/sertifikat` -> Inputs WA.
3.  System shows valid certificates -> Download PDF.
4.  Public verifies validity at `/verifikasi`.

---

## 5. Security & Best Practices
*   **Code Entropy**: 8-char random alphanumeric strings.
*   **No Participant Login**: Reduces attack surface; security relies on Code secrecy.
*   **Rate Limiting**: Critical on Code Input and Certificate Download endpoints to prevent enumeration.
