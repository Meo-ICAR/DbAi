# Database AI Assistant

<p align="center">
  <img src="https://img.icons8.com/color/96/000000/database.png" alt="Database AI Assistant Logo" width="120">
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#requirements">Requirements</a> •
  <a href="#installation">Installation</a> •
  <a href="#usage">Usage</a> •
  <a href="#screenshots">Screenshots</a> •
  <a href="#contributing">Contributing</a> •
  <a href="#license">License</a>
</p>

## 🚀 Features

- **Natural Language to SQL**: Convert plain English / Italian queries into SQL commands
- **Interactive Query Builder**: Visual interface for building complex queries
- **Query History**: Save, manage, and reuse your queries
- **Data Visualization**: Generate charts and graphs from query results
- **Multi-Database Support**: Connect to various database systems
- **User Management**: Secure authentication and authorization
- **Dashboard**: Customizable dashboard for your favorite queries

## 🛠️ Requirements

- PHP 8.1 or higher
- Composer
- MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.8.8+
- Node.js 16+ and NPM
- Laravel 10.x

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/dbai.git
   cd dbai
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install NPM dependencies:
   ```bash
   npm install
   npm run build
   ```

4. Create a copy of the .env file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database and insert GEMINI API KEY in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dbai
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   AI_DRIVER=gemini
   GEMINI_API_KEY=<your-gemini-api-key>
    GEMINI_MODEL=gemini-2.5-flash
    GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1
    AI_DEBUG=true

7. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

9. Visit `http://localhost:8000` in your browser

## 📸 Screenshots

### Query Interface
![Query Interface](public/images/screenshots/query-interface.png)

### Dashboard
![Dashboard](public/images/screenshots/dashboard.png)

### Query Results
![Query Results](public/images/screenshots/query-results.png)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework For Web Artisans
- [Tailwind CSS](https://tailwindcss.com) - A utility-first CSS framework
- [Alpine.js](https://alpinejs.dev) - A rugged, minimal framework for composing JavaScript behavior in your markup

## 📧 Contact

Your Name - [@HassistoDev](https://twitter.com/HassistoDev - info@hassisto.com

Project Link: [https://github.com/Meo-ICAR/DbAI.git  (https://github.com/Meo-ICAR/DbAI.git)
