# Event Registration System – Custom Drupal Module

## Project Summary
This project implements a **custom Event Registration System** using Drupal.
It allows administrators to configure events and users to register for them via a public form.
All registrations are stored in custom database tables and confirmation emails are sent to both users and administrators.

The module strictly follows:
- Drupal coding standards
- PSR-4 autoloading
- Dependency Injection
- No contributed modules

---

## Drupal Version
- Drupal Core: **10.x**
- Environment: Local (XAMPP)

---

## Module Location
web/modules/custom/event_reg


---

## Features Implemented

### ✔ Admin Event Configuration
- Create and manage events
- Define registration start & end dates
- Define event date, name, and category

### ✔ Public Event Registration Form
- Available only during valid registration period
- AJAX-based dependent dropdowns
- Duplicate registration prevention

### ✔ Validation Rules
- Email format validation
- Special characters restricted in text fields
- Prevent duplicate registrations using:

Email + Event Date


### ✔ Database Storage
- Custom tables for event configuration and registrations

### ✔ Email Notifications
- Sent to both user and admin
- Fully configurable via admin settings

### ✔ Admin Listing Page
- Filter by Event Date and Event Name (AJAX)
- CSV export
- Participant count
- Permission protected

---

## URLs

| Feature | URL |
|------|-----|
| Event Configuration | `/admin/config/event-reg` |
| Event Registration Form | `/event-register` |
| Admin Registration Listing | `/admin/reports/event-registrations` |

---

## Forms

### 1. Event Configuration Form (Admin)

**Fields:**
- Event Registration Start Date (required)
- Event Registration End Date (required)
- Event Date (required)
- Event Name (required)
- Category
- Online Workshop
- Hackathon
- Conference
- One-day Workshop

This configuration controls which events are available for registration.

---

### 2. Event Registration Form (Public)

**Visible only between start and end dates**

**Fields:**
- Full Name (required)
- Email Address (required)
- College Name (required)
- Department (required)
- Category (dropdown – AJAX)
- Event Date (dropdown – AJAX)
- Event Name (dropdown – AJAX)

---

## Validation Logic

### Duplicate Registration Prevention
- A user **cannot register twice** for the same event date using the same email.

### Field Validation
- Email validated using Drupal email validation
- Text fields reject special characters
- Clear and user-friendly error messages

---

## Database Design

### Table 1: Event Configuration

| Column | Description |
|------|-------------|
| id | Primary key |
| registration_start_date | Start date |
| registration_end_date | End date |
| event_date | Event date |
| event_name | Event name |
| category | Event category |

---

### Table 2: Event Registration

| Column | Description |
|------|-------------|
| id | Primary key |
| full_name | User name |
| email | Email |
| college_name | College |
| department | Department |
| category | Event category |
| event_date | Event date |
| event_id | FK to event configuration |
| created | Timestamp |

---

## Email Notifications

### Recipients
- Registered User
- Admin (if enabled)

### Email Content
- Name
- Event Date
- Event Name
- Category

Emails are sent using **Drupal Mail API**.

---

## Admin Configuration Page

**Path:**
/admin/config/event-reg


**Settings:**
- Admin notification email
- Enable / Disable admin notifications

Uses **Drupal Config API** (no hard-coded values).

---

## Admin Listing Page

**Path:**
/admin/reports/event-registrations


### Features
- Event Date dropdown
- Event Name dropdown (AJAX)
- Participant count
- Registration table (AJAX)
- CSV Export

### Displayed Columns
- Name
- Email
- Event Date
- College Name
- Department
- Submission Date

---

## Permissions

Custom permission used:

view event registrations


Only authorized users can access the admin listing page.

---

## Technical Implementation

- Custom services using Dependency Injection
- No use of `\Drupal::service()` in business logic
- PSR-4 compliant namespaces
- AJAX callbacks via Form API
- Custom database queries using Database API

---

## Repository Structure

event-registration-system/
├── composer.json
├── composer.lock
├── event_reg.sql
├── README.md
└── web/modules/custom/event_reg/
├── event_reg.info.yml
├── event_reg.routing.yml
├── event_reg.services.yml
├── event_reg.permissions.yml
└── src/
├── Form/
├── Controller/
└── Service/

---

## Installation Steps

1. Clone repository
2. Place module in: web/modules/custom/event_reg
3. Import `event_reg.sql` into Drupal database
4. Enable module from **Admin → Extend**
5. Rebuild cache: /core/rebuild.php

---

## Status
✅ All functional requirements implemented
✅ Fully working forms
✅ Emails sending
✅ AJAX working
✅ CSV export working
✅ Ready for submission

---

## Author
**Pranav Singal**
