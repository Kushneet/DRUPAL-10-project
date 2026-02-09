# DRUPAL-10-project
# Drupal 10 Event Registration Module

This project is a **custom Drupal 10 module** developed as part of the **FOSSEE Web Development Screening Task**.

The module allows:
- Administrators to configure events
- Users to register for events using a custom form
- Storage of registrations in custom database tables
- Viewing and exporting registrations from the admin panel

---

## Features

### Admin Features
- Create and configure events
- Set registration start and end dates
- View all event registrations
- Export registrations

### User Features
- Event registration form
- Dynamic event selection
- Data validation using Drupal Form API

---

## Custom Module Location
- web/modules/custom/event_registration
---

## Database Tables

The module uses **custom database tables**.

### `event_registration_event`
Stores event configuration data:
- Event name
- Category
- Registration start date
- Registration end date
- Event date

### `event_registration_entries`
Stores user registration data:
- Event ID
- Full name
- Email
- College
- Department
- Registration timestamp

The SQL schema is provided in:
- web/modules/custom/event_registration/sql/event_registration.sql

---

## Routes / URLs

After enabling the module, the following URLs are available:

### User Page
- /event-registration
- Event registration form for users.

### Admin Pages
- /admin/event-registration/config
- /admin/config/event-registration/settings
- /admin/event-registration/registrations
- /admin/event-registration/registrations/export

---

## Installation Steps

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
3. Enable the custom module:
 drush en event_registration -y
4. Clear Drupal cache:
drush cr
## How to Use the Module

### Admin Configuration
1. Login as an administrator.
2. Go to:
   `/admin/event-registration/config`
3. Configure:
   - Event name
   - Registration start date
   - Registration end date
   - Event date
4. Save the configuration.

### User Registration
1. Visit:
   `/event-registration`
2. Fill the event registration form.
3. Submit the form.
4. Registration data is stored in a custom database table.

### Admin View & Export
- View all registrations:
  `/admin/event-registration/registrations`
- Export registrations:
  `/admin/event-registration/registrations/export`
## Database

This module uses a custom database table created during module installation.

- SQL schema file:
  `web/modules/custom/event_registration/sql/event_registration.sql`
- Table stores:
  - User name
  - Email
  - Event details
  - Registration timestamp
## Permissions

The module defines custom permissions to control access:
- Admin users can configure events and view registrations.
- Regular users can access the event registration form.
## Notes for Evaluators

- This project was developed as part of the FOSSEE Web Development Screening Task.
- The module follows Drupal 10 best practices:
  - Form API
  - Routing system
  - Custom permissions
  - Custom database tables
- No contributed modules were used.

## Troubleshooting

- If routes do not appear after enabling the module, clear cache:
  ```bash
  drush cr
  
## Future Improvements

The following enhancements can be considered in future iterations of this module:

- Integration with Drupal entities instead of custom tables for better extensibility.
- Email notifications to users and administrators upon successful registration.
- Support for multiple events with capacity limits.
- CSV export filtering by event or date range.
- Role-based access control for different admin actions.
