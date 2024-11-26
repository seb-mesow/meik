<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\Date;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;

class Exhibit
{
	/** @Accessor(getter="get__id") */
	private ?string $_id = null;

	/** @Accessor(getter="get__rev") */
	private ?string $_rev = null;

	/** @Accessor(getter="get_name") */
	private ?string $name = null;

	/** @Accessor(getter="get_inventory_number") */
	private ?string $inventory_number = null;

	/** @Accessor(getter="get_manufacturer") 
	 */
	private ?string $manufacturer = null;

	/** @Accessor(getter="get_year_of_construction") */
	private ?string $year_of_construction = null;

	/** @Accessor(getter="get_place") */
	private ?string $place = null;

	/** @Accessor(getter="get_aquiry_date") 
	 * @Type("DateTime")
	 */
	private ?DateTime $aquiry_date = null;

	/** @Accessor(getter="get_free_text_fields") 
	 * @Serializer\SerializedName("freetextfield")
	 */
	private ?array $free_text_fields = [];

	/** @Accessor(getter="get_connected_exhibits") 
	 */
	private ?array $connected_exhibits = [];

	/**
	 *  @Accessor(getter="get_original_price") 
	 */
	private ?price $original_price = null;

	/**
	 *  @Accessor(getter="get_current_value") 
	 */
	private ?float $current_value = 0;

	/**
	 * Get the value of id
	 */
	public function get__id()
	{
		return $this->_id;
	}

	/**
	 * Set the value of id
	 *
	 * @return  self
	 */
	public function set__id($_id)
	{
		$this->_id = $_id;

		return $this;
	}

	/**
	 * Get the value of name
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @return  self
	 */
	public function set_name($name)
	{
		$this->name = $name;

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
	 * Get the value of place
	 */
	public function get_place()
	{
		return $this->place;
	}

	/**
	 * Set the value of place
	 *
	 * @return  self
	 */
	public function set_place($place)
	{
		$this->place = $place;

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
	public function get__rev()
	{
		return $this->_rev;
	}

	/**
	 * Set the value of _rev
	 *
	 * @return  self
	 */
	public function set__rev($_rev)
	{
		$this->_rev = $_rev;

		return $this;
	}
	
	/**
	 * Get the value of free_text_fields
	 */
	public function get_free_text_fields()
	{
		return $this->free_text_fields;
	}

	/**
	 * Set the value of free_text_fields
	 *
	 * @return  self
	 */
	public function set_free_text_fields($free_text_fields)
	{
		$this->free_text_fields = $free_text_fields;

		return $this;
	}

	/**
	 * Get the value of connected_exhibits
	 */
	public function get_connected_exhibits()
	{
		return $this->connected_exhibits;
	}

	/**
	 * Set the value of connected_exhibits
	 *
	 * @return  self
	 */
	public function set_connected_exhibits($connected_exhibits)
	{
		$this->connected_exhibits = $connected_exhibits;

		return $this;
	}

	/**
	 * Get the value of original_price
	 */
	public function get_original_price()
	{
		return $this->original_price;
	}

	/**
	 * Set the value of original_price
	 *
	 * @return  self
	 */
	public function set_original_price($original_price)
	{
		$this->original_price = $original_price;

		return $this;
	}

	/**
	 * Get the value of current_value
	 */ 
	public function get_current_value()
	{
		return $this->current_value;
	}

	/**
	 * Set the value of current_value
	 *
	 * @return  self
	 */ 
	public function set_current_value($current_value)
	{
		$this->current_value = $current_value;

		return $this;
	}
}
