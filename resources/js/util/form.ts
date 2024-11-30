type RecordKey = string|number|symbol;

type _FormValues<ID extends RecordKey, T> =
	T extends Record<RecordKey, any>
		? _FormValues_Record<ID, T>
		: (T extends Array<any>
			? _FormValues_Array<ID, T>
			: FormValue<ID, T>
		);

type _FormValues_Record<ID extends RecordKey, R extends Record<RecordKey, any>> = {
	[K in keyof R]: _FormValues<K, R[K]>;
}
type _FormValues_Array<ID extends RecordKey, A extends Array<any>> = Array<_FormValues<number, A[number]>>;

export type FormValue<ID extends RecordKey, T> = {
	id: ID,
	val: T,
	errs: string[],
}

type FormValues<P extends Record<RecordKey, any>> = {
	[K in keyof P]: _FormValues<K, P[K]>
}

export type IForm<P extends Record<RecordKey, any>> = {
	vals: FormValues<P>,
	errs: string[]
}

export function create_form(props: Record<RecordKey, any>) {
	const vals = {};
	for (let prop in props) {
		//@ts-ignore
		vals[prop] = create_form_recurse(prop, props[prop]);
	}
	const form = {
		vals: vals,
		errs: []
	};
	return form;
}
function create_form_recurse(prop: RecordKey, val: any) {
	let mapped_val;
	if (Array.isArray(val)) {
		mapped_val = create_form_array(val);
	} else if (typeof val === 'object' && val !== null) {
		//@ts-ignore
		mapped_val = create_form_record(val);
	} else {
		mapped_val = create_form_simple(val);
	}
	return {
		id: prop,
		val: mapped_val,
		errs: []
	}
}
function create_form_record(record: Record<RecordKey, any>) {
	const obj: Record<RecordKey, any> = {};
	for (const key in record) {
		obj[key] = create_form_recurse(key, record[key]);
	}
	return obj;
}
function create_form_array(array: Array<any>) {
	const form_values: any[] = []
	array.forEach((elem, index) => {
		form_values[index] = create_form_recurse(index, elem);
	});
	return form_values;
}
function create_form_simple(val: any) {
	return val;
}

export function create_request_json(form: any) {
	const obj: Record<RecordKey, any> = {};
	for (const prop in form.vals) {
		obj[prop] = create_request_json_recurse(form.vals[prop].val);
		// obj[prop] = form.vals[prop];
	}
	return obj;
}
function create_request_json_recurse(form_val: any) {
	// console.log(form_val)
	if (Array.isArray(form_val)) {
		// console.log("array")
		return create_request_json_array(form_val);
	} else if (typeof form_val === 'object' && form_val !== null) {
		// console.log("object")
		return create_request_json_record(form_val);
	} else {
		// console.log("simple")
		return create_request_json_simple(form_val);
	}
}
function create_request_json_record(form_value: any) {
	const obj: Record<RecordKey, any> = {};
	for (const key in form_value) {
		obj[key] = create_request_json_recurse(form_value[key].val);
	}
	return obj;
}
function create_request_json_array(array_like_obj: any) {
	const arr: any[] = [];
	for (const index in array_like_obj) {
		//@ts-ignore
		arr[index] = create_request_json_recurse(array_like_obj[index].val);
	}
	return arr;
}
function create_request_json_simple(form_value: any): any {
	return form_value;
}

/* Testdaten
const my_data = {
	propUndefined: undefined,
	propNull: null,
	prop0: 0,
	propEmptyString: '',
	propEmptyArray: [],
	propEmptyObject: {},
	prop1: "easy",
	prop2: ["prop2.0", "prop2.1"],
	prop3: {
		"prop3.prop1": "simple",
		"prop3.prop2": "simple",
	},
	prop4: [
		{ "prop4.0.prop1": "easy", "prop4.0.prop2": "easy" } ,
		{ "prop4.1.prop1": "easy", "prop4.1.prop2": "easy" } 
	],
	prop5: {
		"prop5.0": [ "prop5.0.prop0", "prop5.0.prop1" ],
		"prop5.1": [ "prop5.1.prop0", "prop5.1.prop1" ] 
	},
};

console.log(my_data);
console.log(create_form(my_data));
console.log(create_request_json(create_form(my_data)));
*/
