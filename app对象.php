
# 服务提供者

- [简介](#introduction)
- [获取核心组件](#get-component)
- [使用核心组件](#use-component)

<a name="introduction"></a>
## 简介

app对象是应用唯一的全局变量。
app对象是所有 Smooler 应用程序的引导中心。你的应用程序，以及 通过服务器引导的 Smooler 核心服务都是通过app对象引导到核心组件。

但是，「核心组件」是什么意思呢？ 通常，我们可以理解为**功能实现类**，比如实现类单例化，协程上下文内容，中间件，甚至是路由。app对象是配置应用程序的中心。

本篇你将会学到如何获取app对象核心组件，并将其使用到你的 Smooler 应用程序中

<a name="get-component"></a>
## 获取核心组件

所有的核心组件都会在app对象构造函数中全部实例化，并保存在app对象的属性中。 大多组件都是用来实现内核的基础功能。
在 app对象中，你只需要获取对应组件的app属性就能实现。千万不要尝试在app对象中新增或者覆盖任何监听器，路由，或者其他属性。否则，你可能会意外地使用到尚未加载的app对象提供的组件。

让我们来看一个基础的核心组件。在任何核心组件方法中，你总是通过 `global $app` 属性来获取：
    
    <?php

    global $app;
    $config = $app->config;


<a name="use-component"></a>
## 核心组件的使用

<a name="config-component"></a>
### config组件

用以管理Smooler所有配置信息的组件。您可以在根目录的`cofing`目录中自定义配置。
    <?php

    global $app;
    $config = $app->config;
    $env = $config->get('app.env');

config只是定义了一个 `get` 方法，并且使用这个方法来获取相应的配置。

<a name="constant-component"></a>
### constant组件

用以管理Smooler所有常量的组件。您可以在根目录的`constant`目录中自定义常量。
constant在Http 核心实例化的时候，会自动定义`constant`目录下所有文件的常量值。
