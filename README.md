# Gorm v1.0.0 Beta
一个快速构建PHP ORM类的工具

## Requirement
- PHP 5.4 + (PDO support)
- Linux Shell / Windown cmd

## Last ChangeLog [2014-09-20]
- 重新调整Building Status相关处理, 文字重新编写,增加Linux Shell环境下提示文字颜色
- 取消选项 `+v`, 调整为系统直接处理, 分别为 `Notic` `Warning` `Error`
- 修改数据库连接尝试次数: 由原来5次改成3次, 每次尝试增加3秒间隔时间
- 增加shell处理快速构建PHP ORM [具体查看 `Example`]

## Command [区分大小写]

`PHP cli模式使用 '+', Shell模式使用 '-', 建议使用Shell模式`

- `f`  Model Class保存路径, 默认保存在gorm.php相应目录下的BuildResult文件夹下
- `e`  Model Class父类 (未开启命名空间，'\\' 以 '_' 代替)
- `i`  Model Class类所需接口类 (未开启命名空间，'\\' 以 '_' 代替)
- `x`  Model Class文件后缀名, 默认 php
- `l`  Model Class文件名/类名是否保留下划线, 默认 false
- `L`  Model Class方法名是否保留下划线, 默认 true
- `m`  Model Class命名类型, 默认 1，1. %sModel  2. Model%s  3.%s_Model  4. Model_%s
- `N`  Model Class的命名空间，默认 \\
- `F`  Model Class能支持写 `final` 关键字, 默认 false
- `o`  是否开启命名空间， 默认 true
- `d`  从Config中读取的数据库配置，默认 false
- `T`  设置N个空格替代一个TAB，为0时将以TAB出现,不替换, 默认 4
- `u`  连接mysql用户名，使用此项 +d 将失效
- `p`  连接mysql密码，使用此项 +d 将失效, 不建议直接在命令行输入密码
- `h`  连接mysql主机, 默认 127.0.0.1
- `P`  连接mysql主机端口, 默认 3306
- `n`  连接mysql数据库名
- `O`  数据库驱动选项处理, 多个时用 ',' 分隔
- `t`  指定Build的表名，多个时用 ',' 分隔
- `H`  显示帮助

## Example

- 使用Shell模式
```sh
sudo ln -s /home/www/OrmBuild/gorm /usr/bin/gorm
```
```sh
gorm -f "/home/gsinhi/models" -e "\Base\Model\AbstractModel" -u root -p -n test_orm
```

- 指定保存路径
```php
php -f gorm.php +f /home/gsinhi/testOrm
```

- 指定数据库
```php
php -f gorm.php +f /home/gsinhi/testOrm +u test +p +n test_orm
```

- 关闭命名空间
```php
php -f gorm.php +f /home/gsinhi/testOrm +o false
```

- 示例配置 Config/Db.php
```php
namespace Config;
class Db extends \Config\ConfigAbstract {
    public function init() {
        return array(
            'host'     => '127.0.0.1',
            'dbname'   => 'test',
            'username' => 'test',
            'passwd'   => 'test',
            'port'     => '3306',
            'options'  => array("SET NAMES 'utf8'")
        );
    }
}
```

## ChangeLog
`[2014-09-18]`
- Linux 平台下输入密码不再回显
- 修复多个Bug
- 一些小细节调整

`[2014-09-10]`
- 增加选项 `+F`, 使Model类能支持写 `final` 关键字, 默认 false
- 字段解析相关代码大幅调整, 包括代码/代码结构, 缓冲区处理, 输出内容等
- 现在工具将自动格式化 `toArray` 类方法的代码内容(以空格符计算), 不需要手动处理
- 其它小细节调整

`[2014-09-03]`
- 定义此次版本为 v1.0.0 Beta
- 取消了Model Class父类的默认值
- 取消了从Config中读取的数据库配置的默认值
- 调整Model Class保存路径选项 `+P` 为 `+f`, 选项 `+P` 另有用途
- 调整了命名空间开启默认值为 `true`
- 调整Model Class文件名/类名是保留下划线默认值为 `false`
- 调整MySQL主机地址默认值为 `127.0.0.1`
- 调整MySQL主机端口默认值为 `3306`
- 调整显示详情的默认等级为 3
- 选项 `+h` 不再影响 `+d` 选项
- 增加对接口类写入Model Class类的支持
- 增加Model Class方法名是否保留下划线支持, 默认为 `true`
- 增加MySQL Port自定义支持, 配置选项为 `+P`
- 增加MySQL数据库驱动选项处理支持, 配置选项为 `+O`, 默认为 `SET NAMES 'utf8'`
- 增加MySQL使用选项 `+p` 流输入支持, 并且建议使用此方式
- 更改类名的合法检测, 将保证类名以[A-Z]任一字母开头
- 更新 `+H` 选项相应的输出说明
- 其它小细节调整及测试

## License
Apache License Version 2.0 http://www.apache.org/licenses/LICENSE-2.0.html
