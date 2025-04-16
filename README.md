# Rental Platform

A full-stack rental website that allows users to register and log in as either renters or owners. Owners can list items (houses, cars, tools) and renters can browse and book items.

## Features

- User authentication (registration and login)
- Role-based access (renters and owners)
- Item listing management
- Category-based browsing
- Booking system
- Messaging system
- Responsive design

## Tech Stack

- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL
- Server: XAMPP

## Project Structure

```
rental-platform/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── includes/
│   ├── config.php
│   ├── functions.php
│   └── auth.php
├── pages/
│   ├── home.php
│   ├── listings.php
│   ├── about.php
│   ├── contact.php
│   ├── login.php
│   └── register.php
└── database/
    └── schema.sql
```

## Setup Instructions

1. Install XAMPP
2. Clone this repository into your XAMPP's htdocs folder
3. Start Apache and MySQL services in XAMPP
4. Import the database schema from `database/schema.sql`
5. Configure database connection in `includes/config.php`
6. Access the website through `http://localhost/rental-platform`

## Database Schema

The database includes tables for:
- Users
- Listings
- Categories
- Bookings
- Messages 