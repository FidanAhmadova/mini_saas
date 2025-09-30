# 🚀 Mini SaaS Task Manager

A modern SaaS-style project management application built with Laravel - perfect for freelancers and small teams who need a simple alternative to Trello/Jira.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white)

## ✨ Features

### 🔐 Authentication System
- **Laravel Breeze** authentication (register/login/logout)
- **Password reset** functionality
- **Email verification** (optional)

### 💳 Subscription Plans
- **Free Plan**: 2 projects, 3 team members
- **Pro Plan**: Unlimited projects & team members, API access
- **Demo payment system** (Stripe simulation)
- **Automatic plan limit enforcement**

### 📊 Project & Task Management
- **Full CRUD operations** for projects and tasks
- **Task status tracking**: Pending, In Progress, Completed
- **Project-based task organization**
- **Plan-based access control**

### 👥 Team Collaboration
- **Email-based invitations** with secure tokens
- **Role management**: Owner & Member roles
- **Team permission system**
- **Collaborative project access**

### 🔗 REST API
- **Laravel Sanctum** token authentication
- **Mobile-ready endpoints**
- **Complete API coverage** for projects and tasks
- **Plan-based API access control**

### 🎨 Modern UI/UX
- **Tailwind CSS** professional design
- **Responsive layout** (mobile-friendly)
- **Interactive dashboard** with statistics
- **Plan comparison page**

## 🏗️ Tech Stack

- **Backend**: Laravel 12.x, PHP 8+
- **Database**: SQLite (easily configurable for MySQL/PostgreSQL)
- **Authentication**: Laravel Breeze + Sanctum
- **Frontend**: Blade Templates + Tailwind CSS
- **API**: RESTful with Laravel Sanctum
- **Queue System**: Laravel Queue (ready for Redis)

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+
- Composer
- Node.js & NPM

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/FidanAhmadova/mini_saas.git
cd mini_saas
```

2. **Install dependencies**
```bash
composer install
npm install && npm run build
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
php artisan migrate --seed
```

5. **Start the application**
```bash
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

### 🧪 Test Credentials
```
Email: demo@example.com
Password: password
```

## 📱 API Usage

### Authentication
```bash
# Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@example.com","password":"password"}'
```

### API Endpoints
```bash
# Get projects (requires Pro plan)
curl http://127.0.0.1:8000/api/projects \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get all user tasks
curl http://127.0.0.1:8000/api/my-tasks \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get project tasks
curl http://127.0.0.1:8000/api/projects/{id}/tasks \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🌟 Key Pages

- **Dashboard**: `/dashboard` - Overview with statistics
- **Projects**: `/projects` - Project management
- **Tasks**: `/tasks` - Task management
- **Subscription**: `/subscriptions/plans` - Plan comparison & upgrade
- **API Documentation**: Available endpoints listed above

## 🔧 Development

### Branch Structure
- `main` - Production-ready code
- `development` - Development branch
- `feature/*` - Feature branches
- `hotfix/*` - Hotfix branches

### Contributing
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🎯 Use Cases

Perfect for:
- **Freelancers** managing client projects
- **Small teams** needing simple project tracking
- **Startups** requiring basic task management
- **Developers** learning SaaS architecture
- **Portfolio projects** demonstrating Laravel skills

## 🔮 Future Enhancements

- [ ] Real-time notifications (Laravel Echo + Pusher)
- [ ] File upload for tasks
- [ ] Advanced reporting & analytics
- [ ] Mobile app integration
- [ ] Third-party integrations (Slack, Discord)
- [ ] Advanced team roles & permissions

---

**Built with ❤️ using Laravel and Tailwind CSS**

For questions or support, please open an issue on GitHub.