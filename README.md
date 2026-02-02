# Event Registration System – Custom Drupal Module

## Overview
This project is a custom **Event Registration System** developed as a Drupal custom module.
It allows administrators to configure events and users to register for them through a public form.
All registrations are stored in custom database tables and email notifications are sent to both users and administrators.

---

## Drupal Version
- Drupal Core: **10.x**

---

## Module Location
web/modules/custom/event_reg


---

## Functional Features

### 1. Event Configuration (Admin)
Administrators can configure events with:
- Event Registration Start Date
- Event Registration End Date
- Event Date
- Event Name
- Event Category
  - Online Workshop
  - Hackathon
  - Conference
  - One-day Workshop

Configured events control availability on the registration form.

---

### 2. Event Registration Form (Public)
- Accessible only during the configured registration period
- Uses **AJAX-based dependent dropdowns**

**Form Fields**
- Full Name (required)
- Email Address (required)
- College Name (required)
- Department (required)
- Category (dropdown – AJAX)
- Event Date (dropdown – AJAX)
- Event Name (dropdown – AJAX)

---

### 3. Validation Rules
- Email format validation
- Special characters are not allowed in text fields
- Duplicate registration prevention using: Email + Event
- User-friendly validation messages

---

### 4. Data Storage

#### Event Configuration Table
Stores event configuration details:
- ID
- Registration Start Date
- Registration End Date
- Event Date
- Event Name
- Category

#### Event Registration Table
Stores user registrations:
- ID
- Event ID (foreign key)
- Full Name
- Email
- College Name
- Department
- Category
- Event Date
- Created Timestamp

---

### 5. Email Notifications
Emails are sent using **Drupal Mail API**.

**Recipients**
- Registered User
- Administrator (configurable)

**Email Content**
- Name
- Event Date
- Event Name
- Category

---

### 6. Admin Configuration Page
Administrators can manage:
- Admin notification email address
- Enable / Disable admin notifications

Configuration uses **Drupal Config API** with no hard-coded values.

---

### 7. Admin Listing Page
Accessible only to users with a custom permission.

**Features**
- Filter registrations by Event Date
- Filter Event Names based on selected date (AJAX)
- Display total participant count
- Display registration details in tabular format
- Export registrations as CSV

**Displayed Columns**
- Name
- Email
- Event Date
- College Name
- Department
- Submission Date

---

## URLs

| Feature | URL |
|------|-----|
| Event Configuration | `/admin/config/event-reg` |
| Event Registration Form | `/event-register` |
| Admin Registration Listing | `/admin/reports/event-registrations` |

---

## Permissions
Custom permission: view event registrations

Only users with this permission can access the admin listing page.

---

## Technical Implementation
- Custom module (no contributed modules)
- PSR-4 autoloading
- Dependency Injection (no `\Drupal::service()` in business logic)
- Drupal Form API
- Drupal Database API
- Drupal Mail API
- AJAX callbacks for dependent dropdowns
- Drupal coding standards followed

---

## Repository Structure
event-registration-system/
├── composer.json
├── composer.lock
├── event_reg.sql
├── README.md
└── web/
└── modules/
└── custom/
└── event_reg/
├── event_reg.info.yml
├── event_reg.routing.yml
├── event_reg.services.yml
├── event_reg.permissions.yml
├── event_reg.install
├── event_reg.module
└── src/
├── Controller/
├── Form/
└── Service/

---

## Installation Steps
1. Clone the repository
2. Place the module inside: web/modules/custom/event_reg
3. Import `event_reg.sql` into the Drupal database
4. Enable the module from **Admin → Extend**
5. Rebuild cache: /core/rebuild.php

---

## Status
- All required features implemented
- Forms working as expected
- Data stored in custom tables
- Email notifications working
- AJAX functionality implemented
- CSV export implemented
- Ready for submission

---

## Author
**Pranav Singal**



