
# mysql

- [简介](#introduction)
    - [配置](#configuration)
    - [读 & 写连接](#read-and-write-connections)
    - [使用多个数据库连接](#using-multiple-database-connections)
- [运行原生的 SQL 查询](#running-queries)
    - [监听查询事件](#listening-for-query-events)
- [数据库事务](#database-transactions)

<a name="introduction"></a>
## 简介

Smooler 内部调用swoole官方协程mysql，实现web的高并发。能使用原生 SQL和 [Eloquent ORM](/docs/{{version}}/eloquent) 在各种数据库后台与数据库进行非常简单的交互。

<a name="configuration"></a>
### 配置


数据库的配置文件放置在 `config/database.php` 文件中，你可以在此定义所有的数据库连接，并指定默认使用的连接。此文件内提供了大部分 Smooler 能支持的数据库配置示例。

你可以根据本地数据库的需要修改这个配置。


<a name="read-and-write-connections"></a>
### 读写分离

有时候你希望 SELECT 语句使用一个数据库连接，而 INSERT，UPDATE，和 DELETE 语句使用另一个数据库连接。在 Smooler 中，无论你是使用原生查询，查询构造器，或者是 Eloquent ORM，都能轻松的实现

为了弄明白读写分离是如何配置的，我们先来看个例子：

    'mysql' => [
        'read' => [
            'host' => ['192.168.1.1'],
        ],
        'write' => [
            'host' => ['196.168.1.2'],
        ],
        'sticky'    => true,
        'driver'    => 'mysql',
        'database'  => 'database',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix'    => '',
    ],

注意在以上的例子中，配置数组中增加了两个键，分别是 `read` 和 `write`。 `read` 和 `write` 的键都包含一个键为 `host` 的数组。而 `read` 和 `write` 的其他数据库都在键为 `mysql` 的数组中。

如果你想重写主数组中的配置，只需要修改 `read` 和 `write` 数组即可。所以，这个例子中：  `192.168.1.1` 将作为 「读」 连接主机，而 `192.168.1.2` 将作为 「写」 连接主机。这两个连接会共享 `mysql` 数组的各项配置，如数据库的凭据（用户名/密码），前缀，字符编码等。 

<a name="using-multiple-database-connections"></a>
### 使用多个数据库连接

当使用多个数据库连接时，你可以通过 `DB` Facade 的 `connection` 方法访问每一个连接。传递给 `connection` 方法的参数 `name` 应该是 `config/database.php` 配置文件中 connections 数组中的一个值：

    $users = DB::connection('foo')->select(...);

你也可以使用一个连接实例上的 `getPdo` 方法访问底层的 PDO 实例：

    $pdo = DB::connection()->getPdo();

<a name="running-queries"></a>
## 运行原生 SQL 查询

一旦配置好数据库连接后，便可以使用 `DB` facade 运行查询。 `DB` facade 为每种类型的查询提供了方法： `select`，`update`，`insert`，`delete` 和 `statement`。

#### 运行 Select 查询

你可以使用 `DB` Facade 的 `select` 方法来运行基础的查询语句： 

    <?php

    namespace App\Controllers;


    class UserController
    {
        public function index()
        {
            $users = DB::select('select * from users where active = ?', [1]);
        }
    }

传递给 `select` 方法的第一个参数就是一个原生的 SQL 查询，而第二个参数则是需要绑定到查询中的参数值。通常，这些值用于约束 `where` 语句。参数绑定用于防止 SQL 注入。

`select` 方法将始终返回一个数组，数组中的每个结果都是一个= `StdClass` 对象，可以像下面这样访问结果值：

    foreach ($users as $user) {
        echo $user->name;
    }

#### mysql防注入

除了使用 `?` 表示参数绑定外，你也可以使用命名绑定来执行一个查询：

    $results = DB::select('select * from users where id = :id', ['id' => 1]);



#### 运行插入语句

可以使用 `DB` Facade 的 `insert` 方法来执行 `insert` 语句。与 `select` 一样，该方法将原生 SQL 查询作为其第一个参数，并将绑定数据作为第二个参数：

    DB::insert('insert into users (id, name) values (?, ?)', [1, 'Dayle']);

#### 运行更新语句

`update` 方法用于更新数据库中现有的记录。该方法返回受该语句影响的行数：

    $affected = DB::update('update users set votes = 100 where name = ?', ['John']);

#### 运行删除语句

`delete` 方法用于从数据库中删除记录。与 `update` 一样，返回受该语句影响的行数：

    $deleted = DB::delete('delete from users');

#### 运行普通语句

有些数据库语句不会有任何返回值。对于这些语句，你可以使用 `DB` Facade 的 `statement` 方法来运行：

    DB::statement('drop table users');

<a name="database-transactions"></a>
## 数据库事务


#### 手动使用事务

如果你想要手动开始一个事务，并且对回滚和提交能够完全控制，那么你可以使用 `DB` Facade 的 `beginTransaction` 方法：

    DB::beginTransaction();

你可以使用 `rollBack` 方法回滚事务：

    DB::rollBack();

最后，你可以使用 `commit` 方法提交事务：

    DB::commit();

$mysql是swoole官方定义的mysql协程类，可如果你想查看更多$request类的方法，可以查看 swoole 官方文档，了解 [request](https://wiki.swoole.com/#/http_server)。