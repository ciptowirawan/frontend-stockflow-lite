# API StockFlow Lite

Laravel 12 backend API for inventory management system.

## Tech Stack

- Laravel 12
- PHP 8.2
- Docker & Docker Compose

## Getting Started

### Prerequisites

- Docker and Docker Compose installed
- Git

### Installation

#### 1. Clone the repository
```bash
git clone <repository-url>
cd frontend-stockflow-lite
```

#### 2. Create and configure environment file
```bash
cp .env.example .env
```

#### 3. Run first-time setup (Do this BEFORE starting the app)

```bash
# Build the app container (without starting it)
docker compose build app

# Run setup commands
docker compose run --rm app php artisan key:generate
```

#### 4. Start the application
```bash
docker compose up -d app
```

### Access the Application

- **Web Interface**: http://localhost:9001

**Default Credentials:**
- Email: `admin@gmail.com`
- Password: `StriveToBeGreater`

## Daily Usage

### Start the application
```bash
docker compose up -d
```

### Stop the application
```bash
docker compose down
```

### Rebuild after changes
If you modify `composer.json` or `Dockerfile`:
```bash
docker compose up -d --build
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
