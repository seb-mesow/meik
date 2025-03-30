export namespace ChangePassword {
	export interface IRequestData {
		old_password: string,
		new_password: string,
	};
	// Altes Passwort war korrekt.
	export type I200ResponseData = never;
	// Altes Passwort ist falsch.
	export type I422ResponseData = never;
}
