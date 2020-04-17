## Batch dump for MySQL and PostgreSQL databases.

This class creates dump archives for MySQL and PostgreSQL databases. You can create dumps for all databases including system, without system databases or own specific database list.

### Setup

```php
include ('src/DataBasesDumper.php');

//example for MySQL
$mysql = new DataBasesDumper();
$mysql->setDbType('mysql');
$mysql->setTargetDirectory('path_to_archive_folder');
$mysql->setConnectionData('mysql:host=host', 'user', 'password');

//example for PostgreSQL
$postgresql= new DataBasesDumper();
$postgresql->setDbType('postgresql');
$postgresql->setTargetDirectory('path_to_archive_folder');
$postgresql->setConnectionData("pgsql:dbname=db_name;host=host", 'user', 'password' );
```
