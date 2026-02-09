# Student-Registration-System Manager

A comprehensive PHP & MySQL web application designed to manage student records, track registrations, and generate academic profile reports. This system provides a full CRUD (Create, Read, Update, Delete) interface with a dedicated management dashboard.

🚀 Features
-Student Registration: Securely register new students into the system.

-Management Dashboard: A centralized hub to view and manage all student data.

-Full CRUD Functionality: Ability to update student information and delete records with automated logging.

-PDF Report Generation: Generates professional Profile Summary and Registration Confirmation reports (as seen in the Thabang Mohale examples).

-Activity Logging: Tracks deletions in deleted_log.txt for administrative oversight.

🛠️ Tech Stack
Backend: PHP
Database: MySQL
Frontend: HTML, CSS (styles.css), JavaScript (StudentDashboard.js)
Server Logic: Node.js (server.js) — for real-time features and API funtionality.

📁 Project Structure
-index.php: The main entry point/login page.

-student_management_dashboard.php: The primary administrative interface.

-database.php: Handles the connection logic to the MySQL database.

-delete_function.php & update_student_info.php: Backend logic for data manipulation.

⚙️ Installation & Setup
Clone the repository:

Bash
git clone https://github.com/ThabangJR/Student-Registration-System.git

Database Setup:

Import the provided SQL schema into your MySQL environment (e.g., XAMPP/WAMP).

Configure your credentials such as username, host, password etc. in database.php.

Run the App:

Place the folder in your htdocs or www directory.

Navigate to http://localhost/StudentRegisterAcademicManager/index.php.

🔒 Security Note
Ensure that database.php is secured and not sharing plain-text credentials in public environments.

Sensitive student PDFs should be stored in a protected directory.
