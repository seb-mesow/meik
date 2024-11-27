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

type FormValues<P extends Record<RecordKey>> = {
	[K in keyof P]: _FormValues<K, P[K]>
}

export type Form<P extends Record<RecordKey, any>> = {
	vals: FormValues<P>,
	errs: string[]
}
