# SSL-сертификаты

Положите файлы в каталог `sitepro.avatars-meta.com/`:

```
nginx/ssl/sitepro.avatars-meta.com/
├── fullchain.pem   # сертификат + цепочка CA
└── privkey.pem     # приватный ключ
```

Файлы **не коммитятся** в git (см. `.gitignore`).

## Вариант 1: Let's Encrypt (certbot)

На сервере, до включения HTTPS в nginx:

```bash
sudo mkdir -p /var/www/certbot
sudo apt install certbot

# Временно используйте только HTTP-блок из конфига (порт 80 для ACME)
sudo certbot certonly --webroot \
  -w /var/www/certbot \
  -d sitepro.avatars-meta.com \
  --email YOUR@EMAIL.com \
  --agree-tos
```

Скопируйте сертификаты в проект:

```bash
sudo cp /etc/letsencrypt/live/sitepro.avatars-meta.com/fullchain.pem \
  /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com/
sudo cp /etc/letsencrypt/live/sitepro.avatars-meta.com/privkey.pem \
  /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com/
sudo chmod 640 /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com/*.pem
sudo chown root:www-data /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com/*.pem
```

Или укажите в nginx пути напрямую на `/etc/letsencrypt/live/...` (без копирования).

## Вариант 2: Свои сертификаты

Скопируйте выданные CA файлы как `fullchain.pem` и `privkey.pem` в каталог выше.
