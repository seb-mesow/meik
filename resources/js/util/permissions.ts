import { Permissions } from "@/types/permissions";
import { usePage } from "@inertiajs/vue3";
import { computed, ComputedRef } from "vue";

const page = usePage();

export const permissions: ComputedRef<Permissions> = computed(() => page.props.auth.permissions);
