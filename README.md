# Font Group System

Live Demo: http://fontgroupzepto.rf.gd/

A web application to create and manage font groups using uploaded TrueType Font (TTF) files. The application allows users to upload fonts, create groups with custom font names, and manage these groups—all without page reloads.

## Table of Contents

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Project Structure](#project-structure)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Font Uploading**: Upload TTF font files via a drag-and-drop interface or by clicking to select files.
- **Font Preview**: View a list of uploaded fonts with a preview of each font style.
- **Font Group Creation**: Create font groups by selecting multiple fonts and assigning custom names.
- **Group Management**: Edit and delete existing font groups.
- **Responsive UI**: Built using Bootstrap for responsive design.
- **Single-Page Application**: Uses AJAX to perform actions without page reloads.

## Prerequisites

Before you begin, ensure you have met the following requirements:

- **Operating System**: Windows, macOS, or Linux
- **Web Server**: Apache or Nginx
- **PHP**: Version 7.2 or higher
- **MySQL**: Version 5.7 or higher
- **Composer**: For dependency management (optional but recommended)

## Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/yourusername/font-group-system.git
   cd font-group-system
   ```

2. **Install Dependencies**

   If you're using Composer:

   ```bash
   composer install
   ```

   Otherwise, ensure you have the required PHP extensions installed.

3. **Set Up Virtual Host (Optional)**

   Configure your web server to serve the project directory. For Apache, add a virtual host configuration.

## Database Setup

### 1. Create the Database and Tables

You can create the required database and tables using the provided SQL script. You can execute this script using a tool like phpMyAdmin, MySQL Workbench, or via the command line.

**SQL Script:**

```sql
-- Create the database
CREATE DATABASE IF NOT EXISTS `font_system`;
USE `font_system`;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `font_group_fonts`;
DROP TABLE IF EXISTS `font_groups`;
DROP TABLE IF EXISTS `fonts`;

-- Create the fonts table
CREATE TABLE `fonts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- Create the font_groups table
CREATE TABLE `font_groups` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- Create the font_group_fonts table
CREATE TABLE `font_group_fonts` (
  `group_id` INT NOT NULL,
  `font_id` INT NOT NULL,
  `custom_font_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`group_id`, `font_id`),
  INDEX `idx_font_id` (`font_id`),
  INDEX `idx_group_id` (`group_id`),
  CONSTRAINT `fk_font_group` FOREIGN KEY (`group_id`) REFERENCES `font_groups`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_font` FOREIGN KEY (`font_id`) REFERENCES `fonts`(`id`)
);
```

### 2. Import the SQL Script

- **Using Command Line:**

  ```bash
  mysql -u your_username -p < database.sql
  ```

- **Using phpMyAdmin:**

  - Log in to phpMyAdmin.
  - Create a new database named `font_system`.
  - Select the database and navigate to the "Import" tab.
  - Upload the SQL script file and execute.

### 3. Database Configuration

Create a configuration file named `config.php` in the `includes` directory with the following content:

```php
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'font_system');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

Replace `your_username` and `your_password` with your MySQL credentials.

## Configuration

Ensure the following directories exist and have appropriate permissions:

- **Font Uploads Directory**

  Create a directory named `fonts` in the project root to store uploaded font files.

  ```bash
  mkdir fonts
  chmod 755 fonts
  ```

- **Assets Directory**

  Ensure the `assets` directory contains the required images:

  - `upload_icon.png`: Used in the font upload section.
  - `cross.png`: Used as the remove icon in the font group form.

## Running the Application

1. **Start Your Web Server**

   - For Apache:

     ```bash
     sudo service apache2 start
     ```

   - For PHP's Built-in Server (for development purposes):

     ```bash
     php -S localhost:8000
     ```

2. **Access the Application**

   Open your web browser and navigate to `http://localhost/font-group-system/` or `http://localhost:8000/` if using PHP's built-in server.

## Project Structure

```
font-group-system/
├── assets/
│   ├── cross.png
│   └── upload_icon.png
├── css/
│   └── bootstrap.min.css
├── fonts/
│   └── [Uploaded font files]
├── includes/
│   ├── config.php
│   ├── Database.php
│   ├── Font.php
│   └── FontGroup.php
├── js/
│   ├── bootstrap.bundle.min.js
│   └── jquery.min.js
├── index.php
├── upload_font.php
├── get_fonts.php
├── create_font_group.php
├── get_font_groups.php
├── get_font_group.php
├── update_font_group.php
├── delete_font.php
├── delete_font_group.php
└── README.md
```

## Usage

### 1. Upload Fonts

- Click on the upload area or drag and drop `.ttf` files.
- The uploaded fonts will appear in the "Our Fonts" list with a preview.

### 2. Create a Font Group

- Scroll to the "Create Font Group" section.
- Enter a group name in the "Group Name" field.
- Add at least two fonts by clicking "+Add Row" and selecting fonts from the dropdown.
- Assign custom names to each font.
- Click the "Create" button to save the group.

### 3. Manage Font Groups

- Existing font groups are listed under "Font Groups".
- **Edit**: Click "Edit" to modify a group. Make changes and click "Update" or "Cancel" to discard changes.
- **Delete**: Click "Delete" to remove a group.

### 4. Delete Fonts

- In the "Our Fonts" list, click "Delete" next to a font to remove it.
- Note: Deleting a font that's part of a group may affect the group.

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Create a new Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Note:** This application is built as part of an assignment to demonstrate proficiency in PHP and JavaScript. It focuses on functionality over design, adhering to SOLID principles, and uses Core PHP with jQuery and Bootstrap.
