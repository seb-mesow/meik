#!/usr/bin/env -S bash -euo pipefail -O extglob
set -x;

REPO_DIR="$(dirname -- "$( readlink -f -- "$0"; )")";

if [[ "$MEIK_DEPLOYMENT_PROFILE" = 'prod' ]] ; then
	compose_profile_file="prod"
elif [[ "$MEIK_DEPLOYMENT_PROFILE" = 'prod-ssr' ]] ; then
	compose_profile_file="prod-ssr"
else
	echo "Env-Var MEIK_DEPLOYMENT_PROFILE has invalid value '$MEIK_DEPLOYMENT_PROFILE'.";
	exit 1;
fi

function docker_compose() {
	docker compose \
		--profile "${MEIK_DEPLOYMENT_PROFILE}" \
		-f "${REPO_DIR}/docker/compose.yml" \
		-f "${REPO_DIR}/docker/compose.${compose_profile_file}.yml" \
		-f "${REPO_DIR}/docker/compose.${compose_profile_file}.override.yml" \
		"$@";
}

function docker_compose_run() {
	docker_compose run --rm --entrypoint '' "$@";
}

function docker_compose_run_entrypoint() {
	docker_compose run --rm "$@" 'true';
}

function php() {
	docker_compose_run --user normal app php "$@";
}

function artisan() {
	php artisan "$@";
}

function npm() {
	docker_compose_run --user normal node npm "$@";
}

if [[ "$1" = "up" ]] ; then
	if [[ "$MEIK_DEPLOYMENT_PROFILE" = "prod-ssr" ]] ; then
		echo "Deployment Profile prod-ssr is not yet implemented";
		exit 1;
	else
		# MEIK_DEPLOYMENT_PROFILE = 'prod'
		docker_compose build;
		docker_compose_run_entrypoint app;
		artisan key:generate;
		artisan ziggy:generate --types;
		npm run prod-build;
		docker_compose up -d --wait --force-recreate --remove-orphans db mariadb
		artisan migrate --quiet;
		
		artisan migrate:fresh --seed; # TODO RAUS!
		
		artisan config:clear;
		artisan optimize; #(Config, Event-Listener-Mapping, Routes cachen)
		docker_compose up -d --wait --force-recreate --remove-orphans web app
			# php-fpm
			# php artisan inertia:start-ssr
	fi
elif [[ "$1" = "down" ]] ; then
	docker_compose down;
else
	echo "No action specificed";
fi
