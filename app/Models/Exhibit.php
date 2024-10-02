<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\Date;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class Exhibit
{
	private string $designation;
	private string $inventory_number;
	private string $manufacturer;
	private string $year_of_construction;
	private string $location;
	private Date $aquiry_date;
	
	
}
