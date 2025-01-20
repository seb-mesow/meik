import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let dark = window.window.matchMedia('(prefers-color-scheme: dark)').matches;
function set_dark_mode(new_dark: boolean) {
	dark = new_dark;
	const root = document.documentElement.classList;
	const icon = document.getElementById("dark_mode_icon")?.classList;
	if (dark) {
		root?.add('p-dark');
		icon?.remove("pi-sun");
		icon?.add("pi-moon");
	} else {
		root.remove('p-dark');
		icon?.add("pi-sun");
		icon?.remove("pi-moon");
	}
}
set_dark_mode(dark);
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
	set_dark_mode(event.matches)
});
export function toggle_dark_mode() {
	set_dark_mode(!dark);
}
