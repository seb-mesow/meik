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
	public const ID_PREFIX = "exhibit:";

	private string $_id;
	private string $_rev;
	private string $designation;
	private string $inventory_number;
	private string $manufacturer;
	private string $year_of_construction;
	private string $location;
	private Date $aquiry_date;
	
	/**
	 * Get the value of designation
	 */ 
	public function get__id()
	{
		return $this->_id;
	}

	/**
	 * Set the value of designation
	 *
	 * @return  self
	 */ 
	public function set__id($_id)
	{
		$this->_id = self::ID_PREFIX.$_id;

		return $this;
	}

	/**
	 * Get the value of designation
	 */ 
	public function get_designation()
	{
		return $this->designation;
	}

	/**
	 * Set the value of designation
	 *
	 * @return  self
	 */ 
	public function set_designation($designation)
	{
		$this->designation = $designation;

		return $this;
	}

	/**
	 * Get the value of inventory_number
	 */ 
	public function get_inventory_number()
	{
		return $this->inventory_number;
	}

	/**
	 * Set the value of inventory_number
	 *
	 * @return  self
	 */ 
	public function set_inventory_number($inventory_number)
	{
		$this->inventory_number = $inventory_number;

		return $this;
	}

	/**
	 * Get the value of manufacturer
	 */ 
	public function get_manufacturer()
	{
		return $this->manufacturer;
	}

	/**
	 * Set the value of manufacturer
	 *
	 * @return  self
	 */ 
	public function set_manufacturer($manufacturer)
	{
		$this->manufacturer = $manufacturer;

		return $this;
	}

	/**
	 * Get the value of year_of_construction
	 */ 
	public function get_year_of_construction()
	{
		return $this->year_of_construction;
	}

	/**
	 * Set the value of year_of_construction
	 *
	 * @return  self
	 */ 
	public function set_year_of_construction($year_of_construction)
	{
		$this->year_of_construction = $year_of_construction;

		return $this;
	}

	/**
	 * Get the value of location
	 */ 
	public function get_location()
	{
		return $this->location;
	}

	/**
	 * Set the value of location
	 *
	 * @return  self
	 */ 
	public function set_location($location)
	{
		$this->location = $location;

		return $this;
	}

	/**
	 * Get the value of aquiry_date
	 */ 
	public function get_aquiry_date()
	{
		return $this->aquiry_date;
	}

	/**
	 * Set the value of aquiry_date
	 *
	 * @return  self
	 */ 
	public function set_aquiry_date($aquiry_date)
	{
		$this->aquiry_date = $aquiry_date;

		return $this;
	}

	/**
	 * Get the value of _rev
	 */ 
	public function get_rev()
	{
		return $this->_rev;
	}

	/**
	 * Set the value of _rev
	 *
	 * @return  self
	 */ 
	public function set_rev($_rev)
	{
		$this->_rev = $_rev;

		return $this;
	}
}