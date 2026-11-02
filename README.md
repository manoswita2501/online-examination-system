# Online Examination System

A complete, beginner-friendly academic **Online Examination System** built with **Laravel, Eloquent ORM, Blade, MySQL, Custom CSS (minimalist academic green palette), and Vanilla JavaScript**.

Designed specifically for colleges, universities, and academic institutions to manage batches, enroll students, schedule and conduct multiple-choice examinations, explicitly persist student responses, auto-submit upon time expiry, automatically evaluate scores, and print official student and batch grade reports.

---

## 📌 Technology Stack

- **Backend:** PHP `^8.2`
- **Framework:** Laravel `^12.0` (MVC Architecture)
- **ORM:** Laravel Eloquent ORM
- **Frontend Templating:** Laravel Blade
- **Database:** MySQL / MariaDB (XAMPP)
- **Styling:** Custom CSS (`public/css/app.css` — Minimalist shades of green design)
- **Interactivity:** Vanilla JavaScript (zero external JS frameworks or libraries)
- **Authentication:** Laravel Session-Based Authentication
- **Reporting:** Browser-based Printing (`window.print()`) & Save as PDF via `@media print` rules

---

## 🚀 Key Features

### 1. User Roles & Dual Authentication
- **Shared `users` Table:** Role column (`student` / `admin`) prevents fragmented schemas.
- **Student Login:** Email address and password with account active verification.
- **Administrator Login:** 5-digit Admin ID (e.g., `01234`) and password.
- **Security & Route Guard:** Custom `RoleMiddleware` prevents students from accessing administrator routes and prevents admins from taking exams.
- **Deactivation Protection:** Deactivated accounts cannot sign in.

### 2. Academic Batch Management
- Manage Courses, Departments, Semesters, Sections, and Academic Years (e.g., *BTech CSE, Semester 1, Section A, 2025-2026*).
- Prevents accidental deletion of batches that contain enrolled students or scheduled exams.

### 3. Student Enrollment & Management
- Public student registration is disabled; all credentials are provisioned by the Administrator.
- Unique Student ID / Roll Number and Email validation.
- Search by name, email, or Student ID; filter by academic batch.
- One-click account activation / deactivation toggle.

### 4. MCQ Examination Management & Dynamic Question Builder
- **Section A (Details):** Title, subject module, instructions, target batch, date, start time, duration, and marks per question.
- **Section B (Dynamic Questions):** Vanilla JavaScript dynamic MCQ entry (Question statement, Options A–D, and correct key radio selector). Add/remove questions dynamically before saving.
- **Section C (Review & Publish):** Live preview summary calculating total questions, duration, and maximum marks; save as Draft or Publish immediately.
- **Integrity Lock:** Cannot edit questions, options, or marking schemes once the first attempt has begun.

### 5. Interactive Student Examination Interface
- **Distraction-Free Environment:** Top navigation is minimized during live testing.
- **Question Palette:** Grid of numbered buttons with clear states:
  - 🟩 *Answered & Saved to Database*
  - ⬜ *Unanswered Question*
  - 🟢 *Currently Viewing*
- **Explicit Answer Saving:** Students click **"Save Answer"** to persist responses via server route.
- **Unsaved Selection Warning:** If a student selects an option without saving and navigates away, a clear warning prevents accidental loss of selections.
- **Question Navigation:** Previous, Next, and direct jump via Question Palette buttons.
- **Countdown Timer:** JavaScript timer synchronized with server-side attempt deadline. Warnings trigger when under 5 minutes; automatically submits when time reaches `00:00`.
- **Automatic Submission:** Server-side expiry enforcement and scheduled task (`exam:finalize-expired`).

### 6. Automated Scoring & Immediate Results
- Instant server-side grading upon final submission.
- Detailed metrics: Total Questions, Answered, Unanswered, Correct, Incorrect, Marks Obtained, Maximum Marks, Percentage, and Time Taken.
- Correct answer keys are withheld on the student result sheet to preserve academic integrity.

### 7. Results & Comprehensive Reports
- **Result Directory:** Full results table with filters for Exam, Batch, and Student.
- **Individual Student Report:** Question-by-question evaluation breakdown for administrators.
- **Consolidated Batch Report:** Class cohort statistics including Assigned Count, Completed Count, Unattempted Count, Average Score, Highest Score, and Lowest Score.
- **Print & Save as PDF:** One-click `window.print()` formatted with `@media print` rules hiding sidebars, buttons, and navigation.

---

## 💻 Installation & Setup Guide

### Prerequisites
1. **PHP:** `^8.2` (installed with XAMPP or CLI)
2. **Composer:** `^2.x`
3. **MySQL / MariaDB:** Running on `127.0.0.1:3306` (XAMPP MySQL service started)

### Step 1: Clone or Navigate to the Workspace
```bash
cd c:\xampp\htdocs\online_exam
```

### Step 2: Configure Environment (.env)
Ensure your `.env` file contains:
```env
APP_NAME="Online Examination System"
APP_ENV=local
APP_KEY=base64:MeyvYTYwXDMGPjxw9Ev4j7nauzvx/QNQ3dWNf+y6NQY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=online_exam
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Run Migrations and Seed Database
Run the following Artisan command to create all tables and populate sample academic data:
```bash
php artisan migrate:fresh --seed
```

### Step 4: Run the Development Server
```bash
php artisan serve --port=8000
```
Open your web browser and navigate to: **`http://localhost:8000`**

---

## 🔑 Default Demo Accounts

| Portal | Login Identifier | Password | Role | Notes |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | Admin ID: `01234` | `password123` | `admin` | **Prof. M. Bose** (Full control center access) |
| **Student 1** | `abir.banerjee@student.edu` | `password123` | `student` | **Abir Banerjee** (BTech CSE Section A) |
| **Student 2** | `bipasha.sarkar@student.edu` | `password123` | `student` | **Bipasha Sarkar** (BTech CSE Section A) |
| **Student 3** | `dinesh.kumar@student.edu` | `password123` | `student` | **Dinesh Kumar** (BTech CSE Section B) |

---

## 🧪 Running Automated Tests

Run the full PHPUnit test suite to verify authentication, role security, exam taking, answer saving, auto-finalization, and scoring:
```bash
php artisan test
```

---

## ⏰ Background Scheduler (Automatic Submission)

To test the automatic finalization command manually on expired attempts:
```bash
php artisan exam:finalize-expired
```

To run the scheduler in development:
```bash
php artisan schedule:work
```
