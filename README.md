# Library.Book Management System

A web-based library management system built using Native PHP, JavaScript, and Tailwind CSS. This application helps manage books, users, and borrowing activities with a simple and responsive interface.

## Features

- Book management (CRUD)
- User management
- Borrow and return books
- Search books
- Responsive interface
- Authentication system (if available)
- Dashboard management

---

## Tech Stack

### Backend

- Native PHP
- PostgreSQL

### Frontend

- HTML5
- JavaScript
- Tailwind CSS

---

## Installation

Clone repository:

```bash
git clone <repository-url>
cd library_book
```

Configure database connection:

Open:

```txt
config/database.php
```

Adjust:

```php
$host = 'localhost';
$port = '5432';
$dbname = 'your_database';
$user = 'your_username';
$password = 'your_password';
```

Import database:

```txt
database.sql
```

Start local server:

Using XAMPP:

```txt
Move project to:
htdocs/library.book
```

Open:

```txt
http://localhost/library.book
```

Or use PHP server:

```bash
php -S localhost:8000
```

Then visit:

```txt
http://localhost:8000
```

---

## Project Structure

```txt
config/
function/
images/
public/
```

---

## Main Features

### Book Management

- Add books
- Update books
- Delete books
- Search books

### Borrowing System

- Borrow books
- Return books
- Track borrowing status

### User Management

- Manage users
- Authentication

---

## Future Improvements

- Fine calculation system
- Export reports
- Multi-role access

---

## License

This project was built for learning, portfolio development, and practice purposes.
