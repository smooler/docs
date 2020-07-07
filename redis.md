# Redis

- [简介](#introduction)
    - [配置](#configuration)
- [Redis 交互](#interacting-with-redis)
- [发布 / 订阅](#pubsub)

<a name="introduction"></a>
## 简介

Smooler 内部调用swoole官方协程redis，实现web的高并发。


[Redis](https://redis.io) 是一个开源的, 高级键值对存储数据库。由于它包含 [字符串](https://redis.io/topics/data-types#strings), [哈希](https://redis.io/topics/data-types#hashes), [列表](https://redis.io/topics/data-types#lists), [集合](https://redis.io/topics/data-types#sets), 和 [有序集合](https://redis.io/topics/data-types#sorted-sets) 这些数据类型，所以它通常被称为数据结构服务器。

<a name="configuration"></a>
### 配置

Laravel 应用的 Redis 配置都在配置文件 `config/database.php` 中。在这个文件里，你可以看到  `redis` 数组里面包含了应用程序使用的 Redis 服务器：

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

默认的服务器配置应该足以进行开发。当然，你也可以根据使用的环境来随意更改这个数组。只需在配置文件中给每个 Redis 服务器指定名称、host 和 port 即可。


<a name="interacting-with-redis"></a>
## Redis 交互


你可以调用 `Smooler\Caches\Redis` [facade](/docs/{{version}}/facades)上的各种方法来与 `Redis` 进行交互。`Redis` facade 支持动态方法，这意味着你可以在 facade 上调用任何 [Redis 命令](https://redis.io/commands)， 还能将该命令直接传递给 `Redis`。 在本例中，通过调用 `Redis` facade 上的  `GET` 方法来调用 Redis 的 `get` 命令：

    <?php

    namespace App\Controllers;

    use Smooler\Caches\Redis

    class UserController 
    {
        public function showProfile($id)
        {
            $user = Redis::get('user:profile:'.$id);
            printr($user);
        }
    }

也就是说，你可以在 `Smooler\Caches\Redis`  facade 上调用任何的 Redis 命令。Smooler 使用魔术方法将传递命令给 Redis 服务器，因此只需传递 Redis 命令所需的参数即可：

    Redis::set('name', 'Taylor');

    $values = Redis::lrange('names', 5, 10);

#### 使用多个 Redis 连接

你可以通过 `Redis::connection` 方法来获取 Redis 实例：

    $redis = Redis::connection();

这会返回一个默认的 redis 服务器的实例。你也可以将连接或者集群的名称传递给 `connection` 方法，来获取在 Redis 配置文件中定义的特定的服务器或者集群：

    $redis = Redis::connection('my-connection');


<a name="pubsub"></a>
## 发布与订阅

Smooler 为 Redis 的 `publish` 及`subscribe` 提供了方便的接口。这些 Redis 命令让你可以监听指定「频道」上的消息。你可以从另一个应用程序发布消息给另一个应用程序，甚至使用其它编程语言，让应用程序和进程之间能够轻松进行通信。

首先，我们使用 `subscribe` 方法设置频道监听器。我们将这个方法调用放在 [Artisan 命令](/docs/{{version}}/artisan) 中，因为调用  `subscribe` 方法会启动一个长时间运行的进程：

    <?php

    namespace App\Console\Commands;

    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Redis;

    class RedisSubscribe extends Command
    {
        /**
         * 控制台命令的名称和签名。
         *
         * @var string
         */
        protected $signature = 'redis:subscribe';

        /**
         * 控制台命令说明。
         *
         * @var string
         */
        protected $description = 'Subscribe to a Redis channel';

        /**
         * 执行控制台命令。
         *
         * @return mixed
         */
        public function handle()
        {
            Redis::subscribe(['test-channel'], function ($message) {
                echo $message;
            });
        }
    }

现在我们可以使用 `publish` 方法将消息发布到频道：

    Route::get('publish', function () {
        // Route logic...

        Redis::publish('test-channel', json_encode(['foo' => 'bar']));
    });

$redis是swoole官方定义的redis协程类，可如果你想查看更多$redis类的方法，可以查看 swoole 官方文档，了解 [redis](https://wiki.swoole.com/#/http_server)。