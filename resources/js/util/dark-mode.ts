export default class {
	private dark: boolean;
	
	public constructor() {
		this.dark = window.window.matchMedia('(prefers-color-scheme: dark)').matches;
		this.set(this.dark);
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
			this.set(event.matches)
		});
	}
	
	public set(new_dark: boolean) {
		this.dark = new_dark;
		const root = document.documentElement.classList; // auch auf primevue.org wird die Classe direkt am html-Element gesetzt.
		const icon = document.getElementById("dark_mode_icon")?.classList;
		if (this.dark) {
			root?.add('p-dark');
			icon?.remove("pi-sun");
			icon?.add("pi-moon");
		} else {
			root.remove('p-dark');
			icon?.add("pi-sun");
			icon?.remove("pi-moon");
		}
	}
	
	public toggle() {
		this.set(!this.dark);
	}
}
