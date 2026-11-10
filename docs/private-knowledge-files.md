# Protected knowledge files

All knowledge uploads (web, API, validation) now use the private `knowledge` disk at `storage/app/knowledge`. Existing `/storage/uploads/...`, `/storage/documents/...`, `/storage/knowledge_files/...`, and `/storage/knowledge_covers/...` URLs pass through KnowledgeFileController. It checks the current article visibility on every request, including cookie sessions and Sanctum bearer tokens. Unpublished, private, or deleted knowledge cannot be downloaded by a guest. Responses are not cached by shared caches.

## Existing installations

Back up database and uploaded files. Deploy during maintenance to prevent uploads from old application workers:

```sh
php artisan down
# Deploy updated code and restart long-running workers.
php artisan config:clear
php artisan migrate --force
php artisan knowledge:protect-files
php artisan config:cache
php artisan up
```

The command copies, verifies SHA-256, then removes each legacy public original. URLs and database paths remain unchanged. The command is safe to rerun and fails on conflicting files rather than overwriting them. Ensure the PHP/web user can write storage/app/knowledge.

Apache's updated public/.htaccess forces these paths through Laravel even when an old public file exists. For Nginx or a CDN, configure the equivalent before reopening traffic: bypass static-file serving and proxy/cache rules for these four `/storage/` directories and send requests to Laravel's front controller. Purge any cached copies from proxies/CDNs. A change cannot revoke files already downloaded by another person.

Do not expose storage/app/knowledge through a symlink or web server alias. Profile photos and banners continue to use the public disk. External media URLs remain controlled by their external provider.
