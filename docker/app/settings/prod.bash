#!/usr/bin/env -S bash -euo pipefail -O extglob

source "$NVM_DIR/nvm.sh";

declare -a pids;
php-fpm & pids+=($!)
# npm run prod-srr & pids+=($!)

wait "${pids[@]}";
