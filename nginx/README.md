# Nginx — sitepro.avatars-meta.com

Проксирует HTTPS на Docker-приложение Yii2 (`127.0.0.1:6003`).

## Установка на сервере

1. DNS: A-запись `sitepro.avatars-meta.com` → IP сервера.

2. **SSL через acme.sh** — файлы в `nginx/ssl/` (`fullchain.cer`, `sitepro.avatars-meta.com.key`). См. [ssl/README.md](ssl/README.md).

   Обновление / установка в эту папку:
   ```bash
   ~/.acme.sh/acme.sh --install-cert -d sitepro.avatars-meta.com \
     --cert-file       /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com.cer \
     --key-file        /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com.key \
     --fullchain-file  /opt/site_pro_gads/nginx/ssl/fullchain.cer \
     --reloadcmd       "nginx -t && systemctl reload nginx"
   ```

3. Подключить конфиг:

```bash
sudo ln -sf /opt/site_pro_gads/nginx/sitepro.avatars-meta.com.conf \
  /etc/nginx/sites-enabled/sitepro.avatars-meta.com.conf

sudo nginx -t
sudo systemctl reload nginx
```

4. Docker на порту 6003:

```bash
cd /opt/site_pro_gads && docker compose ps
curl -I http://127.0.0.1:6003/login
```

5. Откройте https://sitepro.avatars-meta.com/login

## Структура

```
nginx/
├── sitepro.avatars-meta.com.conf
├── ssl/                              ← fullchain.cer, *.key (acme.sh)
└── README.md
```

## Пути в конфиге

```nginx
ssl_certificate     /opt/site_pro_gads/nginx/ssl/fullchain.cer;
ssl_certificate_key /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com.key;
```
