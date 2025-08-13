<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# Sooqnaa Backend API

A secure Laravel-based REST API with comprehensive authentication, authorization, and security features.

## 🚀 Features

- **Secure Authentication**: Laravel Sanctum-based token authentication
- **API Versioning**: Versioned API endpoints (v1)
- **Comprehensive Security**: Rate limiting, input validation, security headers
- **Documentation**: OpenAPI/Swagger documentation
- **Health Monitoring**: Health check endpoints
- **Request Logging**: Comprehensive request/response logging
- **Error Handling**: Custom exception handling for APIs

## 🔒 Security Features

### Authentication & Authorization
- Laravel Sanctum for secure token-based authentication
- Role-based access control (admin, merchant, customer)
- Account lockout protection after failed login attempts
- Email and phone verification
- OTP-based authentication
- Password reset functionality

### Security Headers
- X-Content-Type-Options: nosniff
- X-Frame-Options: DENY
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin
- Permissions-Policy: geolocation=(), microphone=(), camera=()
- Strict-Transport-Security: max-age=31536000; includeSubDomains

### Input Validation & Sanitization
- Comprehensive form request validation
- Input sanitization with regex patterns
- SQL injection protection
- XSS protection

### Rate Limiting
- Login attempts: 5 per minute
- Registration: 5 per minute
- Password reset: 3 per minute
- OTP requests: 3 per minute

## 📋 Requirements

- PHP 8.2+
- Laravel 12.0+
- MySQL 8.0+ or PostgreSQL 13+
- Composer
- Node.js & NPM (for frontend assets)

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Sooqnaa_backend_new
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure environment variables**
   Edit `.env` file with your database and mail settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sooqnaa_backend
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   
   MAIL_MAILER=smtp
   MAIL_HOST=your_smtp_host
   MAIL_PORT=587
   MAIL_USERNAME=your_email
   MAIL_PASSWORD=your_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed the database (optional)**
   ```bash
   php artisan db:seed
   ```

7. **Generate API documentation**
   ```bash
   php artisan l5-swagger:generate
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication Endpoints

#### Register User
```http
POST /auth/register
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer"
}
```

#### Login
```http
POST /auth/login
Content-Type: application/json

{
    "email": "john.doe@example.com",
    "password": "password123"
}
```

#### Get Current User
```http
GET /auth/me
Authorization: Bearer {token}
```

#### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

#### Refresh Token
```http
POST /auth/refresh
Authorization: Bearer {token}
```

### Health Check
```http
GET /health
```

### Swagger Documentation
Access the interactive API documentation at:
```
http://localhost:8000/api/documentation
```

## 🔧 Configuration

### Security Settings
Configure security parameters in `.env`:
```env
# Authentication Security
AUTH_MAX_LOGIN_ATTEMPTS=5
AUTH_LOCKOUT_MINUTES=15
AUTH_VERIFICATION_EXPIRY=60
AUTH_OTP_EXPIRY_MINUTES=10
AUTH_OTP_LENGTH=6
AUTH_SESSION_LIFETIME_DAYS=30

# Rate Limiting
RATE_LIMIT_LOGIN=5
RATE_LIMIT_REGISTER=3
RATE_LIMIT_PASSWORD_RESET=3
```

### CORS Configuration
Configure CORS settings in `config/cors.php`:
```php
'allowed_origins' => [env('FRONTEND_URL', '*')],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📊 Monitoring

### Health Check
The API includes a comprehensive health check endpoint that monitors:
- Database connectivity
- Cache connectivity
- Storage accessibility
- Application environment

### Logging
All API requests and responses are logged with:
- Request method and URL
- IP address and user agent
- User ID (if authenticated)
- Response status and duration
- Timestamps

## 🚀 Production Deployment

### Environment Configuration
For production, ensure these settings:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
LOG_LEVEL=error
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Security Checklist
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Use HTTPS everywhere
- [ ] Configure proper database backups
- [ ] Set up monitoring and logging
- [ ] Implement proper error tracking (Sentry, Bugsnag)
- [ ] Configure rate limiting for all endpoints
- [ ] Set up proper CORS policies
- [ ] Implement API versioning
- [ ] Add request/response logging
- [ ] Set up automated security scanning

### Performance Optimization
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the API documentation at `/api/documentation`

## 🔄 Changelog

### Version 1.0.0
- Initial release with comprehensive security features
- Laravel Sanctum authentication
- API versioning
- Security headers and middleware
- Comprehensive input validation
- Rate limiting
- Health monitoring
- Request/response logging