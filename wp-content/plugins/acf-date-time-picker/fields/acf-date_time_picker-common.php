<?php
// exit if accessed directly
if(!defined('ABSPATH')) exit;

if(!class_exists('acf_field_date_time_picker_common')):

class acf_field_date_time_picker_common {
	/**
	* Field name
	*
	* @since 1.0.0
	*
	* @var string
	*/
	public $name;
	
	/**
	* Field label
	*
	* @since 1.0.0
	*
	* @var string
	*/
	public $label;
	
	/**
	* Field category
	*
	* @since 1.0.0
	*
	* @var string
	*/
	public $category;
	
	/**
	* Default field settings
	*
	* @since 1.0.0
	*
	* @var array
	*/
	public $defaults;
	
	/**
	* Class constructor
	*
	* @since 1.0.0
	*/
	public function __construct() {
		$this->name = 'date_time_picker';
		$this->label = __('Date & Time Picker', 'acf-date-time-picker');
		$this->category = 'jQuery';
		
		$this->defaults = array(
			'field_type' => 'date_time',
			'date_format' => 'yy-mm-dd',
			'time_format' => 'HH:mm',
			'first_day' => 1,
			'time_selector' => 'slider',
			'past_dates' => 'yes',
		);
	}
	
	/**
	* Initialize translations
	*
	* @since 1.0.0
	*
	* @return array Translations
	*/
	public function translations() {
		global $wp_locale;
		
		return array(
			'closeText'         => __('Done', 'acf-date-time-picker'),
			'currentText'       => __('Now', 'acf-date-time-picker'),
			'monthNames'        => array_values($wp_locale->month),
			'monthNamesShort'   => array_values($wp_locale->month_abbrev),
			'monthStatus'       => __('Show a different month', 'acf-date-time-picker'),
			'dayNames'          => array_values($wp_locale->weekday),
			'dayNamesShort'     => array_values($wp_locale->weekday_abbrev),
			'dayNamesMin'       => array_values($wp_locale->weekday_initial),
			'isRTL'             => isset($wp_locale->is_rtl) ? $wp_locale->is_rtl : false,
			'timeOnlyTitle'		=> __('Choose Time', 'acf-date-time-picker'),
			'timeText'			=> __('Time', 'acf-date-time-picker'),
			'hourText'			=> __('Hour', 'acf-date-time-picker'),
			'minuteText'		=> __('Minute', 'acf-date-time-picker'),
			'secondText'		=> __('Second', 'acf-date-time-picker'),
		);
	}
	
	/**
	* Format date and time
	*
	* @since 1.0.0
	*
	* @param mixed $value The value found in the database
	* @param string $format The date and time format
	*
	* @return string The formatted date and time
	*/
	public function format_date_time($value, $field) {
		if(empty($value)) {
			return $value;
		}
		if($field['field_type'] == 'time') {
			$format = $this->time_format_js_to_php($field['time_format']);
		}
		else {
			$format = $this->date_format_js_to_php($field['date_format']).' '.$this->time_format_js_to_php($field['time_format']);
		}
		return date_i18n($format, strtotime($value));
	}

	/**
	* Convert JS date format string to PHP
	*
	* @since 1.0.0
	*
	* @param string $format JS date format string
	* 
	* @return string PHP date format string
	*/
	public function date_format_js_to_php($format) {
		$t = array(
			// day
			'dd' => 'd',
			'd' => 'j',
			'DD' => 'l',
			'D' => 'D',
			'o' => 'z',
			// month
			'mm' => 'm',
			'm' => 'n',
			'MM' => 'F',
			'M' => 'M',
			// year
			'yy' => 'Y',
			'y' => 'y',
			);
		return strtr((string)$format, $t);
	}
	
	/**
	* Convert JS time format string to PHP
	*
	* @since 1.0.0
	*
	* @param string $format JS time format string
	* 
	* @return string PHP time format string
	*/
	public function time_format_js_to_php($format) {
		$t = array(
			// hour
			'HH' => 'H',
			'H' => 'G',
			'hh' => 'h',
			'h' => 'g',
			// minute
			'mm' => 'i',
			'm' => 'i',
			// second
			'ss' => 's',
			's' => 's',
			// am / pm
			'tt' => 'a',
			't' => 'a',
			'TT' => 'A',
			'T' => 'A',
			);
		return strtr((string)$format, $t);
	}
	
}

endif;
