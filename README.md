## Batch Mysql and PostgreSQL dumper.

This class creates dump archives for MySQL and PostgreSQL databases. You can create dumps for all databases including system, without system databases or own specific database list.

---
### Setup

```php

include ('src/DataBasesDumper.php');

$mysql = new DataBasesDumper();
$mysql->setDbType('mysql');
$mysql->setTargetDirectory('../../testy/mysql_dump');
$mysql->setConnectionData('mysql:host=localhost', 'marcin', 'ralf96');
```
