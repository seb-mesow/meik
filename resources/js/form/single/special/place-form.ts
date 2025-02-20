import { ReactiveFlags } from "vue";
import { ISelectForm, ISelectFormConstructorArgs, SelectForm } from "../generic/select-form";
import { ISingleValueForm2Parent } from "../generic/single-value-form2";

export type IPlace = Readonly<{
	id: string,
	name: string,
}>;

export interface IPlaceForm extends ISelectForm<IPlace> {
	set_selectable_places(selectable_places: IPlace[]): void;
}

export interface IPlaceFormConstructorArgs extends Pick<ISelectFormConstructorArgs<IPlace>, 'val'|'required'|'on_change'|'selectable_options'> {
}

export class PlaceForm extends SelectForm<IPlace> implements IPlaceForm {
	
	public constructor(args: IPlaceFormConstructorArgs, id: string|number, parent: ISingleValueForm2Parent<IPlace>) {
		const _args: ISelectFormConstructorArgs<IPlace> = {
			...args,
			...{
				search_in: 'name',
				optionLabel: 'name',
				validate: PlaceForm.get_validate(args.selectable_options ?? []),
			},
		};
		super(_args, id, parent);
	}
	
	public async set_selectable_places(selectable_places: IPlace[]): Promise<void> {
		this.selectable_options = selectable_places;
		this.set_validate(PlaceForm.get_validate(selectable_places));
	}
	
	private static get_validate(selectable_places: IPlace[]): (value_in_editing: IPlace|null|undefined) => Promise<string[]> {
		return async (value_in_editing) => {
			if (value_in_editing === undefined) {
				return ['Bitte einen auswählbaren Platz angeben'];
			}
			if (value_in_editing === null) {
				return [];
			}
			if (selectable_places.some((place) => place.id === value_in_editing.id)) {
				return [];
			}
			return ['Bitte einen auswählbaren Platz angeben'];
		}
	}
}
