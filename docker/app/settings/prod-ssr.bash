#!/usr/bin/env -S bash -euo pipefail -O extglob

source "$NVM_DIR/nvm.sh";

declare -a pids;
declare -a short_pids;

php-fpm & pids+=($!);

php artisan key:generate & $short_pids+=($!);
npm ci; & $short_pids+=($!);
wait "${pids[@]}";

php artisan ziggy:generate --types;
npm run prod-build-srr;
php artisan inertia:start-ssr & pids+=($!);

wait "${pids[@]}";
