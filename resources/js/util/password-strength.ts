export interface IFulfilledPasswortRules {
	min_length: boolean,
	min_uppercase_characters: boolean,
	min_lowercase_characters: boolean,
	min_digits: boolean,
	min_other_characters: boolean,
}

export interface IPasswortRules {
	min_length: number,
	min_uppercase_characters: number,
	min_lowercase_characters: number,
	min_digits: number,
	min_other_characters: number,
}

export interface IPasswordValidationResult {
	is_valid: boolean,
	fulfilled_rules: IFulfilledPasswortRules,
	rules: IPasswortRules
}

export class PasswordStrengthValidator {
	private static readonly MIN_LENGTH: number = 12;
	private static readonly MIN_LOWER: number = 1;
	private static readonly MIN_UPPER: number = 1;
	private static readonly MIN_DIGITS: number = 1;
	private static readonly MIN_OTHER: number = 2;
	
	public validate_password(password: string): IPasswordValidationResult {
		console.log(`PasswordStrengthValidator::validate_password(): 1: password === '${password}'`);
		let upper_cnt = 0;
		let lower_cnt = 0;
		let digits_cnt = 0;
		let other_cnt = 0;
		
		for (let i = 0; i < password.length; i++) {
			const c = password[i];
			const c_lower = c.toLowerCase();
			const c_upper = c.toUpperCase();
			if (c_lower === c_upper) {
				if (c >= '0' && c <= '9') {
					digits_cnt++;
				} else {
					other_cnt++;
				}
			} else if (c === c_lower) {
				lower_cnt++;
			} else {
				upper_cnt++;
			}
		}
		
		const is_long_enough = password.length >= PasswordStrengthValidator.MIN_LENGTH;
		const has_enough_upper = upper_cnt >= PasswordStrengthValidator.MIN_UPPER;
		const has_enough_lower = lower_cnt >= PasswordStrengthValidator.MIN_LOWER;
		const has_enough_digits = digits_cnt >= PasswordStrengthValidator.MIN_DIGITS;
		const has_enough_other = other_cnt >= PasswordStrengthValidator.MIN_OTHER;
		
		const summary = is_long_enough
			&& has_enough_upper
			&& has_enough_lower
			&& has_enough_digits
			&& has_enough_other;
		
		const result =  {
			is_valid: summary,
			fulfilled_rules: {
				min_length: is_long_enough,
				min_uppercase_characters: has_enough_upper,
				min_lowercase_characters: has_enough_lower,
				min_other_characters: has_enough_other,
				min_digits: has_enough_digits,
			},
			rules: {
				min_length: PasswordStrengthValidator.MIN_LENGTH,
				min_uppercase_characters: PasswordStrengthValidator.MIN_UPPER,
				min_lowercase_characters: PasswordStrengthValidator.MIN_LOWER,
				min_digits: PasswordStrengthValidator.MIN_DIGITS,
				min_other_characters: PasswordStrengthValidator.MIN_OTHER,
			}
		};
		console.log(`PasswordStrengthValidator::validate_password(): 2: result.is_valid ===`);
		console.log(result.is_valid);
		return result;
	}
}
