<?php
/**
 * TwoCoda Core Acf.
 *
 * @since   0.0.1
 * @package TwoCoda_Core
 */

/**
 * TwoCoda Core Acf.
 *
 * @since 0.0.1
 */
class TC_Acf {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.1
	 *
	 * @var   TwoCoda_Core
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.1
	 *
	 * @param  TwoCoda_Core $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
		$this->create_acf_options_pages();
		$this->create_acf_policy_fields();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {

	}

	/**
	 * Create the options pages to be populated by ACF
	 *
	 * @return void
	 */
	public function create_acf_options_pages() {
		if( function_exists('acf_add_options_page') ) {
	
			acf_add_options_page(array(
				'page_title' 	=> 'Policy Customizer',
				'menu_title'	=> 'Policies',
				'menu_slug' 	=> 'twocoda-policies',
				'capability'	=> 'edit_posts',
				'redirect'		=> false
			));
		}
	}

	/**
	 * Define all ADF policy fields.
	 *
	 *
	 * @return void
	 */
	public function create_acf_policy_fields() {
		//TODO: Replace keys with friendlier names
		if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array(
				'key' => 'group_5d6f0135f3996',
				'title' => 'Custom Policies',
				'fields' => array(
					array(
						'key' => 'field_5d6f1804d832c',
						'label' => '',
						'name' => '',
						'type' => 'message',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => 'Welcome to the Policy Customizer!
			
			By answering a few questions, you can generate a policy statement that your students must agree to before booking their first lesson with you.
			
			Please note that existing students will be notified when these policies are changed.
			<hr>',
						'new_lines' => 'wpautop',
						'esc_html' => 0,
					),
					array(
						'key' => 'field_5d6f0a1b6e7fc',
						'label' => 'Lessons',
						'name' => '',
						'type' => 'tab',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'placement' => 'top',
						'endpoint' => 0,
					),
					array(
						'key' => 'field_5d6f014df03c5',
						'label' => 'Lesson Types',
						'name' => 'lesson_types',
						'type' => 'repeater',
						'instructions' => 'Here, you can specify the various lengths of lessons you offer, and how much those lessons cost.',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '50',
							'class' => '',
							'id' => '',
						),
						'collapsed' => 'field_5d6f0701f03c6',
						'min' => 1,
						'max' => 0,
						'layout' => 'table',
						'button_label' => 'Add Lesson',
						'sub_fields' => array(
							array(
								'key' => 'field_5d6f0701f03c6',
								'label' => 'Length (in minutes)',
								'name' => 'length_in_minutes',
								'type' => 'number',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => 30,
								'placeholder' => '',
								'prepend' => '',
								'append' => 'minutes',
								'min' => '',
								'max' => '',
								'step' => 5,
							),
							array(
								'key' => 'field_5d6f077ff03c7',
								'label' => 'Cost',
								'name' => 'cost',
								'type' => 'number',
								'instructions' => 'This will appear in your local currency. ($, £, €, etc).',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => 30,
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'min' => '',
								'max' => '',
								'step' => 1,
							),
						),
					),
					array(
						'key' => 'field_5d6f0a82ad21d',
						'label' => 'Display prices on website?',
						'name' => 'display_prices_on_website',
						'type' => 'radio',
						'instructions' => 'Choose whether you wish to display your prices publically on your website.',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'Public' => 'Public',
							'Logged-in Students Only' => 'Logged-in Students Only',
							'Hidden' => 'Hidden',
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'Hidden',
						'layout' => 'horizontal',
						'return_format' => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key' => 'field_5d6f0b3876ba7',
						'label' => 'Scheduling',
						'name' => '',
						'type' => 'tab',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'placement' => 'top',
						'endpoint' => 0,
					),
					array(
						'key' => 'field_5d6f0b5176ba8',
						'label' => 'How can students schedule their lessons?',
						'name' => 'how_can_students_schedule_their_lessons',
						'type' => 'radio',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'open' => 'Open: Anyone can register as a student, and then schedule lessons on my calendar.',
							'contact' => 'New students must contact me first. After I approve them, however, they can manage their own schedule according to my availability.',
							'closed' => 'New students must contact me to start lessons, and I also want to manage my schedule myself',
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'contact',
						'layout' => 'vertical',
						'return_format' => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key' => 'field_5d6f0e87f9f7f',
						'label' => 'How far in advance must students book or change a lesson time?',
						'name' => 'how_far_in_advance_must_students_book_or_change_a_lesson_time',
						'type' => 'number',
						'instructions' => 'You can override this manually.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '25',
							'class' => '',
							'id' => '',
						),
						'default_value' => 24,
						'placeholder' => '',
						'prepend' => '',
						'append' => 'hours',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5d6f153429adb',
						'label' => 'How do students pay for their lessons?',
						'name' => 'how_do_students_pay_for_their_lessons',
						'type' => 'radio',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'one_off' => 'They pay for one lesson at the time of booking.',
							'after_lesson' => 'I take payment at the time of the lesson (check, cash, or run their card).',
							'package' => 'They must prepay for a specific amount of lessons (e.g. a "package").',
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'package',
						'layout' => 'vertical',
						'return_format' => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key' => 'field_5d6f168d29adc',
						'label' => 'Minimum number of prepaid lessons',
						'name' => 'minimum_number_of_prepaid_lessons',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d6f153429adb',
									'operator' => '==',
									'value' => 'package',
								),
							),
						),
						'wrapper' => array(
							'width' => '25',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => 'lessons.',
						'min' => 1,
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5d6f112dd8095',
						'label' => 'Cancellation',
						'name' => '',
						'type' => 'tab',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'placement' => 'top',
						'endpoint' => 0,
					),
					array(
						'key' => 'field_5d6f11c3d8096',
						'label' => 'Students must contact me directly to cancel a lesson.',
						'name' => 'students_must_contact_me_directly_to_cancel_a_lesson',
						'type' => 'radio',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'no' => 'No, they can cancel lessons themselves and leave a reason via the calendar.',
							'yes' => 'Yes, they need to contact me to cancel.',
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'no',
						'layout' => 'vertical',
						'return_format' => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key' => 'field_5d6f1229d8097',
						'label' => 'How far in advance can students cancel without penalty?',
						'name' => 'how_far_in_advance_must_students_cancel',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '30',
							'class' => '',
							'id' => '',
						),
						'default_value' => 24,
						'placeholder' => '',
						'prepend' => '',
						'append' => 'hours',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5d6f12d7ae25b',
						'label' => 'What penalty is incurred when students cancel after this period?',
						'name' => 'what_penalty_is_incurred_when_students_cancel_after_this_period',
						'type' => 'radio',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'none' => 'No penalty.',
							'percent' => 'They forfeit a percentage of the lesson cost.',
							'flat_fee' => 'They forfeit a flat cancellation fee.',
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'percent',
						'layout' => 'vertical',
						'return_format' => 'value',
						'save_other_choice' => 0,
					),
					array(
						'key' => 'field_5d6f13e108fe2',
						'label' => 'Percent of Lesson Cost',
						'name' => 'percent_of_lesson_cost',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d6f12d7ae25b',
									'operator' => '==',
									'value' => 'percent',
								),
							),
						),
						'wrapper' => array(
							'width' => '25',
							'class' => '',
							'id' => '',
						),
						'default_value' => 100,
						'placeholder' => '',
						'prepend' => '',
						'append' => '%',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5d6f144a08fe3',
						'label' => 'Flat Cancellation Fee',
						'name' => 'flat_cancellation_fee',
						'type' => 'number',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d6f12d7ae25b',
									'operator' => '==',
									'value' => 'flat_fee',
								),
							),
						),
						'wrapper' => array(
							'width' => '25',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '$',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'twocoda-policies',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'seamless',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			endif;
	}
}
