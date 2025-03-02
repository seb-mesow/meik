import { ref, Ref, watch } from "vue";

interface IDarkMode {
	readonly is_dark: Ref<boolean>;
	init_on_before_mounted(): void;
	toggle(): void;
}

class DarkModeClass {
	private static readonly CLASS_NAME: string = 'p-dark';
	
	public readonly is_dark: Ref<boolean> = ref(true);
	private is_initialized_on_before_mounted: boolean = false;
	
	public init_on_before_mounted(): void {
		console.log(`DarkMode::init_on_before_mounted()`);
		if (!this.is_initialized_on_before_mounted) {
			watch(this.is_dark, (new_dark) => {
				this.on_change(new_dark);
			});
			
			this.is_dark.value = window.window.matchMedia('(prefers-color-scheme: dark)').matches;
			window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
				this.is_dark.value = event.matches;
			});
			
			// Reactivity is does seem not to work until after the component was mounted.
			this.on_change(this.is_dark.value);
			
			this.is_initialized_on_before_mounted = true;
		}
	}
	
	private on_change(new_dark: boolean) {
		console.log(`DarkMode::on_change(${new_dark})`);
		const root = document.documentElement.classList; // Auch auf primevue.org wird die Klasse direkt am html-Element gesetzt.
		if (new_dark) {
			root.add(DarkModeClass.CLASS_NAME);
		} else {
			root.remove(DarkModeClass.CLASS_NAME);
		}
	}
	
	public toggle(): void {
		this.is_dark.value = !this.is_dark.value;
	}
}

const DarkMode: IDarkMode = new DarkModeClass();
export default DarkMode;
