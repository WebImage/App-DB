# Setup

In configuration, setup the following structure

## Connections

Set up named connections.  Generally, use "default" as the connection name to allow any ConnectionManager::getConnection() call to work.

Settings are compatible with Doctrine's ConnectionManager, allowing you to use the same configuration for both.

```php
[
    'database' => [
        'connections' => [
            'default|connectionName' => [ // Doctrine connection manager compatible
                'dbname'   => 'your_database_name',
                'user'     => 'your_username',
                'password' => 'your_password',
                'host'     => '127.0.0.1', // Optional
                'port'     => 3306, // Optional
                'driver'   => 'pdo_mysql'
            ]
        ]
    ]
)
```

Supported drivers include:

| Driver         | Description                              | Required PHP Extension |
| --- |------------------------------------------| --- |
| `pdo_mysql`    | MySQL / MariaDB using PDO                | `pdo_mysql`            |
| `mysqli`       | MySQL / MariaDB using MySQLi driver      | `mysqli`               |
| `pdo_pgsql`    | PostgreSQL using PDO                     | `pdo_pgsql`            |
| `pdo_sqlite`   | SQLite using PDO                         | `pdo_sqlite`           |
| `pdo_sqlsrv`   | Microsoft SQL Server using PDO           | `pdo_sqlsrv`           |
| `sqlsrv`       | Microsoft SQL Server using native driver | `sqlsrv`               |
| `oci8`         | Oracle using OCI8                        | `oci8`                 |
| `pdo_oci`      | Oracle using PDO                         | `pdo_oci`              |
| `ibm_db2`      | IBM DB2                                  | `ibm_db2`              |
| `pdo_ibm`      | IBM DB2 using PDO                        | `pdo_ibm`              |
| `pdo_firebird` | Firebird using PDO                       | `pdo_firebird`         |
| `pdo_dblib`    | SQL Server/Sybase via FreeTDS            | `pdo_dblib`            |



## Global Table Prefix

Defining a global table prefix allows you to set a prefix that will be applied to all tables unless overridden by specific table settings.

```php
[
    'database' => [
        'globalTablePrefix' => 'prefix_'
    ]
]
```  
## Table Settings

Simple table aliases can be set up as follows: 
```php
[
    'database' => [
        'tables' => [
            'tableAlias' => 'tableName'
        ]
    ]
]
```

Or more fine grain control can be achieved by specifying the table name, read and write connections, and whether to use the global prefix:
```php
[
    'database' => [
        'tables' => [
            'tableAlias' => [
                'table' => 'tableName',
                'readConnection' => 'readConnectionName',
                'writeConnection' => 'writeConnectionName',
                'useGlobalPrefix' => true|false
            ] 
        ]
    ]
]
```