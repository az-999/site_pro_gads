# SSL-сертификаты (acme.sh)

После выпуска acme.sh кладёт файлы прямо в `nginx/ssl/`:

```
nginx/ssl/
├── fullchain.cer                      ← ssl_certificate в nginx
├── sitepro.avatars-meta.com.key       ← ssl_certificate_key в nginx
├── sitepro.avatars-meta.com.cer
├── ca.cer
├── sitepro.avatars-meta.com.csr
└── sitepro.avatars-meta.com.conf      ← конфиг acme.sh (не nginx!)
```

Все `*.cer`, `*.key`, `*.csr` — **не коммитятся** в git.

## Пути в nginx

```nginx
ssl_certificate     /opt/site_pro_gads/nginx/ssl/fullchain.cer;
ssl_certificate_key /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com.key;
```

## Выпуск / обновление

```bash
cd /opt/site_pro_gads/nginx/ssl

# первый выпуск
~/.acme.sh/acme.sh --issue -d sitepro.avatars-meta.com --nginx

# или webroot
~/.acme.sh/acme.sh --issue -d sitepro.avatars-meta.com -w /var/www/acme

# установить/обновить в эту папку + reload nginx
~/.acme.sh/acme.sh --install-cert -d sitepro.avatars-meta.com \
  --cert-file       /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com.cer \
  --key-file        /opt/site_pro_gads/nginx/ssl/sitepro.avatars-meta.com.key \
  --fullchain-file  /opt/site_pro_gads/nginx/ssl/fullchain.cer \
  --reloadcmd       "nginx -t && systemctl reload nginx"
```

Проверка:
```bash
sudo nginx -t && sudo systemctl reload nginx
curl -I https://sitepro.avatars-meta.com/login
```
