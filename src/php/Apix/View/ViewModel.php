<?php
namespace Apix\View;

use Apix\View\ViewModel as Model;

class ViewModel
{

	/**
	 * Default View Model key.
	 */
	public static $default_key = null;

	/**
	 * Default View Model class.
	 */
	public static $default_class = 'Apix\View\ViewModel';

	/**
	 * Assigns a property.
	 *
	 *     // This value can be accessed as {{foo}} within the template
	 *     $view->set('foo', 'my value');
	 *
	 * You can also use an array to set several values at once:
	 *
	 *     // Create the values {{food}} and {{beverage}} in the template
	 *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
	 *
	 * @param   string|array  	variable name or an array of variables
	 * @param   mix				value
	 * @return  $this
	 */
	public function set($mix, $value = null)
	{
		if (is_array($mix)) {
			$mix = isset($mix[self::$default_key])
					? $mix[self::$default_key]
					: array();

			foreach ($mix as $name => $value) {
				$this->{$name} = $value;
			}
		} else {
			$this->{$mix} = $value;
		}
		return $this;
	}

	/**
	 * Assigns a value by reference. The benefit of binding is that values can
	 * be altered without re-setting them. It is also possible to bind variables
	 * before they have values. Assigned values will be available as a
	 * variable within the template file:
	 *
	 *     // This reference can be accessed as {{ref}} within the template
	 *     $view->bind('ref', $bar);
	 *
	 * @param   string   variable name
	 * @param   mixed    referenced variable
	 * @return  $this
	 */
	public function bind($key, & $value)
	{
		$this->{$key} =& $value;

		return $this;
	}

	/* generic helpers */

    public function hasMany($name)
    {
    	if(isset($this->{$name})) {
	        return count($this->{$name})>1 ? true : false;
    	}
    }

    public function getLayout()
    {
    	return $this->_layout;
    }

    public function debug($data=null)
    {
    	$data = null !== $data ? $data : $this;

        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

}