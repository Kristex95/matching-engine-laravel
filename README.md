# Matching Engine (Laravel)
Matching Engine is a backend financial transaction processing system built with Laravel.

## Getting Started

### 1. Clone repository
bash `git clone <repository-url>` 

### 2. Create environment configuration
Copy .env.example and rename it to .env
bash `cp .env.example .env `

Update sensitive configuration values inside .env such as:
- database credentials
- application secrets
- Redis credentials

### 3. Start application with Docker
bash `docker compose up -d`

## Tech stack
- PHP 8.2
- Laravel 12
- MySQL 8
- Redis
