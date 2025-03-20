<script lang="ts" setup>
import { route } from 'ziggy-js';
import isc_logo_url from '../../images/Logo_isc_300x122.jpg';
import fv_logo_url from '../../images/FV_ISC.jpg';
import 'material-design-icons-iconfont/dist/material-design-icons.css';
import { permissions } from '@/util/permissions';

const user_permissions = permissions.value.user;

const can_access_user_administration: boolean = user_permissions.create || user_permissions.update || user_permissions.delete;
</script>

<template>
	<div class="navbar-underlay">
		<nav>
			<div class="navbar">
				<a :href="route('category.overview')">
					<img class="logo" :src="isc_logo_url">
				</a>
				<div class="flex flex-col gap-4">
					<a :href="route('exhibit.overview')">
						<div class="link flex items-center justify-center rounded-sm hover:bg-meik-primary/60">
							<div class="">
								<span class="icon material-symbols-outlined">
									archive
								</span>
							</div>
							<div class="label-box">
								<span class="label">Exponate</span>
							</div>
						</div>
					</a>

					<a :href="route('location.overview')">
						<div class="link flex items-center justify-center rounded-sm hover:bg-meik-primary/60">
							<div class=" flex items-center justify-center">
								<span class="icon material-symbols-outlined">
									location_on
								</span>
							</div>
							<div class="label-box">
								<span class="label">Standorte</span>
							</div>
						</div>
					</a>

					<a v-if="can_access_user_administration" :href="route('user.overview')">
						<div class="link flex items-center justify-center rounded-sm hover:bg-meik-primary/60">
							<div class="">
								<span class="icon material-symbols-outlined">
									person
								</span>
							</div>
							<div class="label-box">
								<span>Benutzer</span>
							</div>
						</div>
					</a>
				</div>

				<img class="logo" :src="fv_logo_url">
			</div>
		</nav>
	</div>
</template>

<style lang="css" scoped>
.navbar-underlay {
	flex-grow: 0;
	flex-shrink: 0;
	flex-basis: 8rem;
}

.navbar {
	width: 8rem;
	height: 100vh;
	position: fixed;
	top: 0;
	left: 0;
	background-color: #003366;
	/* Dunkelblau */
	display: flex;
	justify-content: space-between;
	flex-direction: column;
	align-items: center;
}

.logo {
	padding: 0.25rem;
}

.link {
	width: 7.5rem;
	height: 3rem;
	color: rgb(255, 255, 255);
}

/* . {
	display: inline flow-root;
	vertical-align: middle;
	width: 33%;
	text-align: center;
} */
.label-box {
	display: inline flow-root;
	width: 67%;
	text-align: center;
}

@media screen and (max-width: 64rem) {
	.navbar-underlay {
		flex-basis: 3rem;
	}

	.navbar {
		width: 3rem;
		justify-content: space-around;
	}

	.logo {
		display: none;
	}

	.link {
		width: 3.5rem;
	}

	. {
		width: 100%;
	}

	.label-box {
		display: none;
	}
}

.nav-list {
	list-style: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.7rem;
}

.nav-item {
	display: flex;
	justify-content: center;
	width: 100%;
}
</style>
