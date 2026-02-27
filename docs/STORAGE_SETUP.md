# Storage Symlink Setup

## Development (PHP Built-in Server)

Run server from project root (NOT from public/):

```bash
cd /path/to/php-cms
php -S localhost:8000
```

The `public/index.php` handles `/storage/*` requests automatically.

## Production (Apache)

Create symlink:
```bash
cd public
ln -s ../storage/uploads storage
```

Or use X-Sendfile:
```apache
<Directory "/path/to/php-cms/public">
    XSendfile On
    XSendFilePath /path/to/php-cms/storage/uploads
</Directory>
```

## Production (Nginx)

```nginx
location /storage/ {
    alias /path/to/php-cms/storage/uploads/;
    
    # Optional: Protect files
    # auth_basic "Private";
    # auth_basic_user_file /etc/nginx/.htpasswd;
}
```

## Production (Symfony/Apache)

If using Symfony/Apache, the symlink approach works:
```bash
cd public
ln -s ../storage/uploads storage
```

Make sure `FollowSymLinks` is enabled in Apache config.
