type RecordKey = string|number;

// export type IValueForm<ID extends RecordKey, T> =
// 	T extends Record<RecordKey, any>
// 		? IRecordForm<ID, T>
// 		: (T extends Array<any>
// 			? IArrayForm<ID, T>
// 			: ISimpleForm<ID, T>
// 		);

// type IRecordForm<ID extends RecordKey, R extends Record<RecordKey, any>> = ISimpleForm<ID, {
// 	[K in keyof R]: IValueForm<K, R[K]>
// }>;
// type IArrayForm<ID extends RecordKey, A extends Array<any>> = ISimpleForm<ID,
// 	Array<IValueForm<number, A[number]>>
// >;

type KeyWords = '__type'|'__persisted'|'__index'|'__key';
type _Persisted = boolean;
type _Index = number;
type _Key = number|string;

type SimpleValue = number|string|boolean|null|undefined|bigint
interface IFormSpec {
	__type?: FormSpec,
	__persisted?: _Persisted,
	__index?: _Index,
	__key?: _Index,
};
type FormSpec = SimpleValue|Array<FormSpec>|IFormSpec&{[key: Exclude<RecordKey,KeyWords>]: FormSpec};
export type CheckFormSpec<FS extends IFormSpec> = FS extends IFormSpec ? FS : never; // extends IFormSpec ? FS : never;
// type EnforceKeyWords<FS extends Record<RecordKey,FormSpec>> =
	// FS & {
		
	// };

export type IForm<ID extends RecordKey, T extends FormSpec> = {
	id: ID,
	val: FormedValue<T>,
	errs: string[],
}
& (T extends { __persisted: infer P } ? { persisted: P } : {})
& (T extends { __index: infer I } ? { index: I } : {})
& (T extends { __key: infer K } ? { key: K }: {});


type MyForm = IForm<'xyz', {
	simple_prop: string,
	simple_prop_advanced: { __type: number, __index: number },
	record_prop: {
		p1: string,
		p2: { __type: number, __persisted: false },
	},
	__persisted: true,
	__index: number
}>;

const my_value: MyForm = {
	id: 'xyz',
	val: {
		simple_prop: {
			id: 'simple_prop',
			val: 'sdfsdf',
			errs: [],
		},
		simple_prop_advanced: {
			id: 'simple_prop_advanced',
			val: 1234,
			errs: [],
			index: 12,
		},
		record_prop: {
			id: 'record_prop',
			val: {
				p1: {
					id: 'p1',
					val: 'asdad',
					errs: [],
				},
				p2: {
					id: 'p2',
					val: 123,
					errs: [],
					persisted: false
				},
			},
			errs: [],
		}
	},
	errs: [],
	persisted: true,
	index: 12,
};

// https://www.typescriptlang.org/docs/handbook/utility-types.html

// very good names
//@ts-ignore
type FormedRecord<R extends Record<RecordKey, FormSpec>> = { [K in keyof R]: IForm<K, R[K]> };
type FormedArray<A extends Array<FormSpec>> = Array<IForm<number, A[number]>>;
type FormedSimpleValue<V extends SimpleValue> = V;

type FormedValue<T extends FormSpec> =
	T extends Record<RecordKey, FormSpec>
	? ( T extends { __type: infer T2 extends FormSpec }
		? FormedValue<T2>
		: FormedRecord<Omit<T, KeyWords>>
	) : (T extends Array<FormSpec>
		? FormedArray<T>
		: (T extends SimpleValue
			? FormedSimpleValue<T>
			: never 
		)
	);

// export type IValueForm<ID extends RecordKey, T> = ISimpleForm<ID, FormedValue<T>>; 

// type IPropsForm<P extends Record<RecordKey, any>> = {
// 	[K in keyof P]: IValueForm<K, P[K]>
// }

// export type IForm<Props extends Record<RecordKey, any>> = {
// 	vals: IPropsForm<Props>,
// 	errs: string[],
// 	persisted: boolean,
// }


/**
 * creates a form for attributes without errors
 * 
 * Not yet set attributes and sub-attributes must have the value null.
 */
// export function create_form<Props extends Record<RecordKey, any>>(props: Props, persisted: boolean = false): IForm<Props> {
// 	//@ts-ignore
// 	const vals: IPropsForm<Props> = {};
// 	for (let prop in props) {
// 		vals[prop] = create_value_form(prop, props[prop]);
// 	}
// 	const form: IForm<Props> = {
// 		vals: vals,
// 		errs: [],
// 		persisted: persisted
// 	};
// 	return form;
// }

/**
 * creates a sub-form for one attribute without errors
 * 
 * Not yet set attributes and sub-attributes must have the value null.
 */
export function create_form<ID extends RecordKey, T extends FormSpec>(
	attr: ID, val: T
): IForm<ID, T>
{
	let formed_val: FormedValue<T>;
	if (Array.isArray(val)) {
		//@ts-ignore
		formed_val = create_form_array(val, persisted);
	} else if (typeof val === 'object' && val !== null) {
		if ('__type' in val) {
			//@ts-ignore
			return create_form(attr, val.__type);
		} else {
			//@ts-ignore
			formed_val = create_form_record(val);
		}
	} else {
		//@ts-ignore
		formed_val = val;
	}
	const form : Record<RecordKey, any> = {
		id: attr,
		val: formed_val,
		errs: [],
	};
	if (typeof val === 'object' && val !== null) {
		if ('__persisted' in val) {
			form.persisted = val.__persisted;
		}
		if ('__index' in val) {
			form.index = val.__index;
		}
		if ('__key' in val) {
			form.key = val.__key;
		}
	}
	//@ts-ignore
	return form;
}
function create_form_record<T extends FormSpec, R extends Record<RecordKey, T>>(
	record: R
): FormedRecord<R> {
	//@ts-ignore
	const obj: FormedRecord<R> = {};
	for (const key in record) {
		obj[key] = create_form(key, record[key]);
	}
	return obj;
}
function create_form_array<T extends FormSpec, A extends Array<T>>(
	arr: A
): FormedArray<A> {
	const form_values: FormedArray<A> = [];
	arr.forEach((elem, index) => {
		form_values[index] = create_form(index, elem);
	});
	return form_values;
}

// export function create_form_request_data(form: any) {
// 	const obj: Record<RecordKey, any> = {};
// 	for (const prop in form.vals) {
// 		obj[prop] = create_value_request_data(form.vals[prop].val);
// 	}
// 	return obj;
// }
export function create_request_data(form: any) {
	const form_val = form.val;
	// console.log(form_val)
	if (Array.isArray(form_val)) {
		console.log("array")
		return create_request_json_array(form_val);
	} else if (typeof form_val === 'object' && form_val !== null) {
		console.log("object", form_val)
		return create_request_json_record(form_val);
	} else {
		// console.log("simple")
		return create_request_json_simple(form_val);
	}
}
function create_request_json_record(form_value: any) {
	const obj: Record<RecordKey, any> = {};
	for (const key in form_value) {
		obj[key] = create_request_data(form_value[key]);
	}
	return obj;
}
function create_request_json_array(array_like_obj: any) {
	const arr: any[] = [];
	for (const index in array_like_obj) {
		//@ts-ignore
		arr[index] = create_request_data(array_like_obj[index]);
		console.log('hier', array_like_obj)
	}
	return arr;
}
function create_request_json_simple(form_value: any): any {
	return form_value;
}


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

// console.log(my_data);
// console.log(create_form('my-model', my_data));
// console.log(create_request_data(create_form('my-model', my_data)));
