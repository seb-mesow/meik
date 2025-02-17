export namespace Errors {
	export abstract class IError extends Error {};
	export class NoPartialDate extends IError {
		public constructor() {
			super('Bitte ein partielles Datum eingeben');
		}
	};
	export class InvalidFormat extends IError {
		public constructor() {
			super('Bitte in einem gültigen Format angeben');
		}
	};
	export class InvalidYearFormat extends IError {
		public constructor() {
			super('Bitte das Jahr in einem gültigen Format angeben.');
		}
		public static message: string = ''
	};
	export class InvalidMonthFormat extends IError {
		public constructor() {
			super('Bitte den Monat in einem gültigen Format angeben');
		}
	};
	export class InvalidDayFormat extends IError {
		public constructor() {
			super('Bitte den Tag in einem gültigen Format angeben');
		}
	};
	export class InvalidYearNumber extends IError {
		public constructor() {
			super('Bitte ein gültiges Jahr angeben');
		}
	};
	export class InvalidMonthNumber extends IError {
		public constructor() {
			super('Bitte einen Monat von 1 bis 12 angeben');
		}
	};
	export class InvalidMonthName extends IError {
		public constructor() {
			super('Bitte einen gültigen Monatsnamen angeben');
		}
	};
	export class InvalidDayNumber extends IError {
		public constructor(max_days_in_month?: number) {
			if (max_days_in_month === undefined) {
				super('Bitte einen gültigen Tag angeben');
			} else {
				super('Bitte einen Tag von 1 bis ' + max_days_in_month.toString() + ' angeben');
			}
		}
	};
	export class MultipleErrors {
		public constructor(
			public readonly errors: Error[]
		) {}
	}
}

const REGEX_ISO_YYYY_MM_DD: RegExp = /^(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})$/;

export function parse_iso_date(input: string): Date {
	// if (input.length < 1) {
	// 	throw new Errors.NoPartialDate();
	// }
	
	// const res = (REGEX_ISO_YYYY_MM_DD.exec(input)) as { groups: { year: string, month: string, day: string } }|null;
	// if (!res) {
	// 	throw new Errors.InvalidFormat();
	// }
	
	// const year: number = parseInt(res.groups.year);
	// const month: number = parseInt(res.groups.month);
	// const day: number = parseInt(res.groups.day);
	
	// const date = new Date(Date.UTC(year, month - 1, day));
	// return date;
	return new Date(input);
}

export function format_as_iso_date(date: Date): string {
	return date.getUTCFullYear().toString().padStart(4, '0')
		+ '-' + (date.getUTCMonth() + 1).toString().padStart(2, '0')
		+ '-' + date.getUTCDay().toString().padStart(2, '0');
}
