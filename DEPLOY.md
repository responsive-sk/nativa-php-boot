# Production Deployment Checklist

## 📋 Pre-deployment

### 1. Generate APP_KEY
```bash
php -r "echo bin2hex(random_bytes(32));"
# Copy the output and add to .env.production
```

### 2. Update .env.production
```bash
# Edit these values:
APP_URL=https://api.responsive.sk
ADMIN_PASSWORD=YourSecurePassword123!
APP_KEY=your-generated-key-here
```

---

## 🚀 Deployment Steps

### Step 1: Build Locally
```bash
cd /home/evan/dev/03/nativa

# Build frontend assets
cd src/Templates
pnpm install
pnpm run build:prod
cd ../..

# Test locally
php -S localhost:8000 -t public
curl http://localhost:8000/
```

### Step 2: Upload to Server
```bash
# Create .deployignore locally
cat > .deployignore << 'EOF'
vendor/
node_modules/
.git/
.gitignore
.env
.env.example
.env.production
.env.production.example
.phpunit.cache/
tests/
.php-cs-fixer.dist.php
phpstan*
psalm*
rector.php
AGENTS.md
QWEN.md
*.md
EOF

# Upload files (exclude vendor/)
rsync -avz \
  --exclude 'vendor/' \
  --exclude 'node_modules/' \
  --exclude '.git/' \
  --exclude '.env' \
  --exclude '.env.example' \
  --exclude '.env.production' \
  --exclude '.env.production.example' \
  --exclude-from='.deployignore' \
  ./ evan@api.responsive.sk:/var/www/nativa/
```

### Step 3: On Server
```bash
# SSH to server
ssh evan@api.responsive.sk

# Navigate to project
cd /var/www/nativa

# Create .env from production template
cp .env.production .env

# Edit .env with production values
nano .env

# Set permissions
chown -R www-data:www-data storage/ data/
chmod -R 755 storage/ data/
chmod -R 644 .env

# Clear any old cache
rm -rf storage/cache/templates/*

# Test PHP syntax
php -l public/index.php
php -l src/init.php

# Test application
php -r "require 'src/init.php'; echo 'Bootstrap OK\n';"
```

### Step 4: Configure Web Server

#### Option A: PHP Built-in Server (Development)
```bash
cd /var/www/nativa
php -S localhost:8000 -t public &
```

#### Option B: Nginx (Production Recommended)
```nginx
server {
    listen 443 ssl http2;
    server_name api.responsive.sk;
    
    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;
    
    root /var/www/nativa/public;
    index index.php;
    
    # Security headers
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Security: deny access to sensitive files
        location ~ /\. {
            deny all;
        }
    }
    
    location ~ /\. {
        deny all;
    }
}
```

#### Option C: Apache (Alternative)
```apache
<VirtualHost *:443>
    ServerName api.responsive.sk
    
    SSLEngine on
    SSLCertificateFile /path/to/ssl/cert.pem
    SSLCertificateKeyFile /path/to/ssl/key.pem
    
    DocumentRoot /var/www/nativa/public
    
    <Directory /var/www/nativa/public>
        AllowOverride All
        Require all granted
        
        # Security headers
        Header always set X-Frame-Options "DENY"
        Header always set X-Content-Type-Options "nosniff"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </Directory>
    
    # Deny access to sensitive files
    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>
</VirtualHost>
```

### Step 5: Verify Deployment
```bash
# Test homepage
curl -I https://api.responsive.sk/

# Test blog
curl -I https://api.responsive.sk/blog

# Test contact
curl -I https://api.responsive.sk/contact

# Check for errors
tail -50 /var/www/nativa/storage/logs/app.log
```

---

## 🔧 Post-deployment

### 1. Create Admin User
```bash
# If admin user doesn't exist, create via database
sqlite3 /var/www/nativa/data/cms.db

INSERT INTO users (id, name, email, password, role, created_at, updated_at)
VALUES (
    hex(randomblob(16)),
    'Admin',
    'admin@phpcms.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  # 'password' hashed
    'admin',
    datetime('now'),
    datetime('now')
);

.quit
```

### 2. Setup Monitoring
```bash
# Create log rotation config
cat > /etc/logrotate.d/nativa << 'EOF'
/var/www/nativa/storage/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    postrotate
        systemctl reload php8.4-fpm
    endscript
}
EOF

# Test logrotate
logrotate -f /etc/logrotate.d/nativa
```

### 3. Setup Backup Script
```bash
cat > /var/www/nativa/scripts/backup.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/nativa"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
cp /var/www/nativa/data/cms.db $BACKUP_DIR/cms_$DATE.db
cp /var/www/nativa/data/jobs.db $BACKUP_DIR/jobs_$DATE.db

# Backup uploads
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/nativa/storage/uploads/

# Keep only last 7 days
find $BACKUP_DIR -name "*.db" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
EOF

chmod +x /var/www/nativa/scripts/backup.sh

# Add to crontab (daily at 2 AM)
echo "0 2 * * * /var/www/nativa/scripts/backup.sh" | crontab -
```

---

## 🐛 Troubleshooting

### Issue: 500 Internal Server Error
```bash
# Check PHP error log
tail -50 /var/log/php8.4-fpm.log

# Check application log
tail -50 /var/www/nativa/storage/logs/app.log

# Check permissions
ls -la /var/www/nativa/storage/
```

### Issue: Templates not updating
```bash
# Clear template cache
rm -rf /var/www/nativa/storage/cache/templates/*

# Check OPcache
php -r "opcache_reset();"

# For PHP-FPM
systemctl restart php8.4-fpm
```

### Issue: Database locked
```bash
# Check for lock files
ls -la /var/www/nativa/data/*.db-journal

# Remove journal files if stuck
rm /var/www/nativa/data/*.db-journal

# Check permissions
chown www-data:www-data /var/www/nativa/data/*.db
```

---

## ✅ Verification Checklist

- [ ] Homepage loads (https://api.responsive.sk/)
- [ ] Blog loads (https://api.responsive.sk/blog)
- [ ] Contact loads (https://api.responsive.sk/contact)
- [ ] Admin login works (https://api.responsive.sk/admin)
- [ ] No errors in logs
- [ ] Permissions are correct (www-data)
- [ ] .env file exists and is secure (644)
- [ ] vendor/ directory NOT uploaded
- [ ] SSL certificate is valid
- [ ] Security headers are present
- [ ] Backup script is running
- [ ] Log rotation is configured

---

**Last Updated:** 2026-03-01
**Status:** Ready for production deployment
