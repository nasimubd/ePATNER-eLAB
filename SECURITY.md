# Security Policy

## Reporting a vulnerability

Do not report security vulnerabilities in a public issue. Contact the maintainer privately with a concise description, affected versions, and reproducible steps where safe to share.

## Secure setup requirements

PulseForge handles healthcare operational data. Before deployment, configure unique application and database credentials, restrict access by role, secure backups, and review the security, privacy, and regulatory requirements for the intended environment.

Never commit patient data, production database files, credentials, private environment files, or secrets.

## Administrator bootstrap

Database seeders create roles and permissions only. They do not create administrator accounts or ship credentials.

After running migrations, create the first super administrator interactively:

```bash
php artisan user:create-super-admin "Administrator Name" admin@example.com
```

The command prompts for the password without placing it in shell history. Existing accounts require explicit confirmation before their password is reset and the super-admin role is assigned.
