export interface IElement<I extends string|number> {
	id?: I;
};

export function update_order_from_partial_order<I extends string|number, T extends IElement<I>>(cur_order: T[], new_partial_order: I[]): T[] {
	const partial_order_map: Map<I, number> = new Map();
	const sorted_chunks: T[][] = [];
	let i;
	for (i = 0; i < new_partial_order.length; i++) {
		partial_order_map.set(new_partial_order[i], i);
		sorted_chunks.push([]);
	}
	
	const leading_elems: T[] = [];
	i = 0;
	let elem: T;
	let prev_new_index: number|undefined = undefined;
	// copy leading elements not in partial order in own chunk
	while (i < cur_order.length) {
		elem = cur_order[i++];
		if (elem.id !== undefined) {
			prev_new_index = partial_order_map.get(elem.id);
			if (prev_new_index !== undefined) {
				break;
			}
		}
		leading_elems.push(elem);
	}
	
	// We can stop early, if no element is in the partial order.
	if (prev_new_index === undefined) {
		return leading_elems;
	}
	
	// @ts-expect-error At this point prev_new_index !== undefined implies elem !== undefined .
	let cur_chunk: T[] = [ elem ];
	let cur_new_index: number|undefined;
	
	// create chunks based on the partial order
	while (i < cur_order.length) {
		elem = cur_order[i++];
		if (elem.id !== undefined) {
			cur_new_index = partial_order_map.get(elem.id);
			if (cur_new_index !== undefined) {
				// if elem in partial order, then start new chunk
				sorted_chunks[prev_new_index] = cur_chunk; // Here the cur_chunk always has at least one elem.
				cur_chunk = [ elem ];
				prev_new_index = cur_new_index;
				continue;
			}
		}
		// if elem not in partial order, then add it to the current chunk
		cur_chunk.push(elem);
	}
	sorted_chunks[prev_new_index] = cur_chunk;
	
	// only assertation
	for (i = 0; i < sorted_chunks.length; i++) {
		if (sorted_chunks[i].length < 1) {
			throw new Error('Assertation failed: new_partial_order contains elements that are not in cur_order');
		}
	}
	
	// flatten the sorted chunks into a single array
	return leading_elems.concat(sorted_chunks.flat());
}
