### 环境要求

- php >= 7.4

### 安装

```shell
composer require church/apollo-client
```

### 使用

```php
$client = new Client('http://127.0.0.1:8082', 'appid', 'tech.oss', 'app secret');
$result = $client->getFromCache();
$result = $client->getWithoutCache();

//必须在CLI模式下运行
$client->autoPull(['tech.oss', 'tech.baidu', 'tech.sms'], function($configuration) {
    $configuration = json_decode($configuration, true);
    $this->assertTrue(boolval($configuration));
});
```
