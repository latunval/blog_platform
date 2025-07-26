# Blog Platform

A simple PHP-based blog platform with user authentication, post creation, comments, and likes.

## Features

- User registration and login
- Create, edit, and delete blog posts
- Comment on posts
- Like/unlike posts and comments
- User dashboard with statistics
- Responsive Bootstrap design

## Setup Instructions

1. **Database Setup**
   - Create a MySQL database named `blog_platform`
   - Update database credentials in `config/db_connect.php` if needed
   - Run `setup.php` in your browser to create tables and sample user

2. **Sample Login**
   - Email: admin@example.com
   - Password: admin123

3. **File Structure**
   ```
   Blog/
   ├── config/
   │   └── db_connect.php
   ├── includes/
   │   ├── header.php
   │   └── footer.php
   ├── CSS/
   │   └── style.css
   ├── JS/
   │   └── script.js
   ├── index.php
   ├── login.php
   ├── signUp.php
   ├── dashboard.php
   ├── create_post.php
   ├── edit.php
   ├── view_post.php
   ├── delete_post.php
   ├── interact.php
   ├── like.php
   ├── comment.php
   ├── logout.php
   ├── database.sql
   └── setup.php
   ```

## How to Use

1. **Registration**: New users can register at `signUp.php`
2. **Login**: Users can login at `login.php`
3. **Dashboard**: View posts and statistics at `dashboard.php`
4. **Create Posts**: Create new posts at `create_post.php`
5. **View Posts**: View all published posts on the homepage
6. **Interact**: Like and comment on posts

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS prevention with `htmlspecialchars()`
- Session-based authentication
- Input sanitization

## Technologies Used

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5.3
- JavaScript (ES6+)
- HTML5/CSS3

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest) 