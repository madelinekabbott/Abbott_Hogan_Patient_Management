This Patient Management System (PMS) centralizes essential components of patient management, allowing healthcare providers to easily manage patient records and appointment schedules within a single interface.

--Project Overview--
The purpose of this project is to streamline the management of patient information and scheduling within healthcare settings by providing a centralized system where healthcare providers can add, edit, and manage records and appointments. This project supports CRUD operations on patient data, making it easier for providers to organize and access essential information.

--Technology Used--
The Patient Management System is built using:
PHP for backend logic and server-side processing
JavaScript for interactive elements
SQL for data management and storage
HTML & CSS for the front-end interface and styling

--Requirements--
To run this project, you will need:
- A local server environment (e.g., XAMPP, WAMP, MAMP, or LAMP) to serve PHP files and host a MySQL-compatible database.
- MySQL or a similar database system.
You can choose any setup that allows you to host a local PHP server and SQL database. Ensure that your environment supports the latest PHP version compatible with your database.

--Installation and Setup--
1. Set Up Local Server: Install and configure a local server (like XAMPP, WAMP, MAMP, or LAMP) to host the PHP files.
2. Create the Database:
  Start your server and open the SQL management tool (e.g., phpMyAdmin for XAMPP).
  Create a new database for the project.
  Import the provided SQL file (patient_management-draft.sql) to set up the necessary tables and initial data.
3. Configure Database Connection:
  Open db_connect.php in the project files.
  Update the username and password in db_connect.php to match your local database credentials:
                                                      $username = "your_database_username";
                                                      $password = "your_database_password";
4. Run the Project: Once configured, open the project in your web browser. You can access the application by navigating to [http://localhost/your-project-folder/login.php]. Example log ins for this page are:
                                                      user: 2144 password: securepass
                                                      user: 3211 password: password123

--Usage--
Once installed, the Patient Management System provides an intuitive interface where healthcare providers can:
1. Log in to view and manage patient records and appointments.
2. Access forms to create, edit, or delete patient records as needed.
3. Schedule, update, or cancel appointments within the system.                                                      
