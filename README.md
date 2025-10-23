# ⚡ StormPHP Framework

Built with love to deliver joy and satisfaction with every piece of code you write.
StormPHP is designed to be **intuitive, lightweight, and blazing fast** — the simplest app takes just **4 lines of code**.

Despite its minimal footprint (\~100KB, zero dependencies), it provides a **rich set of features** to help you build modern PHP applications.

---

## ✨ Features

* Easy to learn — start coding in minutes
* Tiny footprint (\~100KB, no dependencies)
* Blazing fast
* Built-in Dependency Injection Container
* Built-in Middleware Pipeline
* **Command Query Separation (CQS)**
* Event Dispatcher
* Multilingual (i18n) support
* Authentication & Authorization
* Logger
* Autoloader with class scanning
* Path alias system (`@templates/homepage.php`)
* Mature PHP-based view system (layouts, child views, CSS/JS injection)
* Validation & Forms
* Mailing (i18n, SMTP client)
* CLI tasks & controller execution
* End-to-end tests via PHPUnit
* Error customization
* Docker ready
* PHP 8+ support
* Works with **StormPHP Queries**

---

## 🚀 Hello World

```php
require 'vendor/autoload.php';

$app = App::create();
$app->addRoute('/', fn() => "Hello, world!");
$app->run();
```

---

## ⚡ Quick Start

### Installation

#### Composer

```bash
composer require stormmore/framework
```

In your `index.php`:

```php
require '../vendor/autoload.php';
```

#### Standalone

Download the ZIP package, extract `src/`, and include the autoloader:

```php
require 'YOUR/PATH/TO/STORM/src/autoload.php';
```

---

## 📂 Project Structure

A typical application layout:

```
my_app/
├── .cache/         # cached metadata
├── .logs/          # log files
├── public_html/    # public web root
│   ├── index.php
│   ├── app.css
│   └── app.js
├── src/            # application source
│   ├── MyController.php
│   ├── Database.php
│   └── templates/
│       └── view.php
└── README.md
```

---

## 🛠 Key Concepts

### Class Scanning

Storm automatically scans your `src/` directory for controllers, tasks, and classes.
In **production**, it uses a `.cache` file for performance.

### Autoloading

Namespaces map directly to file paths:

```php
use Infrastructure\Images\Resize;
```

→ loads `src/infrastructure/images/resize.php`.

### Routing

Controllers are discovered automatically via the `#[Route]` attribute.
No manual registration required.

### Middleware

Every request flows through a middleware pipeline.
You can plug in your own middleware at any stage.

### Aliases

Aliases make path management easier:

```php
$app->addMiddleware(AliasMiddleware::class, [
    '@templates' => "@/src/templates"
]);
```

### Configuration

Optional but recommended for larger apps:

```php
$app = App::create(directories: [
  'project' => '../',
  'source'  => '../src',
  'cache'   => '../.cache',
  'logs'    => '../.logs'
]);
```

---

## 🎮 Example Controller

```php
namespace App;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\View\View;

#[Controller]
readonly class HomepageController
{
    #[Route("/")]
    public function index(): View
    {
        return view("@templates/homepage");
    }
}
```

Supports:

* **Dependency Injection** (via constructor)
* **HTTP Methods**: `#[Get]`, `#[Post]`, `#[Put]`, `#[Delete]`
* **Query & Path Parameters**
* **Responses**: `View`, `Redirect`, JSON objects, primitives

---

## 🎨 Views

Storm views are plain PHP files, but with helpers for layouts, assets, and dynamic rendering.

**Layout Example**:

```php
<!DOCTYPE html>
<html>
<head>
    <?php $view->printCss(); ?>
    <?php $view->printJs(); ?>
    <?php $view->printTitle("StormApp"); ?>
</head>
<body>
    <main>
        <?php print_view("@templates/includes/header"); ?>
        <?= $view->content ?>
    </main>
</body>
</html>
```

**Template Example**:

```php
<?php $view->useLayout('@templates/includes/layout'); ?>

<h2>Success!</h2>
It's your first template.
```

---

## 🌍 Internationalization (i18n)

Enable multilingual support:

```php
$app->addMiddleware(LanguageMiddleware::class);
```

Example config (`settings.ini`):

```ini
i18n.multi_language = true
i18n.default_language = en-US
i18n.languages = en-US, fr-FR, de-DE, pl-PL
i18n.cookie.name = locale
i18n.translation.file_pattern = @/src/lang/%file%.ini
i18n.culture.file_pattern = @/src/lang/culture/%file%.ini
```

---

## 📖 Documentation

StormPHP is designed to feel natural and intuitive, but you can explore more in-depth examples and guides in the [docs](#) *(coming soon)*.

---

## 💡 Roadmap

* [ ] Example app repo
* [ ] CI/CD integration
* [ ] Advanced documentation website

---

## ⚖️ License

StormPHP is open-source under the [MIT License](LICENSE).

