export interface IFreeTextInitPageProps {
	id: number,
	errs?: string[],
	val: {
		heading: {
			val: string,
			errs?: string[]
		},
		html: {
			val: string,
			errs?: string[]
		}
		is_public: {
			val: boolean,
			errs?: string[]
		}
	}
}
