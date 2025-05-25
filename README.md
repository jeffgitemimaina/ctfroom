# Judge System

The Judge System is a PHP-based web application for managing judges, users, and scores in a competition environment. It uses MySQL (hosted on Aiven) for data storage, Docker for containerized deployment, and is deployed on Render. The application allows administrators to add/delete judges and users, submit scores, and view scoreboards, with a focus on security and usability.

## Table of Contents
- [Features](#features)
- [Setup Instructions](#setup-instructions)
- [Database Schema](#database-schema)
- [Assumptions](#assumptions)
- [Design Choices](#design-choices)
- [Future Features](#future-features)

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

#### Run Locally with Docker:
- **Ensure Docker is running.

- **Build and start:

- **docker-compose up --build

- **Access at http://localhost:80.

- **View logs in logs/app.log.

#### Deployment on Render
- **Push to GitHub:

- **git add .
- **git commit -m "Prepare for Render deployment"
- **git push origin main

- **Create Render Web Service:
- **Log in to Render.

- **Click New > Web Service.

- **Connect jeffgitemimaina/ctfroom and select main (or feature-navigation branch ).

- **Configure:
- **Runtime: Docker

### Environment Variables:

- **MYSQL_HOST=judge-mysql-judge-mysql.l.aivencloud.com
- **MYSQL_DATABASE=judge_system
- **MYSQL_USER=avnadmin
- **MYSQL_PASSWORD=<your_password>
- **MYSQL_PORT=21509

- **Disk: Optional, mount /var/www/html/logs for persistent logs.

- **Deploy and access the URL (e.g., https://ctfroom-rbj7.onrender.com).

- **Configure Aiven Firewall:
- **In Aiven Console, go to MySQL service > Access Control.


