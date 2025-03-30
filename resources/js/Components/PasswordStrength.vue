<script setup lang="ts">
import { IFulfilledPasswortRules, IPasswordValidationResult, IPasswortRules as IPasswordRules } from '@/util/password-strength';

defineProps<{
	validation_result: IPasswordValidationResult,
}>();

const rules: (keyof IFulfilledPasswortRules)[] = [
	'min_length',
	'min_lowercase_characters',
	'min_uppercase_characters',
	'min_digits',
	'min_other_characters',
];
</script>

<template>
	<div>
		<p>Das Passwort muss</p>
		<div v-for="rule in rules" :key="rule" class="grid grid-cols-2 gap-x-1" style="grid-template-columns: max-content 100%;">
			<div class="relative -bottom-[.1rem]">
				<i v-if="validation_result.fulfilled_rules[rule]" class="pi pi-check text-green-600 dark:text-green-400"/>
				<i v-else class="pi pi-times text-red-600 dark:text-red-400"/>
			</div>
			<span v-if="rule === 'min_length'">mindestens {{ validation_result.rules.min_length }} Zeichen lang sein</span>
			<span v-if="rule === 'min_lowercase_characters'">mindestens {{ validation_result.rules.min_lowercase_characters }} Kleinbuchstaben enthalten</span>
			<span v-if="rule === 'min_uppercase_characters'">mindestens {{ validation_result.rules.min_uppercase_characters }} Großbuchstaben enthalten</span>
			<span v-if="rule === 'min_digits'">mindestens {{ validation_result.rules.min_digits }} {{ validation_result.rules.min_digits === 1 ? 'Ziffer' : 'Ziffern' }} enthalten</span>
			<span v-if="rule === 'min_other_characters'">mindestens {{ validation_result.rules.min_other_characters }} Sonderzeichen enthalten</span>
		</div>
	</div>
</template>
