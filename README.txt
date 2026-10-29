PEOPLEDESK - MODULAR CORE PHP EMPLOYEE MANAGEMENT

SETUP
1. Extract the core-php-crud-modular folder into C:\xampp\htdocs\
2. Start Apache in XAMPP.
3. Open http://localhost/core-php-crud-modular/setup.php
4. Sign in using:
   Email: admin@example.com
   Password: Admin@123
5. After setup succeeds, DELETE setup.php.
6. Change the demo password before using this on a shared/live server.

MODULES
- modules/auth/login.php: Login
- modules/auth/logout.php: Logout
- modules/dashboard/index.php: Dashboard
- modules/employees/index.php: Employee list
- modules/employees/create.php: Add employee
- modules/employees/edit.php: Edit employee
- modules/employees/delete.php: Delete employee

STORAGE
- storage/users.json
- storage/employees.json

IMPORTANT
- This is a learning/demo app using JSON file storage, not a production-grade database.
- Do not expose the storage folder directly on a live web server.
- For production, configure web-server access restrictions, use HTTPS, rate-limit login attempts, and preferably use MySQL.
- BASE_URL is configured in config/config.php. If your folder name differs, update BASE_URL.
