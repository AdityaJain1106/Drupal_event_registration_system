
---

## 📑 Table of Contents
- [About The Project](#about-the-project)
- [Built With](#built-with)
- [Features](#features)
- [Project Structure](#project-structure)
- [Module Functionality](#module-functionality)
- [Installation](#installation)
- [Usage](#usage)
- [URLs](#urls)
- [Permissions](#permissions)
- [Screenshots](#screenshots)
- [Author](#author)

---

## 📘 About The Project

The **Event Registration System** is a **fully custom Drupal 10 module** that enables creation of events, user registrations, admin monitoring, and CSV export. It uses **custom database tables, AJAX forms, mail API, Dependency Injection, and Drupal coding standards**.

This module avoids contributed modules and relies only on **core APIs**, making it lightweight and efficient.

---

## 🛠️ Built With

This project is built using:

- **Drupal 10 Core**
- **Drupal Form API**
- **Drupal AJAX API**
- **Drupal Database API**
- **Drupal Config API**
- **Drupal Mail API**
- **Custom Module + PSR-4 Autoloading**

---

## ⭐ Features

### 🎯 1. Event Configuration (Admin)
Admins can configure:
- Registration Start & End Dates  
- Event Date  
- Event Name  
- Category  
  - Online Workshop  
  - Hackathon  
  - Conference  
  - One-day Workshop  

Event configuration controls **form availability**.

---

### 📝 2. Event Registration (Public Form)
Includes:
- Full Name  
- Email  
- College Name  
- Department  
- Category (AJAX)  
- Event Date (AJAX)  
- Event Name (AJAX)

Form accessible only during registration window.

---

### 🔒 3. Validations
- Email format validation  
- No special characters in text fields  
- Duplicate prevention (Email + Event)  
- Clean, user-friendly messages  

---

### 💾 4. Custom Database Tables

#### Event Configuration Table
Stores:
- ID  
- Registration Dates  
- Event Date  
- Category  
- Event Name  

#### Event Registrations Table
Stores:
- Name  
- Email  
- College  
- Department  
- Category  
- Event Date  
- Timestamp  

---

### 📧 5. Email Notifications
Sent via **Drupal Mail API**.

- Confirmation email to user  
- Notification email to admin (configurable)  

---

### ⚙️ 6. Admin Configuration Page
Admins can:
- Update notification email  
- Enable/Disable admin email alerts  

Uses **Drupal Config API** — no hardcoded values.

---

### 📊 7. Admin Registration Listing
Custom permission-protected page.

Features:
- Filter by Event Date  
- Dynamic (AJAX) Event Name filter  
- Total participant count  
- CSV Export  
- Tabular listing  

Columns:
- Name  
- Email  
- Event Date  
- College  
- Department  
- Submission Date  

---

## 📂 Project Structure

```

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

```

---

## ⚙️ Installation

1. Clone the repository  
2. Move module into:  
   `web/modules/custom/event_reg`
3. Import DB tables using:  
   `event_reg.sql`
4. Enable module in **Admin → Extend**
5. Rebuild cache:  
   `/core/rebuild.php`

---

## 🚀 Usage

### For Admins
- Configure events  
- Manage settings  
- View registrations  
- Export CSV  

### For Users
- Access: `/event-register`
- Register for available events  

---

## 🔗 URLs

| Feature | URL |
|--------|------|
| Event Configuration | `/admin/config/event-reg` |
| Event Registration Form | `/event-register` |
| Admin Registration Listing | `/admin/reports/event-registrations` |

---

## 🔐 Permissions

Custom permission:
```

view event registrations

```

Required to access admin registration listing page.

---



## 👤 Author
**Aditya Jain**  
Full-stack Developer | Open-Source Contributor  
LinkedIn: *[add link here](https://www.linkedin.com/in/adddijain/)*

```
