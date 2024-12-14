# Employee Record Management System

## Overview
This project enhances the security of an existing Employee Record Management System, which I sourced from the internet. Initially, the system provided basic CRUD (Create, Read, Update, Delete) functionality for managing employee records but lacked essential security features. My aim was to improve its security posture significantly while maintaining its core functionality.

## Key Features
- **Admin User Role**: Establishes proper access control and privilege management.
- **Two-Factor Authentication (2FA)**: Adds an extra layer of security for user accounts.
- **reCAPTCHA Integration**: Protects against automated attacks and spam submissions.
- **Secure Cookie Handling**: Enhances cookie security to protect session data.
- **Improved Session Management**: Mitigates vulnerabilities related to session handling and user authentication.
- **CSRF Token Protection**: Safeguards against Cross-Site Request Forgery attacks.

## Technologies Used
- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Libraries/Frameworks**:
  - PHPMailer for secure email notifications
  - Google reCAPTCHA for bot protection

## Security Implementations
- **Password Hashing**: Utilizes `password_hash()` for secure password storage.
- **Input Validation**: Implements `filter_input()` and `preg_match()` to sanitize user inputs.
- **Prepared Statements**: Uses prepared statements with `mysqli` to prevent SQL injection attacks.
- **Session Management**: Employs secure cookies with HttpOnly and Secure flags.

## Installation
1. Clone the repository to your local machine.
2. Set up a PHP environment (7.4+ recommended) with MySQL.
3. Import the provided SQL file to create the database schema.
4. Configure the database connection settings in `config.php`.
5. Set up Google reCAPTCHA and update the site key in the relevant files.

## Usage
- Admins can manage employee records, including creating, updating, and deleting entries.
- Users can log in securely using 2FA and submit forms protected by reCAPTCHA.

## Testing
The application has undergone rigorous testing, including:
- Functional Testing of key security features like 2FA, CSRF protection, and reCAPTCHA integration.
- Static Application Security Testing (SAST) to identify potential vulnerabilities.

## Future Improvements
- Implement stronger password requirements.
- Add additional logging for security events.
- Enhance user interface for better usability.
