# 11 — Production Deployment Runbook

> Target: VPS Indonesia (Biznet/Niagahoster/IDCloudHost) untuk data residency UU PDP. Stack: Docker Compose + Nginx + PHP-FPM + MySQL 8 + Redis 7 + MinIO + Certbot.

## 1. Pre-Requisites

- VPS minimal **4 vCPU, 8GB RAM, 80GB SSD** (untuk Starter tier ~25 tenant × 100 karyawan)
- Domain pointing ke VPS IP (A record `hris.app` + wildcard `*.hris.app`)
- Docker + Docker Compose v2 terinstall
- Firewall allow port 80, 443, 22 only
- Outbound allow: `whitelabel.co.id:443` (license validation)

## 2. First-Time Setup

```bash
# 1. Clone repo ke server
git clone https://github.com/youruser/hris.git /var/www/hris
cd /var/www/hris

# 2. Generate .env.production
cp .env.example .env.production
nano .env.production
# Set: APP_KEY (run: php artisan key:generate --show)
#      APP_ENV=production
#      APP_URL=https://hris.app
#      APP_DEBUG=false
#      DB_PASSWORD, DB_ROOT_PASSWORD (strong random)
#      REDIS_PASSWORD (strong random)
#      MINIO_ROOT_USER, MINIO_ROOT_PASSWORD
#      LICENSE_DEV_BYPASS=false
#      MAIL_* (configure tenant default)
#      QUEUE_CONNECTION=redis
#      CACHE_STORE=redis
#      SESSION_DRIVER=redis

# 3. Initial SSL via certbot standalone (sebelum Nginx jalan)
mkdir -p certbot/conf certbot/www
docker run --rm -p 80:80 \
  -v ./certbot/conf:/etc/letsencrypt \
  -v ./certbot/www:/var/www/certbot \
  certbot/certbot certonly --standalone -d hris.app -d '*.hris.app' \
    --email admin@hris.app --agree-tos --no-eff-email
  # Untuk wildcard, butuh DNS-01 challenge → manual config

# 4. Build + start stack
docker compose -f docker-compose.production.yml build
docker compose -f docker-compose.production.yml up -d

# 5. Run migrations + seed
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=HolidaySeeder --force
docker compose exec app php artisan db:seed --class=TaxAndBPJSSeeder --force
docker compose exec app php artisan db:seed --class=PlanSeeder --force

# 6. License pairing (one-time)
# Buka https://hris.app/__pair di browser
# Paste activation key dari whitelabel.co.id
```

## 3. Daily Operations

### Backup
```bash
# Daily DB dump (cron 02:00 WIB)
docker compose exec mysql sh -c 'exec mysqldump --all-databases -uroot -p"$MYSQL_ROOT_PASSWORD"' \
  | gzip > /backup/hris-$(date +%F).sql.gz

# Upload to remote (Wasabi/S3/Backblaze)
aws s3 cp /backup/hris-$(date +%F).sql.gz s3://hris-backup/ \
  --endpoint-url=https://s3.ap-southeast-1.wasabisys.com
```

### Update Deploy
```bash
git pull
docker compose -f docker-compose.production.yml build app
docker compose -f docker-compose.production.yml up -d --no-deps app queue scheduler
docker compose exec app php artisan migrate --force
docker compose exec app php artisan view:cache
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan queue:restart
```

### Logs
```bash
docker compose logs -f app           # Laravel app
docker compose logs -f queue         # Queue workers
docker compose logs -f nginx         # HTTP access/error
docker compose exec app tail -f storage/logs/laravel.log
```

## 4. Scaling

### Horizontal (multi-app instance)
- Add `app2`, `app3` services pointing same MySQL+Redis
- Nginx upstream load balance
- Sticky session via Redis (already configured)

### Database scaling
- MySQL read replica untuk dashboard analytics
- Configure `DB_READ_HOST=replica.internal` di `.env`

### Queue scaling
- Spawn multiple `queue` services dengan command berbeda:
  - `--queue=high,default` (general)
  - `--queue=payroll` (heavy batch)
  - `--queue=notifications` (email/WA)

## 5. Monitoring

### Recommended stack
- **Uptime Kuma** (self-host) — uptime monitoring
- **Sentry** — error tracking (PHP + Flutter)
- **Grafana + Prometheus** — metric (request rate, DB, queue)
- **OpenSearch** (later) — log aggregation

### Health checks
```bash
curl https://hris.app/up        # Laravel health endpoint
curl https://hris.app/healthz   # Custom HRIS health (TODO: implement)
```

## 6. SSL Renewal

Certbot auto-renew sudah configured di compose. Check status:
```bash
docker compose logs certbot
```

## 7. UU PDP Compliance Checklist

- [ ] Data karyawan resident hanya di server Indonesia
- [ ] Backup encrypted (AES-256)
- [ ] Backup retention 7 tahun untuk payroll/tax data
- [ ] PII access logged (sudah di `pii_access_logs` table)
- [ ] Right-to-export endpoint live (`/api/v1/me/data-export`)
- [ ] Right-to-deletion workflow active
- [ ] DPO contact published di privacy policy
- [ ] Breach notification SOP documented
- [ ] Annual pentest scheduled
- [ ] ISO 27001 prep (year 2)

## 8. Disaster Recovery

### RTO (Recovery Time Objective): 4 jam
### RPO (Recovery Point Objective): 15 menit

```bash
# Restore dari backup
gunzip < /backup/hris-2026-05-20.sql.gz | docker compose exec -T mysql mysql -uroot -p"$MYSQL_ROOT_PASSWORD"

# Restore MinIO objects
mc mirror s3://hris-backup/storage/ local/hris-storage/
```

DR drill: 6-bulanan, simulasi total data loss + restore dari S3 backup.

## 9. Cost Estimate (Bulanan)

| Item | Spec | Cost (Rp) |
|---|---|---|
| VPS (Biznet Cloud) | 4 vCPU, 8GB RAM, 80GB SSD | 800.000 |
| Backup storage (Wasabi) | 200GB | 300.000 |
| Domain + Cloudflare | hris.app | 250.000/tahun |
| Email service (SES atau SMTP local) | 50k email/bulan | 200.000 |
| **Total** | | **~1.3 juta/bulan** |

Per tenant cost (jika 100 tenant aktif): **~13.000/tenant** — cost of goods sold yang sangat sehat untuk SaaS pricing Rp 15-25k/user.

## 10. Troubleshooting

| Gejala | Diagnose | Fix |
|---|---|---|
| 502 Bad Gateway | PHP-FPM down | `docker compose restart app` |
| Slow response | Cache cold | `php artisan optimize` |
| Queue stuck | Worker memory leak | `docker compose restart queue` |
| Payroll error | Tax/BPJS rate stale | Re-seed: `php artisan db:seed --class=TaxAndBPJSSeeder` |
| License expired | License server tidak reachable | Cek firewall outbound, atau wait 30d grace |
| Disk full | Storage uploads | Cleanup: `find storage/logs -mtime +30 -delete` |
