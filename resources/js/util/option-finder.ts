export interface IOption<I extends string|number> {
	id: I;
	name: string;
}

type FilterFunc = (searched_value: string, criteria: string) => boolean;
type DeterminateScannedValueFunc<O extends IOption<string|number>> = (option: O) => string;
type SearchInValue = 'id'|'name';

export interface IOptionFinder<I extends string|number, O extends IOption<string|number>> {
	find_all(criteria: string, search_in: SearchInValue, filter: FilterFunc): O[];
	find_all_by_many(criterias: (string|I)[], search_in: SearchInValue, filter: FilterFunc, ): O[];
	find_first(criteria: string, search_in: SearchInValue, filter: FilterFunc): O|undefined;
}

abstract class AbstractOptionFinder<I extends string|number, O extends IOption<string|number>> {
	private determinate_scanned_value_from_id: DeterminateScannedValueFunc<O>;
	private determinate_scanned_value_from_name: DeterminateScannedValueFunc<O>;
	private selectable_options: O[];
	
	public constructor(determinate_searched_value_from_id: DeterminateScannedValueFunc<O>, determinate_searched_value_from_name: DeterminateScannedValueFunc<O>, selectable_options: O[]|undefined) {
		this.determinate_scanned_value_from_id = determinate_searched_value_from_id;
		this.determinate_scanned_value_from_name = determinate_searched_value_from_name;
		this.selectable_options = selectable_options ?? [];
	}
	
	private get_determinate_scanned_value(search_in: SearchInValue): DeterminateScannedValueFunc<O> {
		if (search_in === 'id') {
			return this.determinate_scanned_value_from_id;
		}
		return this.determinate_scanned_value_from_name;
	}
	
	public find_all(criteria: string, search_in: SearchInValue, filter: FilterFunc): O[] {
		criteria = criteria.trim().toLowerCase();
		const determinate_scanned_value = this.get_determinate_scanned_value(search_in);
		return this.selectable_options.filter((option) => filter(determinate_scanned_value(option), criteria));
	}
	
	public find_all_by_many(criterias: (string|I)[], search_in: SearchInValue, filter: FilterFunc): O[] {
		const _criterias: string[] = criterias.map((criteria: string|I): string => criteria.toString().trim().toLowerCase());
		const determinate_scanned_value = this.get_determinate_scanned_value(search_in);
		return this.selectable_options.filter((option): boolean => {
			const scanned_value = determinate_scanned_value(option);
			return _criterias.some((criteria): boolean => filter(scanned_value, criteria));
		});
	}
	
	public find_first(criteria: string, search_in: SearchInValue, filter: FilterFunc): O|undefined {
		criteria = criteria.trim().toLowerCase();
		const determinate_scanned_value = this.get_determinate_scanned_value(search_in);
		return this.selectable_options.find((option) => filter(determinate_scanned_value(option), criteria));
	}
}

export class StringOptionFinder<O extends IOption<string>> extends AbstractOptionFinder<string, O> implements IOptionFinder<string, O> {
	public constructor(selectable_options: O[]|undefined) {
		super((option: O): string => option.id, (option: O): string => option.name.toLowerCase(), selectable_options);
	}
}

export class NumberOptionFinder<O extends IOption<number>> extends AbstractOptionFinder<number, O> implements IOptionFinder<number, O> {
	public constructor(selectable_options: O[]|undefined) {
		super((option: O): string => option.id.toString(), (option: O): string => option.name.toLowerCase(), selectable_options);
	}
}
