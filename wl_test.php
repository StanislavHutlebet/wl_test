<?php
/*
 * Plugin Name: WL TEST
 * Description: This describes my plugin in a short sentence
 * Version: 1.1.1

 * Author: Stas Hutlebet
 * Text Domain: wl_test
 */

class WL_test {
	private static $_instance;

	protected function __construct() {

        // action on ajax
		add_action( 'wp_ajax_wl_test', [ 'WL_test', 'onAjax' ] );
		add_action( 'wp_ajax_nopriv_wl_test', [ 'WL_test', 'onAjax' ] );

        // register scripts and styles
		add_action( 'wp_enqueue_scripts', function () {

            // bootstrap
			wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js' );
			wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css' );

            // main script
			wp_enqueue_script( 'wl_test', plugin_dir_url( __FILE__ ) . '/assets/scripts/wl_test.js' );

            // variables for script
			wp_localize_script( 'wl_test', 'wl_test_inf',
				[
					'url' => admin_url( 'admin-ajax.php' )
				]
			);
		} );

        // filters for register external page template
		add_filter( 'page_template', [ 'WL_test', 'pageTemplate' ] );
		add_filter( 'theme_page_templates', [ 'WL_test', 'addPageTemplateToSelect' ], 10, 4 );

	}

    // constructor
	public static function getInstance(): WL_test {
		if ( ! self::$_instance ) {
			self::$_instance = new static();
		}

		return self::$_instance;
	}

    // page template check
	public static function pageTemplate( $page_template ): string {
		if ( get_page_template_slug() == 'wl_test-page-template.php' ) {
			$page_template = dirname( __FILE__ ) . '/assets/views/wl_test-page-template.php';
		}

		return $page_template;
	}
    // add a page template to the pages templates
	public static function addPageTemplateToSelect( $post_templates ): array {
		$post_templates['wl_test-page-template.php'] = __( 'WL Test Page Template', 'wl_test' );

		return $post_templates;
	}

    // ajax
	public static function onAjax() {

        // @todo validation
		$post = $_POST;

        // action result template
		$result = [
			'status' => false,
            'status_message' => __('Empty data', 'wl_test'),
            //'post' => $post
		];

        // move data to data
        $data = $post['data'];

        //Check if user is logged in
        if (!is_user_logged_in()) {

            // check method
            switch ( $post['method'] ) {
                //login method
                case 'login' :
                    if (isset($data['email'])&&isset($data['password'])) {
                        // if email and password are not empty

                        // getting a user by email
                        $user = get_user_by('email', $data['email']);

                        // check if user in not error
                        if ($user&&!is_wp_error($user)) {

                            // compare user password
                            if (wp_check_password($data['password'], $user->data->user_pass)) {

                                // login user into system
                                self::$_instance->login($user->ID);

                                // set the status is valid and good
                                $result['status'] = true;
                                $result['status_message'] = __('You are logged in', 'wl_test');
                            } else {
                                $result['status_message'] = __('Wrong login or password', 'wl_test');
                            }
                        } else {
                            $result['status_message'] = __('Wrong login or password', 'wl_test');
                        }

                    } else {
                        $result['status_message'] = __('Login or password are not set', 'wl_test');
                    }
                    break;

                //register method
                case 'register' :
                    if (isset($data['email'])&&isset($data['password'])&&isset($data['company'])&&isset($data['position'])) {
                        // check if email, password, company and position are isset

                        // create user
                        $user_id = wp_create_user( $data['email'], $data['password'], $data['email'] );

                        // check if user was created
                        if ($user_id&&!is_wp_error($user_id)) {

                            // set the user role
                            // get User by ID
                            $user_id_role = new WP_User($user_id);
                            // set this user new role
                            $user_id_role->set_role(apply_filters(
                                // filter for external logic
                                'WL_test__default_user_role', // hook name
                                'subscriber', // default role subscriber
                                $user_id,
                                $data
                            ));

                            //add user metafields
                            update_user_meta($user_id, 'company', $data['company']);
                            update_user_meta($user_id, 'position', $data['position']);

                            // login user
                            self::$_instance->login($user_id);

                            // set the status is valid and good
                            $result['status'] = true;
                            $result['status_message'] = __('You are logged in', 'wl_test');
                        } else {
                            // set the status message by the error
                            $result['status_message'] = $user_id?$user_id->get_error_message():__('Unknown error', 'wl_test');
                        }
                    } else {
                        $result['status_message'] = __('Please check all fields', 'wl_test');
                    }
                    break;
            }

        } else {
            $result['status_message'] = __('You are logged in', 'wl_test');
        }

        // send the result
		wp_send_json($result);
	}

    public function login($user_id) : bool {
        // login user by user id
        $user = get_user_by('id', $user_id);
        if ($user&&!is_wp_error($user)) {
            // if user exists
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id); // sending user token for the frontend cookie
            do_action('wp_login', $user->user_login);
            return true;
        }
        return false;
    }

	public function __wakeup() {
        // can't wakeup
	}

	public function __clone() {
        // can't be clonned
	}

}

// init class
WL_test::getInstance();
