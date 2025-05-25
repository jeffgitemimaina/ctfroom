# Judge System

The Judge System is a PHP-based web application for managing judges, users, and scores in a competition environment. It uses MySQL (hosted on Aiven) for data storage, Docker for containerized deployment, and is deployed on Render. The application allows administrators to add/delete judges and users, submit scores, and view scoreboards, with a focus on security and usability.


## Features
- Add and delete judges and users with CSRF protection and rate limiting.
- Submit scores (1-100 points) for users by judges.
- Admin dashboard for managing competition data.
- Judge portal for score submission.
- Scoreboard to display rankings.
- User-friendly navigation menu in `index.php`.
- Secure database connection with SSL via Aiven MySQL.
- Action and error logging in `logs/app.log`.

## Setup Instructions

### Prerequisites
- **Git**: Version control (`git --version`).
- **Docker**: Containerized deployment (`docker --version`).
- **MySQL Client**: For database setup (e.g., MySQL CLI or Workbench).
- **Aiven Account**: For MySQL hosting.
- **Render Account**: For deployment.
- **PHP 8.2**: Optional for local development without Docker.
- **Text Editor**: For editing files (e.g., VS Code).

### Local Setup
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/jeffgitemimaina/ctfroom.git
   cd ctfroom
### Configure Aiven MySQL:
 - **Log in to Aiven Console.

- **Create a MySQL service (e.g., judge-mysql).

- **Note connection details:
- **Host: judge-mysql-judge-mysql.l.aivencloud.com

- **Port: 21509

- **Database: judge_system

- **User: avnadmin

- **Password: <your_password>

- **Download ca.pem from Aiven and save to ssl/ca.pem.

- **Ensure ssl/ca.pem is committed to the repository.

- **Set Environment Variables:
- **Create .env in the project root:

- **MYSQL_HOST=judge-mysql-judge-mysql.l.aivencloud.com
- **MYSQL_DATABASE=judge_system
- **MYSQL_USER=avnadmin
- **MYSQL_PASSWORD=<your_password>
- **MYSQL_PORT=21509



### Initialize Database:
- **Connect to Aiven MySQL

- **mysql -h judge-mysql-judge-mysql.l.aivencloud.com -P 21509 -u avnadmin -p

- **Run the schema and test data (see Database Schema (#database-schema)).

### Run Locally with Docker:
- **Ensure Docker is running.

- **Build and start:

- **docker-compose up --build

- **Access at http://localhost:80.

- **View logs in logs/app.log.

### Deployment on Render
- **Push to GitHub:

- **git add .
- **git commit -m "Prepare for Render deployment"
- **git push origin main

- **Create Render Web Service:
- **Log in to Render.

- **Click New > Web Service.

- **Connect jeffgitemimaina/ctfroom and select main (or feature-navigation branch ).


### Runtime: Docker
- **Environment Variables:

- **MYSQL_HOST=judge-mysql-judge-mysql.l.aivencloud.com
- **MYSQL_DATABASE=judge_system
- **MYSQL_USER=avnadmin
- **MYSQL_PASSWORD=<your_password>
- **MYSQL_PORT=21509

- **Disk: Optional, mount /var/www/html/logs for persistent logs.

- **Deploy and access the URL (e.g., https://ctfroom-rbj7.onrender.com).

- **Configure Aiven Firewall:
- **In Aiven Console, go to MySQL service > Access Control.

# Assumptions

- **No Authentication**: The application lacks user login or role-based access control. Pages like `admin.php` and `manage_users.php` are publicly accessible, which may pose security risks.
- **Aiven MySQL**: Database is hosted on Aiven with SSL required (`ssl-mode=REQUIRED`).
- **Docker Deployment**: Local and Render setups use Docker with `Dockerfile` and `docker-compose.yml`.
- **Security Features**: `csrf.php` and `rate_limit.php` are required for all forms.
- **Single Database**: All operations use `judge_system`, not `defaultdb`.
- **Public SSL Certificate**: `ssl/ca.pem` is committed as it’s not sensitive.
- **Render Hosting**: Deployment is on Render with automatic scaling.

---

# Design Choices

## Database Structure

- **Judges**: `username` (unique) and `display_name` allow flexible identification.
- **Users**: Simple `name` field for competitors.
- **Scores**: Links `judge_id` and `user_id` with `points` (1–100) and `created_at` for tracking. Foreign keys with `ON DELETE CASCADE` maintain referential integrity.

**Why**: Normalized tables reduce redundancy and support efficient queries (e.g., scoreboards).

## PHP Constructs

- **PDO**: Secure database access with prepared statements to prevent SQL injection.
- **Conditional Session Start**: `if (session_status() === PHP_SESSION_NONE)` avoids session conflicts.
- **CSRF Protection**: `csrf.php` generates/validates tokens to prevent cross-site request forgery.
- **Rate Limiting**: `rate_limit.php` limits requests (10/min/IP) to prevent abuse.

**Why**: Prioritizes security, scalability, and maintainability.

## Navigation

- `index.php` uses a clean HTML menu with CSS styling for usability.

## Docker

- Ensures consistent PHP 8.2 and Apache environments.

## Logging

- `logs/app.log` captures actions/errors for debugging.

---

# The following are Future Features I will add
- **Authentication System**: Implement login with roles (`admin`, `judge`, `viewer`) to secure `admin.php`, `manage_users.php`, etc.
- **Score Editing**: Allow updating existing scores.
- **Pagination**: Add for large lists (e.g., scoreboards, user lists).
- **REST API**: Provide endpoints for score submission or data retrieval.
- **Frontend Enhancements**: Use a framework like Vue.js for dynamic interfaces.
- **Notifications**: Email alerts for new scores or user changes.

