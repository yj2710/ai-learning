# ai-learning

PHP 8.3 + PHP-FPM + host Nginx + PostgreSQL/pgvector + Redis.

## 1. 修改 Nginx 配置

编辑 `nginx/ai-learning.conf`，把：

`/Users/你的用户名/xxx/ai-learning/public`

替换成你本机 `ai-learning/public` 的绝对路径。

然后把配置软链接或复制到宿主机 Nginx 的 server 配置目录。

## 2. hosts

在宿主机 `/etc/hosts` 添加：

`127.0.0.1 ai-learning.local`

## 3. 启动 Docker

```bash
docker compose build --no-cache
docker compose up -d
```

## 4. 检查 PHP

```bash
docker compose exec php php -v
docker compose exec php php -m
docker compose exec php composer --version
```

## 5. 访问

浏览器打开：

http://ai-learning.local

PHP-FPM 映射：

宿主机 9002 -> 容器 9000

所以宿主机 Nginx 使用：

`fastcgi_pass 127.0.0.1:9002;`
