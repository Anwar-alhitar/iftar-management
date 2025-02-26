
# Iftar Management System (IMS)

A comprehensive solution for managing and tracking meal distributions during Ramadan, built with Laravel and Filament.

![System Overview](screenshots/dashboard.png)

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Development](#development)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## Features 🚀
- **Role-Based Access Control**
  - Admin: Full system control
  - Employee: Gender-specific beneficiary management
- **Smart Serial Numbers**
  - Auto-generated unique IDs (e.g., M-00001, F-00001)
- **Hijri Calendar Integration**
  - Automatic date conversion (Gregorian ↔ Hijri)
  - Daily meal duplication prevention
- **Real-time Reporting**
  - Daily distribution statistics
  - Beneficiary meal history
- **Advanced Search**
  - Serial number lookup
  - Multi-criteria filtering

## Requirements 📋
- PHP 8.1+
- MySQL 5.7+/MariaDB 10.3+
- Composer 2.0+
- Node.js 16+
- Redis (Optional for caching)

## Installation ⚙️

```bash
# Clone repository
git clone https://github.com/yourrepo/iftar-management.git
cd iftar-management

# Install dependencies
composer install
npm install
npm run build

# Configuration
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed
```

## Configuration ⚙️
`.env` Essentials:
```ini
APP_ENV=production
APP_DEBUG=false

DB_DATABASE=iftar_db
DB_USERNAME=root
DB_PASSWORD=

FILAMENT_ADMIN_EMAIL=admin@iftar.com
FILAMENT_ADMIN_PASSWORD=Secret123!
```

## Usage 🖥️

### Admin Dashboard
1. Access `/admin`
2. Manage users, beneficiaries, and meal distributions
3. Generate monthly reports

### Employee Workflow
1. Daily meal distribution:
   - Search beneficiaries by serial number (M-00001)
   - Automatic date validation
   - Duplication prevention

### Key Endpoints
- `/admin/beneficiaries` - Beneficiary management
- `/admin/distributions` - Meal tracking
- `/admin/reports` - Analytics dashboard

## Development 🛠️

### Tech Stack
- **Backend**: Laravel 10
- **Frontend**: Filament PHP
- **Database**: MySQL
- **Calendar**: Laravel Hijri Date

### Coding Standards
```bash
# Static analysis
php artisan insights

# Code formatting
php-cs-fixer fix
```

### API Documentation
```http
GET /api/beneficiaries/{serial}
Authorization: Bearer {token}
```
```json
{
  "data": {
    "serial": "M-00001",
    "name": "Ahmed Mohamed",
    "meals_this_month": 15
  }
}
```

## Deployment 🚀

### Production Setup
```bash
# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue workers
php artisan queue:work --daemon
```

### Security Best Practices
1. Enable HTTPS
2. Regular backups
3. Implement rate limiting
4. Use monitoring tools (Laravel Horizon)

## Contributing 🤝
1. Fork the repository
2. Create feature branch (`feat/awesome-feature`)
3. Submit PR with detailed description
4. Follow [Conventional Commits](https://www.conventionalcommits.org)

## License 📄
MIT License - See [LICENSE](LICENSE)

---

**Developed with ❤️ by Anwar Alhitar https://github.com/Anwar-alhitar **  
**Ramadan Kareem!** 🌙


