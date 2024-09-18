<?php
// exit if accessed directly
if(!defined('ABSPATH')) exit;

if(!class_exists('acf_field_date_time_picker')):

class acf_field_date_time_picker extends acf_field {
	/**
	 * Helper object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $dtp;
	
	/**
	 * Field Options
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $settings;
	
	/**
	 * Default field options
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
		$this->dtp = new acf_field_date_time_picker_common();
		$this->name = $this->dtp->name;
		$this->label = $this->dtp->label;
		$this->category = $this->dtp->category;
		
		$this->defaults = $this->dtp->defaults;
		
		add_action('init', array($this, 'init'));
		
    	parent::__construct();
		
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => ACF_DTP_VERSION,
		);
	}
	
	/**
	* Initialize translations
	*
	* @since 1.0.0
	*/
	public function init() {
		$this->l10n = $this->dtp->translations();
	}
	
	/**
	* Create extra options for your field. This is rendered when editing a field.
	*
	* @since 1.0.0
	*
	* @param array $field Field data
	*/
	public function create_options($field) {
		$field = array_merge($this->defaults, $field);		
		$key = $field['name'];
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Field type', 'acf-date-time-picker'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type' => 'radio',
					'layout' => 'horizontal',
					'name' => 'fields['.$key.'][field_type]',
					'value' => $field['field_type'],
					'choices' => array('date_time' => __('date & time', 'acf-date-time-picker'),
									   'time' => __('time only', 'acf-date-time-picker')),
				));
				?>
			</td>
		</tr>
		
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Date format', 'acf-date-time-picker'); ?></label>
				<p class="description"><?php _e('Read more about <a href="http://api.jqueryui.com/datepicker/#utility-formatDate" target="_blank">date formats</a>', 'acf-date-time-picker'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type' => 'text',
					'name' => 'fields['.$key.'][date_format]',
					'value' => $field['date_format'],
				));
				?>
			</td>
		</tr>
		
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Time format', 'acf-date-time-picker'); ?></label>
				<p class="description"><?php _e('Read more about <a href="http://trentrichardson.com/examples/timepicker/#tp-formatting" target="_blank">time formats</a>', 'acf-date-time-picker'); ?></p>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type' => 'text',
					'name' => 'fields['.$key.'][time_format]',
					'value' => $field['time_format'],
				));
				?>
			</td>
		</tr>
		
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Allow past dates?', 'acf-date-time-picker'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type' => 'radio',
					'layout' => 'horizontal',
					'name' => 'fields['.$key.'][past_dates]',
					'value' => $field['past_dates'],
					'choices' => array('yes' => __('yes', 'acf-date-time-picker'),
									   'no' => __('no', 'acf-date-time-picker'),
									  ),
				));
				?>
			</td>
		</tr>
		
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Timepicker type', 'acf-date-time-picker'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type' => 'radio',
					'layout' => 'horizontal',
					'name' => 'fields['.$key.'][time_selector]',
					'value' => $field['time_selector'],
					'choices' => array('select' => __('dropdown list', 'acf-date-time-picker'),
									  ),
				));
				?>
			</td>
		</tr>
		
		<?php
		global $wp_locale;
		$choices = array_values($wp_locale->weekday);
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Week starts on', 'acf-date-time-picker'); ?></label>
			</td>
			<td>
				<?php
				do_action('acf/create_field', array(
					'type' => 'select',
					'name' => 'fields['.$key.'][first_day]',
					'value' => $field['first_day'],
					'choices' => $choices,
				));
				?>
			</td>
		</tr>
		<?php
	}
	
	/**
	* Create the HTML interface for the field
	* 
	* @since 1.0.0
	*
	* @param array $field Field data
	*/	
	public function create_field($field) {
		$field = array_merge($this->defaults, $field);
		?>
		<div class="acf-date_time_picker" data-field-type="<?php echo esc_attr($field['field_type']); ?>"
										  data-date-format="<?php echo esc_attr($field['date_format']); ?>"
										  data-time-format="<?php echo esc_attr($field['time_format']); ?>"
										  data-first-day="<?php echo esc_attr($field['first_day']); ?>"
										  data-time-selector="<?php echo esc_attr($field['time_selector']); ?>"
										  data-past-dates="<?php echo esc_attr($field['past_dates']); ?>"
										  >
			<input type="text" name="<?php echo esc_attr($field['name']); ?>" value="<?php echo esc_attr($field['value']); ?>" class="input" />
		</div>
		<?php
	}
	
	/**
	* Enqueue admin JavaScript scripts and CSS stylesheets
	*
	* @since 1.0.0
	*/
	public function input_admin_enqueue_scripts() {
		wp_enqueue_style('acf-jquery-ui-timepicker', ACF_DTP_URL.'assets/css/jquery-ui-timepicker-addon.min.css', array(), ACF_DTP_VERSION);
		wp_enqueue_script('acf-jquery-ui-timepicker', ACF_DTP_URL.'assets/js/jquery-ui-timepicker/jquery-ui-timepicker-addon.min.js', array('jquery-ui-datepicker', 'jquery-ui-slider'), true, ACF_DTP_VERSION);
		wp_enqueue_script('acf-jquery-ui-slideraccess', ACF_DTP_URL.'assets/js/jquery-ui-timepicker/jquery-ui-sliderAccess.js', array('jquery-ui-slider'), true, ACF_DTP_VERSION);
		wp_enqueue_script('acf-input-date_time_picker', ACF_DTP_URL.'assets/js/input.js', array('acf-jquery-ui-timepicker'), true, ACF_DTP_VERSION);
	}
	
	/**
	* Convert date and time to the standard format (Y-m-d H:i:s) before saving it to database
	*
	* @since 1.0.0
	*
	* @param mixed $value The value which will be saved in the database
	* @param integer $post_id The post ID of which the value will be saved
	* @param array $field The field array holding all the field options
	* 
	* @return mixed The value which will be saved in the database
	*/
	public function update_value($value, $post_id, $field) {
		if(empty($value)) {
			return $value;
		}
		$field = array_merge($this->defaults, $field);
		if(preg_match('/^dd?\//', $field['date_format'])) { // if start with dd/ or d/ (not supported by strtotime())
			$value = str_replace('/', '-', $value);
		}
		$value = date('Y-m-d H:i:s', strtotime($value));
		return $value;
	}
	
	/**
	* Format date and time after it is loaded from the db
	*
	* @since 1.0.0
	*
	* @param mixed $value The value found in the database
	* @param mixed $post_id The post ID from which the value was loaded
	* @param array $field The field array holding all the field options
	* 
	* @return mixed The formatted date and time
	*/
	public function load_value($value, $post_id, $field) {
		$field = array_merge($this->defaults, $field);
		return $this->dtp->format_date_time($value, $field);
	}
	
}

// initialize field class
new acf_field_date_time_picker();

endif;
