
[phpmvc](https://github.com/h476564406/phpmvc#phpmvc%E7%9A%84%E5%90%AF%E5%8A%A8)

[phpmvc的启动](https://github.com/h476564406/phpmvc#phpmvc%E7%9A%84%E5%90%AF%E5%8A%A8)

[效果](https://github.com/h476564406/phpmvc#phpmvc%E7%9A%84%E5%90%AF%E5%8A%A8)

# phpmvc
这是一个简陋的mvc框架。用来理解网页响应的流程和前后端沟通。

* 借这个框架使用原始的php函数来实现类的自动加载， 图片上传， 根据url分配路由等功能。。 
* 用php的session相关函数实现登录机制，来理解session对会话状态的维持。
* 用php的header函数设定http首部来测试跨域，缓存等。


# phpmvc的启动
* 拉取分支到本地
* 把sql文件import到数据库中，在app.config.php中配置好数据库信息。
 
app.config.php

```
<?php
// Db config, use mysql
return [
    "database" => [
        'host' => '127.0.0.1',
        'port' => '3333',
        'user' => 'root',
        'pwd' => '',
        'dbname' => 'phpmvc',
        'charset' => 'utf8',
    ],
];

```
* 可以使用xamp套件，修改apache的httpd.conf, 将目录指向phpmvc文件夹， 启动mysql, apache, 根据设定好的主机名在浏览器中访问， 默认为http://localhost:80

httpd.conf

```
DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/phpmvc"
<Directory "/Applications/XAMPP/xamppfiles/htdocs/phpmvc">
    Options Indexes FollowSymLinks
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>

```
# 效果
尝试注册然后登录，实现了一个登录后才能管理用户的功能。

首页

![首页](http://47.96.13.73/dist/1.png)

注册

![注册吧](http://47.96.13.73/dist/2.png)

登录了才能管理

![登录了才能管理](http://47.96.13.73/dist/3.png)
