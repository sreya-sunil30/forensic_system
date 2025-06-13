# Digital Forensic Investigation System

A role-based PHP & MySQL web application designed for secure digital forensic case management. Built as a final year MCA project, it supports Admin, Investigator, and User functionalities for managing digital complaints, evidence, reports, and case investigations.

# Project Overview

This system includes:

- **Admin Module**: Manage users, complaints, assign cases, review reports, update conclusions.
- **Investigator Module**: View assigned cases, upload evidence, submit reports, add notes, track deadlines.
- **User Module**: Register and submit complaints, track case status, view conclusions.

## Features

- Complaint registration with admin review
- Secure login system (Admin, Investigator, User)
- Evidence upload with hash verification
- Calendar system for case deadlines (investigators)
- Daily and Final Report submission by investigators
- Suspicious Pattern Detector
- Public IP Tracker using Leaflet & ipinfo.io
- Bootstrap 5.3 UI with elegant light theme
- Case conclusions & evidence
- Case status tracking for users

## Project Structure

<p>forensic_system/ <br>
├── admin/                 → Admin panel: manage cases, users, reports <br>
├── includes/              → Common includes: db, header, sidebar <br>
├── tools/                 → Utilities like IP Tracker <br>
├── uploads/               → Uploaded evidence/images <br>
├── assigned_cases.php     → Investigator view <br>
├── submit_complaint.php   → Complaint submission form <br>
├── submit_daily_report.php → Investigator's daily reports <br>
├── calendar.php           → Investigator calendar <br>
├── manage_note.php        → Investigator's forensic notes <br>
├── view_case.php          → Case detail view <br>
├── case_conclusion.php    → User view of conclusion <br>
├── index.php              → Login portal <br>
└── config.php             → Database connection settings</p>

## Installation Guide
## Requirements
PHP 7.4+
MySQL
Apache (e.g., XAMPP, WAMP)
Composer (optional)

# Steps
1.Clone the repository:<br>
git clone https://github.com/yourusername/forensic_system.git <br>
cd forensic_system

2.Create a database named forensic_system:<br>
Import your .sql file into the database using phpMyAdmin or MySQL CLI.

3.Configure Connection:<br>
Open config.php and update your DB credentials:<br>
$host = 'localhost'; <br>
$db   = 'forensic_system'; <br>
$user = 'root'; <br>
$pass = ''; 

4.Run the Project:<br>
Place the project in your server root (e.g., htdocs). <br>
Visit http://localhost/forensic_system/ in your browser.

## Default Credentials:
Role: Admin <br>
Email: admin@gmail.com <br>
password: admin123
