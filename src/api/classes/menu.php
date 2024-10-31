<?php

class menu {
	const TOPLEVELSLUG = 'planaday-api';
	private $_className;
	private $options;

	public function __construct() {
		$this->_className = 'planaday-api';
		$this->options    = get_option( 'planaday-api-general' );
	}

	public static function planaday_api_get_instance() {
		static $instance;
		if ( $instance === null ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 *
	 */
	public function planaday_api_add_menu_page() {
		add_menu_page(
			'Planaday',
			'Planaday',
			'manage_options',
			self::TOPLEVELSLUG,
			[
				settings_welcome::planaday_api_get_instance(),
				'planaday_api_welcome_page'
			],
			plugins_url( 'planaday-api/assets/plugin.png' )
		);

		// Check: https://wordpress.stackexchange.com/questions/66498/add-menu-page-with-different-name-for-first-submenu-item
		add_submenu_page(
			self::TOPLEVELSLUG,
			'Planaday',
			'Welkom',
			'manage_options',
			self::TOPLEVELSLUG, // Use the same to prevent double entry
			[
				settings_welcome::planaday_api_get_instance(),
				'planaday_api_welcome_page'
			]
		);

		add_submenu_page(
			self::TOPLEVELSLUG,
			'Planaday',
			'Instellingen',
			'manage_options',
			'planaday-api-general',
			[
				settings_general::planaday_api_get_instance(),
				'planaday_api_admin_page'
			]
		);

		if ( isset( $this->options['betalingenactief'] )
		     && $this->options['betalingenactief'] === '1' ) {
			add_submenu_page(
				self::TOPLEVELSLUG,
				'Planaday',
				'Betalingen',
				'manage_options',
				'planaday-api-payment',
				[
					settings_payment::planaday_api_get_instance(),
					'planaday_api_payment_page'
				]
			);
		}

		add_submenu_page(
			self::TOPLEVELSLUG,
			'Planaday',
			'CSS',
			'manage_options',
			'planaday-api-css',
			[
				settings_css::planaday_api_get_instance(),
				'planaday_api_css_page'
			]
		);

		if ( ! empty( $this->options['url'] )
		     && ! empty( $this->options['key'] ) ) {
			add_submenu_page(
				self::TOPLEVELSLUG,
				'Database',
				'Database',
				'manage_options',
				'planaday-api-database',
				[
					settings_database::planaday_api_get_instance(),
					'planaday_api_show_database'
				]
			);
		}

		if ( ! empty( $this->options['url'] )
		     && ! empty( $this->options['key'] ) ) {
			add_submenu_page(
				self::TOPLEVELSLUG,
				'Support',
				'Support',
				'manage_options',
				'planaday-api-support',
				[
					settings_support::planaday_api_get_instance(),
					'planaday_api_support'
				]
			);
		}
	}
}

