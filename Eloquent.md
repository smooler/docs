# Eloquent

- [简介](#introduction)
- [定义模型](#defining-models)
    - [模型约定](#eloquent-model-conventions)
- [检查多个模型](#retrieving-models)
    - [集合](#collections)
    - [结果分块](#chunking-results)
- [检索单个模型或集合](#retrieving-single-models)
    - [检索集合](#retrieving-aggregates)
- [插入 & 更新模型](#inserting-and-updating-models)
    - [插入](#inserts)
    - [更新](#updates)
    - [批量赋值](#mass-assignment)
    - [其它创建方法](#other-creation-methods)
- [删除模型](#deleting-models)
    - [软删除](#soft-deleting)
    - [查询被删除的模型](#querying-soft-deleted-models)
- [查询作用域](#query-scopes)
    - [全局作用域](#global-scopes)
    - [本地作用域](#local-scopes)
- [模型对比](#comparing-models)
- [事件](#events)
    - [观察器](#observers)

<a name="introduction"></a>
## 简介

Laravel 的 Eloquent ORM 提供了漂亮、简洁的 ActiveRecord 实现来和数据库交互。每个数据库表都有一个对应的「模型」用来与该表交互。你可以通过模型查询数据表中的数据，并将新记录添加到数据表中。

在开始之前，请确保在 `config/database.php` 中配置数据库连接。更多关于数据库的配置信息，请查看 [文档](/docs/{{version}}/database#configuration)。

<a name="defining-models"></a>
## 定义模型

首先，创建一个 Eloquent 模型，生成的模型通常放在 `app\Models` 目录中，但你可以通过 `composer.json` 文件随意地将它们放在可被自动加载的地方。所有的 Eloquent 模型都`use`了 `Smooler\Traits\Eloquent` Trait。


<a name="eloquent-model-conventions"></a>
### Eloquent 模型约定

现在，我们来看一个 `Flight` 模型类的例子，我们将会用它从 `flights` 数据表中检索和存储信息：

    <?php

    namespace App\Models;

    use Smooler\Traits\Eloquent;

    class Flight
    {
        use Eloquent;
        //
    }

#### 数据表名称

请注意，我们并没有告诉 Eloquent，`Flight` 模型该使用哪一个数据表。你可以通过在模型上定义 `table` 属性，来指定自定义数据表：

    <?php

    namespace App\Models;

    use Smooler\Traits\Eloquent;

    class Flight
    {
        use Eloquent;
        protected $table = 'my_flights';
    }


#### 数据库连接

默认情况下， Eloquent 模型将使用你的应用程序配置上的数据库连接。如果你想为模型指定一个不同的连接，可以通过设置 `$connection` 属性来实现:

    <?php

    namespace App\Models;

    use Smooler\Traits\Eloquent;

    class Flight extends Model
    {
        use Eloquent;
        protected $table = 'my_flights';
        protected $connection = 'connection-name';
    }

<a name="retrieving-models"></a>
## 模型查询

一旦你创建了模型 [和他关联的数据表](/docs/{{version}}/migrations#writing-migrations), 你就可以从数据库中获取数据了. 将每一个模型想象成一个强大的查询构造器 [查询构造器](/docs/{{version}}/queries) ，你可以使用它来更快速的查询与其相关联的数据表。举个栗子:

    <?php

    use App\Models\Flight;

    $flights = Flight::get();

    foreach ($flights as $flight) {
        echo $flight['name'];
    }

#### 附加约束

Eloquent  的 `all` 方法会返回模型表中的所有结果。 由于每个Eloquent 模型充当一个[查询构造器](/docs/{{version}}/queries)，所以你也可以添加查询条件， 然后使用 `get` 方法获取查询结果:

    $flights = Flight::where([
                      ['active', '=', 1]
                   ])
                   ->orderBy('name', 'desc')
                   ->limit(10)
                   ->get();

> {tip} 因为模型也是一个查询构造器，所以你也应当阅读 [查询构造器](/docs/{{version}}/queries)提供的的一些方法，你可以在你的 Eloquent 上使用这些方法。



<a name="retrieving-single-models"></a>
## 检索单个模型／集合

除了从指定的数据表检索所有记录外，你也可以通过 `find` 方法来检索单条记录。这些方法不是返回一组模型，而是返回一个模型实例：

    // 取回符合查询限制的第一个模型...
    $flight = App\Models\Flight::where([
              ['active', '=', 1]
        ])
        ->find();


<a name="retrieving-aggregates"></a>
### 检索集合

你还可以使用 [查询构造器](/docs/{{version}}/queries) 提供的 `count` 、 `sum` 、 `max` 以及其它 [聚合函数](/docs/{{version}}/queries#aggregates) 。这些方法只会返回适当的标量值而不是整个模型实例：

    $count = App\Models\Flight::where([
              ['active', '=', 1]
        ])
        ->count();

    $max = App\Models\Flight::where([
              ['active', '=', 1]
        ])
        ->max('price');

<a name="inserting-and-updating-models"></a>
## 插入 & 更新模型



<a name="inserts"></a>
### 插入

要向数据库插入一条记录，先创建模型实例，再设置实例属性，然后调用 `save` 方法：

    <?php

    namespace App\Controllers;

    use App\Models\Flight;
    use Smooler\Traits\Controller;

    class FlightController
    {
        use Controller;
        public function store()
        {
            // 表单验证

            Flight::insert([
                'name' => $this->param('name')
            ]);
        }
    }



<a name="updates"></a>
### 更新


            Flight::where([
                'id' => 1
              ])
            ->update([
                'name' => 'New Flight Name'
            ]);












<a name="deleting-models"></a>
## 删除模型

可以在模型示例上调用 `delete` 方法来删除模型：

            Flight::where([
                'id' => 1
              ])
            ->delete();
