<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void {
		Schema::table('sessions', function (Blueprint $table) {
			$table->string('user_id', 255)->nullable()->comment('original_username')->change();
		});
	}

	public function down(): void {
		Schema::table('sessions', function (Blueprint $table) {
			$table->bigInteger('user_id')->nullable()->unsigned()->change();
		});
	}
};
