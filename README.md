# 8583报文解析工具

## 如何使用

### 命令行使用

所需环境

* PHP：推荐使用 PHP 5.6 或更新版本。

将代码下载到本地，进入代码根目录，输入以下命令：

```shell
php index.php ISO8583 unpack_cli 参数packet [参数length_prefix]
```

+ 参数packet：待解析的报文；
+ 参数length_prefix：报文是否包含2字节的长度前缀，1 包含，0 不包含，默认为1。

### 浏览器中使用

所需环境

* PHP：推荐使用 PHP 5.6 或更新版本。
* Web容器：可使用Nginx或Apache。

若使用Nginx，Nginx配置可参考如下示例：

```
server {
    listen       8080;
    server_name  localhost;
    root    /代码根目录路径;
    index  index.php;
    
    if ($http_user_agent ~* "spider") {
        return 404;
    }

    location = / {
        root /代码根目录路径/html;
        index index.html;
    }

    # set expiration of assets to MAX for caching
    location ~* ^/(.*)\.(html|ico|css|js|gif|jpe?g|png)(\?[0-9]+)?$ {
        rewrite ^/(.*)\.(.*)$ /$1.$2 break;
        root html;
        expires max;
        log_not_found off;
    }

    location ~* \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index     index.php;
        fastcgi_param     SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include         fastcgi_params;
    }
    
    location / {
        # Check if a file or directory index file exists, else route it to index.php.
        try_files $uri $uri/ /index.html /index.php;
    }
}
```

启动PHP-FPM、Nginx，在浏览器中输入如下地址：

```
http://localhost:8080/ISO8583/unpack?packet=待解析的报文&length_prefix=1
```

+ 参数packet：待解析的报文；
+ 参数length_prefix：报文是否包含2字节的长度前缀，1 包含，0 不包含，默认为1。

解析结果示意：

![解析结果示意](http://oatuajceb.bkt.clouddn.com/15310635220875.jpg)

## 如何定制

核心代码位置如下，可根据实际需求进行报文解析定制：

* 报文配置：application/config/iso8583_packet.php
* 报文实体类：application/models/Packet.php
* 报文解析服务类：application/services/ISO8583_service.php
* 报文解析控制器类：application/controllers/ISO8583.php
* 报文解析工具类：application/util/Packet_util.php

## 依赖的其他开源库

项目中依赖了如下开源库，在此表示感谢。

* [CodeIgniter](https://github.com/bcit-ci/CodeIgniter)
* [CodeIgniter Rest Server](https://github.com/chriskacerguis/codeigniter-restserver)
