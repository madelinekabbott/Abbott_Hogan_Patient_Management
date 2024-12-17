# Data Collection Script - README4.txt

## Overview
This script connects to the 'patient_management' database and retrieves key data for reporting purposes. It generates:
1. Patient details including diagnoses, prescriptions, and lab results.
2. Doctor-patient assignments.
3. Upcoming appointments.

## Prerequisites
1. Ensure PHP and MySQL are installed on your system.
2. Verify that the 'patient_management' database exists and is populated with the required tables and data.

## File Description
- `data_collection.php`: The main PHP file that runs SQL queries and displays the collected data.

## Setup Instructions
1. Place `data_collection.php` in your web server's root directory (e.g., `htdocs` for XAMPP or `www` for WAMP).
2. Update database connection credentials in the script:
   - `$servername`: Your MySQL server hostname (default: `localhost`).
   - `$username`: Your MySQL username.
   - `$password`: Your MySQL password.
   - `$dbname`: Database name (use `patient_management`).

3. Start your local server:
   - For XAMPP: Open the XAMPP control panel and start Apache and MySQL.
   - For WAMP: Start the WAMP server.

4. Access the script in your browser:
   - URL: `http://localhost/data_collection.php`

## Expected Output
The script will display tables with the following data:
1. Patient details with diagnoses, prescriptions, and lab results.
2. Doctor-patient assignments.
3. Upcoming check-ups and surgeries.

## Troubleshooting
- **Database Connection Error**: Verify your database credentials and that the database exists.
- **No Data Displayed**: Ensure tables are populated with valid data as per the provided SQL dump.

## Notes
- This script is for data reporting purposes only and does not modify the database.
