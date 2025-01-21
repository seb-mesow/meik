#!/usr/bin/env -S bash -euo pipefail -O extglob

declare -a pids;
php-fpm & pids+=($!);
# php artisan ziggy:generate --types; pids+=($!);

wait "${pids[@]}";
