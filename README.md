# Hydrogen
![representation of a hydrogen atom](./template/logo.png)

## Library of basic building blocks for an xAMP app.

Reusable code for page layout, building and executing SQL statements, creating data-driven HTML tables, authentication, and so on.

> [!IMPORTANT]
> ## Requirements
>* PHP with SQLite3 enabled. (MySQL and Oracle are configurable options)
>* [Composer](https://getcomposer.org/download/) for PHPMailer installation (Fear not, installation is painless).
>* [PHPMailer](https://github.com/PHPMailer/PHPMailer) for email support (likewise).

> [!NOTE]
>## Setup /installation
>* Clone or copy the files into a "Hydrogen" folder in the application root.
>* For usage examples that include a setup walkthrough, copy or link the files in "/template" to the application root as well, and then point a browser to the root (or to index.php).
>* Install PHPMailer in the same application root (not in the Hydrogen folder): <code>composer require phpmailer/phpmailer</code> Run the template application or see the <code>Hydrogen/docs</code> folder for further configuration instructions (SMTP host, etc).

## Changes planned for release 2:
* Use JWT cookie for persistent login
* Add external stylesheet link to page template
* documentation
* role-based access control

## Credits
* Logo from https://www.flaticon.com/free-icons/hydrogen
* Responsive template by [w3schools](https://www.w3schools.com/w3css/tryit.asp?filename=tryw3css_examples_home)
