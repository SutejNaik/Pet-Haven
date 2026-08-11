\# Pet Haven



Pet Haven is a web-based pet adoption platform developed using PHP and MySQL. The platform allows users to browse available pets, register and log in, post pets for adoption, and submit adoption requests.



\## Features



\* User registration and login

\* Browse available pets

\* Pet details and adoption information

\* Post pets for adoption

\* Submit adoption requests

\* Adoption request management

\* Admin panel

\* Contact/message system

\* MySQL database integration



\## Technologies Used



\* PHP

\* MySQL

\* HTML

\* CSS

\* JavaScript

\* XAMPP



\## Project Structure



```text

Pet-Haven/

├── admin/

├── adoption/

├── assets/

├── includes/

├── pets/

├── users/

├── REPORT/

├── index.php

├── abt.php

├── contact.php

└── pet\_haven.sql

```



\## Running Locally



1\. Install XAMPP.

2\. Start Apache and MySQL.

3\. Copy the project into the XAMPP `htdocs` directory.

4\. Create a MySQL database named `sutej`.

5\. Import `pet\_haven.sql` using phpMyAdmin.

6\. Check the database configuration in `includes/config.php`.

7\. Open the project through the local XAMPP server.



Example:



```text

http://localhost/pet\_haven/

```



\## Database Configuration



The project currently uses the local XAMPP MySQL configuration:



```php

$host = "localhost";

$user = "root";

$pass = "";

$db = "sutej";

```



For production deployment, these values should be changed to the credentials provided by the hosting provider.



\## Project Purpose



Pet Haven was developed as an academic web development project to demonstrate PHP, MySQL, authentication, CRUD operations, database relationships, and a complete pet adoption workflow.



