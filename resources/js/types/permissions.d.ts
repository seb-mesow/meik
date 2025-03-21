// siehe HandleInertiaRequests::determinate_permissions()
interface CrudPermissions {
	create: boolean,
	update: boolean,
	delete: boolean,
}

export interface Permissions {
	exhibit: CrudPermissions,
	place: CrudPermissions,
	location: CrudPermissions,
	rubric: CrudPermissions,
	user: CrudPermissions,
}
