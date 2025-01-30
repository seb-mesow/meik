const SHORT_MONTH_NAMES: string[] = ['Jan.', 'Feb.', 'März', 'Apr.', 'Mai', 'Juni', 'Juli', 'Aug.', 'Sep.', 'Okt', 'Nov.', 'Dez.'];
const REGEX: RegExp = /(\d\d\d\d)-?(\d\d)?-?(\d\d)?/g;

export function sprint_pretty_partial_date(partial_date: string): string {
	const res_array: string[][] = partial_date.matchAll(REGEX).toArray();
	if (res_array.length !== 1) {
		throw new Error('invalid PartialDate');
	}
	const res = res_array[0];
	const year_str: string = res[1];
	const month_str: string|null = res[2] ?? null;
	const day_str: string|null = res[3] ?? null;
	if (month_str) {
		if (day_str) {
			return day_str + '.' + month_str + '.' + year_str;
		} else {
			return SHORT_MONTH_NAMES[parseInt(month_str) - 1] + ' ' + year_str;
		}
	}
	return year_str
}
