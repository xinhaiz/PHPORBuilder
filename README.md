# Gorm
一个快速构建PHP ORM类的工具

## Requirement
- PHP 5.4 + (PDO support)
- Linux Shell / Windown cmd (window 未测试)

## Command
- `+f`  Model Class保存路径, 默认保存在work.php相应目录下的BuildResult文件夹下';
- `+e`  Model Class父类 (未开启命名空间，\'\\\' 以 \'_\' 代替)';
- `+i`  Model Class类所需接口类 (未开启命名空间，\'\\\' 以 \'_\' 代替)';
- `+x`  Model Class文件后缀名, 默认 php';
- `+l`  Model Class文件名/类名是否保留下划线, 默认 false';
- `+L`  Model Class方法名是否保留下划线, 默认 true';
- `+m`  Model Class命名类型, 默认 1，1. %sModel  2. Model%s  3.%s_Model  4. Model_%s';
- `+N`  Model Class的命名空间，默认 \\';
- `+o`  是否开启命名空间， 默认 true';
- `+d`  从Config中读取的数据库配置，默认 false';
- `+T`  设置N个空格替代一个TAB，为0时将以TAB出现,不替换, 默认 4';
- `+u`  连接mysql用户名，使用此项 +d 将失效';
- `+p`  连接mysql密码，使用此项 +d 将失效, 不建议直接在命令行输入密码';
- `+h`  连接mysql主机, 默认 127.0.0.1';
- `+P`  连接mysql主机端口, 默认 3306';
- `+n`  连接mysql数据库名';
- `+O`  数据库驱动选项处理, 多个时用 \',\' 分隔';
- `+t`  指定Build的表名，多个时用 \',\' 分隔';
- `+v`  显示详情[1-3]，默认 3';
- `+H`  显示帮助';

## Example

- 指定保存路径
```php
php -f gorm.php +P /home/gsinhi/testOrm +v 1
```

- 指定数据库
```php
php -f gorm.php +P /home/gsinhi/testOrm +u test +h localhost +p 123456 +n test_orm +v 3
```

- 关闭命名空间
```php
php -f gorm.php +P /home/gsinhi/testOrm +o 0
```

- 示例配置 Config/Db.php
```php
namespace Config;
class Db extends \Config\ConfigAbstract {
    // 不提供 options 配置， 如：SET NAMES ‘utf8’
    // 程序默认处理了 SET NAMES ‘utf8’
    public function init() {
        return array(
            'host'     => 'localhost',
            'dbname'   => 'test_orm',
            'username' => 'test',
            'passwd'   => '123456'
        );
    }
}
```

## License
Apache License Version 2.0 http://www.apache.org/licenses/LICENSE-2.0.html
