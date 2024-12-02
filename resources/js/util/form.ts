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

type KeyWords = '__persisted'|'__index'|'__key';
type _Persisted = boolean;
type _Index = number;
type _Key = number|string;

type TypeSpec = number|string|boolean|null|undefined|bigint|({
	__persisted?: _Persisted,
	__index?: _Index,
	__key?: _Index,
}&{
	[key: RecordKey]: TypeSpec
})

export type IForm<ID extends RecordKey, T extends TypeSpec> = {
	id: ID,
	val: FormedValue<T>,
	errs: string[],
}
& (T extends { __persisted: infer P } ? { persisted: P } : {})
& (T extends { __index: infer I } ? { index: I } : {})
& (T extends { __key: infer K } ? { key: K }: {});


type MyForm = IForm<'xyz', {
	heading: string,
	__persisted: true,
	__index: number
}>;

const my_value: MyForm = {
	id: 'xyz',
	val: {
		heading: {
			id: 'heading',
			val: 'sdfsdf',
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
type FormedRecord<R extends Record<RecordKey, any>> = { [K in keyof R]: IForm<K, R[K]> };
type FormedArray<A extends Array<any>> = Array<IForm<number, A[number]>>;
type FormedSimpleValue<V> = V;

type FormedValue<T> =
	T extends Record<RecordKey, any>
	? ( T extends { __type: infer T2 }
		? FormedValue<T2>
		: FormedRecord<Omit<T, KeyWords>>
	) : (T extends Array<any>
		? FormedArray<T>
		: FormedSimpleValue<T>
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
export function create_form<ID extends RecordKey, T extends TypeSpec>(
	attr: ID, val: T, persisted: boolean = false
): IForm<ID, T>
{
	let formed_val: FormedValue<T>;
	if (Array.isArray(val)) {
		//@ts-ignore
		formed_val = create_form_array(val, persisted);
	} else if (typeof val === 'object' && val !== null) {
		//@ts-ignore
		formed_val = create_form_record(val, persisted);
	} else {
		//@ts-ignore
		formed_val = val;
	}
	return {
		id: attr,
		val: formed_val,
		errs: [],
		persisted: persisted,
	};
}
function create_form_record<R extends Record<RecordKey, any>>(record: R, persisted: boolean): FormedRecord<R> {
	//@ts-ignore
	const obj: FormedRecord<R> = {};
	for (const key in record) {
		obj[key] = create_form(key, record[key], persisted);
	}
	return obj;
}
function create_form_array<A extends Array<any>>(arr: A, persisted: boolean): FormedArray<A> {
	const form_values: FormedArray<A> = [];
	arr.forEach((elem, index) => {
		form_values[index] = create_form(index, elem, persisted);
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
		obj[key] = create_request_data(form_value[key]);
	}
	return obj;
}
function create_request_json_array(array_like_obj: any) {
	const arr: any[] = [];
	for (const index in array_like_obj) {
		//@ts-ignore
		arr[index] = create_request_data(array_like_obj[index]);
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

console.log(my_data);
console.log(create_form('my-model', my_data));
console.log(create_request_data(create_form('my-model', my_data)));
