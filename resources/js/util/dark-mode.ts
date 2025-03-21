import { computed, ComputedRef, ref, Ref, watch } from "vue";

interface IDarkMode {
	readonly is_dark: Ref<boolean>;
	readonly is_system: Ref<boolean>;
	init_on_before_mounted(): void;
	toggle(): void;
}

type Mode = 'system'|'dark'|'light';
const MODE_SYSTEM = 'system';
const MODE_DARK = 'dark';
const MODE_LIGHT = 'light';
const CLASS_NAME: string = 'p-dark';
const STORAGE_KEY: string = 'dark_light_mode';

class DarkModeClass implements IDarkMode {
	
	private cached_mode: Ref<Mode> = ref(MODE_SYSTEM);
	private is_system_dark: Ref<boolean> = ref(window.matchMedia('(prefers-color-scheme: dark)').matches);
	
	public readonly is_system: ComputedRef<boolean> = computed(() => this.cached_mode.value === MODE_SYSTEM);
	public readonly is_dark: ComputedRef<boolean> = computed(() => {
		if (this.cached_mode.value === MODE_SYSTEM) {
			return this.is_system_dark.value;
		}
		return this.cached_mode.value === MODE_DARK;
	});
	private is_initialized_on_before_mounted: boolean = false;
	
	private init_mode(): void {
		let cur_mode = localStorage.getItem(STORAGE_KEY);
		if (cur_mode !== MODE_SYSTEM && cur_mode !== MODE_DARK && cur_mode !== MODE_LIGHT) {
			localStorage.setItem(STORAGE_KEY, MODE_SYSTEM);
			cur_mode = MODE_SYSTEM;
		}
		this.cached_mode.value = cur_mode as Mode;
		// this.is_dark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
	}
	
	private set_mode(mode: Mode): void {
		if (mode !== this.cached_mode.value) {
			localStorage.setItem(STORAGE_KEY, mode);
			this.cached_mode.value = mode;
		}
	}
	
	public init_on_before_mounted(): void {
		console.log(`DarkMode::init_on_before_mounted()`);
		if (this.is_initialized_on_before_mounted) {
			return;
		}
		
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
			this.is_system_dark.value = event.matches;
		});
		
		watch(this.is_dark, (new_dark) => {
			this.on_change(new_dark);
		});
		
		this.init_mode();

		// Reactivity is does seem not to work until after the component was mounted.
		this.on_change(this.is_dark.value);
		
		this.is_initialized_on_before_mounted = true;
	}
	
	private on_change(new_dark: boolean): void {
		console.log(`DarkMode::on_change(${new_dark})`);
		const root = document.documentElement.classList; // Auch auf primevue.org wird die Klasse direkt am html-Element gesetzt.
		if (new_dark) {
			root.add(CLASS_NAME);
		} else {
			root.remove(CLASS_NAME);
		}
	}
	
	public toggle(): void {
		if (this.cached_mode.value === MODE_SYSTEM) {
			if (this.is_dark.value) {
				this.set_mode(MODE_LIGHT);
			} else {
				this.set_mode(MODE_DARK);
			}
		} else if (this.cached_mode.value === MODE_DARK) {
			if (this.is_system_dark.value) {
				this.set_mode(MODE_LIGHT);
			} else {
				this.set_mode(MODE_SYSTEM);
			}
		} else {
			if (this.is_system_dark.value) {
				this.set_mode(MODE_SYSTEM);
			} else {
				this.set_mode(MODE_DARK);
			}
		}
	}
}

const DarkMode: IDarkMode = new DarkModeClass();
export default DarkMode;
