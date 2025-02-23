<script setup lang="ts">
import { onMounted } from 'vue';


let dragged_elem: Element = null as any;

function on_dragstart(event: DragEvent) {
	// event.dataTransfer?.setData("text/plain", event.target?.id);
	// @ts-expect-error
	dragged_elem = event.target;
	dragged_elem.classList.add("dragging");
}
function on_dragend(event: DragEvent) {
	dragged_elem.classList.remove("dragging");
}

// @ts-expect-error
let container: Element = null;
onMounted(() => {
	// @ts-expect-error
	container = document.getElementById('container');
});

function on_dragover(event: DragEvent) {
	event.preventDefault();
	// @ts-expect-error
	event.dataTransfer.dropEffect = "move";
	const elem_below = determinate_closest_draggable_element_below(event);
	if (elem_below) {
		container.insertBefore(dragged_elem, elem_below);
	} else {
		container.appendChild(dragged_elem);
	}
}

function determinate_closest_draggable_element_below(drag_over_event: MouseEvent): Element|null {
	const elems: NodeListOf<Element> = container.querySelectorAll('.tile:not(.dragging)');
	let cur_smallest_distance_below_mouse: number = Number.POSITIVE_INFINITY;
	let closest_element_below: Element|null = null;
	const mouse_y = drag_over_event.clientY;
	let log_str = '';
	elems.forEach((elem, index) => {
		const box = elem.getBoundingClientRect();
		const distance_below_mouse = (box.top + box.bottom)/2 - mouse_y;
		if (distance_below_mouse > 0 && distance_below_mouse < cur_smallest_distance_below_mouse) {
			cur_smallest_distance_below_mouse = distance_below_mouse;
			closest_element_below = elem;
		}
		log_str += ' ' + Math.round(distance_below_mouse);
	});
	console.log(log_str);
	return closest_element_below;
}

</script>

<template>
	<div id="container" @dragover="on_dragover" >
		<div class="tile" draggable="true" id="tile-0" @dragstart="on_dragstart" @dragend="on_dragend">
			<span>Tile 1</span>
		</div>
		<div class="tile" draggable="true" id="tile-1" @dragstart="on_dragstart" @dragend="on_dragend">
			<span>Tile 2</span>
		</div>
		<div class="tile" draggable="true" id="tile-2" @dragstart="on_dragstart" @dragend="on_dragend">
			<span >Tile 3</span>
		</div>
		<div class="tile" draggable="true" id="tile-3" @dragstart="on_dragstart" @dragend="on_dragend">
			<span >Tile 4</span>
		</div>
		<div class="tile" draggable="true" id="tile-4" @dragstart="on_dragstart" @dragend="on_dragend">
			<span >Tile 5</span>
		</div>
		<div class="tile" draggable="true" id="tile-5" @dragstart="on_dragstart" @dragend="on_dragend">
			<span >Tile 6</span>
		</div>
	</div>
</template>

<style lang="css">
.tile {
	background-color: var(--p-sky-400);
	color: black;
	text-align: center;
	align-content: center;
	border-radius: 1rem;
	width: 15rem;
	height: 5rem;
	margin-top: .5rem;
	margin-bottom: .5rem;
	cursor: move;	
}
.tile.dragging {
	opacity: .5;
}
.slot {
	width: 15rem;
	height: 5rem;
	border-width: .15rem;
	border-style: dotted; 
	border-color: white;
	border-radius: 1rem;
	margin-top: .5rem;
	margin-bottom: .5rem;
	padding: 0;
}
.slot.target-slot {
	background-color: green;
}
</style>
