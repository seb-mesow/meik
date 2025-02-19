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

interface IMonth {
	full_name: string,
	short_name: string,
	abbreviation: string,
	days(year: number): number,
}

const MONTHS: IMonth[] = [
	{ full_name: 'Januar', short_name: 'Jan', abbreviation: 'Jan.', days: () => 31 },
	{ full_name: 'Februar', short_name: 'Feb', abbreviation: 'Feb.', days: (year) => is_leap_year(year) ? 29 : 28 },
	{ full_name: 'März', short_name: 'März', abbreviation: 'März', days: () => 31 },
	{ full_name: 'April', short_name: 'Apr', abbreviation: 'Apr.', days: () => 30 },
	{ full_name: 'Mai', short_name: 'Mai', abbreviation: 'Mai', days: () => 31 },
	{ full_name: 'Juni', short_name: 'Juni', abbreviation: 'Juni', days: () => 30 },
	{ full_name: 'Juli', short_name: 'Juli', abbreviation: 'Juli', days: () => 31 },
	{ full_name: 'August', short_name: 'Aug', abbreviation: 'Aug.', days: () => 31 },
	{ full_name: 'September', short_name: 'Sep', abbreviation: 'Sep.', days: () => 30 },
	{ full_name: 'Oktober', short_name: 'Okt', abbreviation: 'Okt.', days: () => 31 },
	{ full_name: 'November', short_name: 'Nov', abbreviation: 'Nov.', days: () => 30 },
	{ full_name: 'Dezember', short_name: 'Dez', abbreviation: 'Dez.', days: () => 31 },
];

function get_month_by_name(input_name: string): number {
	input_name = input_name.trim();
	const month: number = MONTHS.findIndex((month: IMonth): boolean => 
		month.short_name === input_name || month.full_name === input_name || month.abbreviation === input_name
	);
	if (month < 0) {
		console.log("invalid month name");
		throw new Errors.InvalidMonthName();
	}
	return month + 1;
}

export function is_leap_year(year: number): boolean {
	return (year % 400 === 0) ? true : ((year % 100 === 0) ? false : (year % 4 === 0));
}

export class PartialDate {
	private static readonly REGEX_ISO_YYYY: RegExp = /^(?<year>\d{4})$/;
	private static readonly REGEX_ISO_YYYY_MM: RegExp = /^(?<year>\d{4})-(?<month>\d{2})$/;
	private static readonly REGEX_ISO_YYYY_MM_DD: RegExp = /^(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})$/;
	private static readonly REGEX_PRETTY_DD_MM_YYYY: RegExp = /^(?<day>\d{1,2})\.(?<month>\d{1,2})\.(?<year>\d{4})$/;
	private static readonly REGEX_PRETTY_DD_MONTH_YYYY: RegExp = /^(?<day>\d{1,2})\. (?<month_name>\p{L}+\.?) (?<year>\d{4})$/u;
	private static readonly REGEX_PRETTY_MONTH_YYYY: RegExp = /^(?<month_name>\p{L}+\.?) (?<year>\d{4})$/u;
	private static readonly REGEX_PRETTY_YYYY: RegExp = /^(?<year>\d{4})$/;
	
	public static parse_iso(input: string): PartialDate {
		if (input.length < 1) {
			throw new Errors.NoPartialDate();
		}
		
		const res = (this.REGEX_ISO_YYYY.exec(input)
			?? this.REGEX_ISO_YYYY_MM.exec(input)
			?? this.REGEX_ISO_YYYY_MM_DD.exec(input)) as { groups: { year: string, month?: string, day?: string } }|null;
		if (!res) {
			throw new Errors.InvalidFormat();
		}
		
		const year: number = parseInt(res.groups.year);
		let month: number|undefined = undefined;
		let day: number|undefined = undefined;
		if (res.groups.month) {
			month = parseInt(res.groups.month);
			if (res.groups.day) {
				day = parseInt(res.groups.day);
			}
		}
		
		const partial_date = new PartialDate(year, month, day);
		partial_date.validate();
		return partial_date;
	}
	
	public static parse_pretty(input: string): PartialDate {
		const _input = input.trim();
		if (_input.length < 1) {
			throw new Errors.NoPartialDate();
		}
		
		const res = (this.REGEX_PRETTY_YYYY.exec(_input)
			?? this.REGEX_PRETTY_MONTH_YYYY.exec(_input)
			?? this.REGEX_PRETTY_DD_MM_YYYY.exec(_input)
			?? this.REGEX_PRETTY_DD_MONTH_YYYY.exec(_input)) as { groups: { year: string, month?: string, month_name?: string, day?: string } }|null;
		if (!res) {
			throw new Errors.InvalidFormat();
		}
		
		const year: number = parseInt(res.groups.year);
		let month: number|undefined = undefined;
		let day: number|undefined = undefined;
		if (res.groups.month) {
			month = parseInt(res.groups.month);
		} else if (res.groups.month_name) {
			month = get_month_by_name(res.groups.month_name);
		}
		if (month !== undefined && res.groups.day) {
			day = parseInt(res.groups.day);
		}
		
		// no validate
		return new PartialDate(year, month, day);
	}
	
	private constructor(
		private readonly year: number,
		private readonly month?: number,
		private readonly day?: number,
	) {};
	
	public validate(): void {
		const errors: Errors.IError[] = [];
		
		if (!Number.isInteger(this.year)) {
			errors.push(new Errors.InvalidYearNumber());
		} else if (this.year < 0) {
			errors.push(new Errors.InvalidYearNumber());
		}
		
		if (this.month !== undefined) {
			const valid_month = (Number.isInteger(this.month) && this.month >= 1 &&  this.month <= 12) ? this.month : undefined;
			const days_in_month = (valid_month !== undefined) ? (MONTHS[valid_month - 1].days(this.year)) : undefined;
			
			// validate days first
			if (this.day !== undefined) {
				if (!Number.isInteger(this.day)) {
					errors.push(new Errors.InvalidDayNumber(days_in_month));
				} else if (this.day < 1) {
					errors.push(new Errors.InvalidDayNumber(days_in_month));
				} else if (days_in_month !== undefined && this.day > days_in_month) {
					errors.push(new Errors.InvalidDayNumber(days_in_month));
				}
			}
			
			if (!Number.isInteger(this.month)) {
				errors.push(new Errors.InvalidMonthNumber());
			} else if (this.month < 1) {
				errors.push(new Errors.InvalidMonthNumber());
			} else if (this.month > 12) {
				errors.push(new Errors.InvalidMonthNumber());
			}
			
		}
		
		if (errors.length > 0) {
			throw new Errors.MultipleErrors(errors);
		}
	}
	
	public format_iso(): string {
		let str = this.year.toString();
		if (this.month) {
			str += '-' + this.month.toString().padStart(2, '0');
			if (this.day) {
				str += '-' + this.day.toString().padStart(2, '0');
			}
		}
		return str;
	}
	
	public format_pretty(): string {
		if (this.month) {
			if (this.day) {
				return this.day.toString().padStart(2, '0') + '.' + this.month.toString().padStart(2, '0') + '.' + this.year.toString();
			} else {
				return MONTHS[this.month - 1].abbreviation + ' ' + this.year.toString();
			}
		}
		return this.year.toString();
	}
}
