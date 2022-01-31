<?php
/*
 * Plugin Name: WL TEST
 * Description: This describes my plugin in a short sentence
 * Version: 1.1.1
 * Author: Stas Hutlebet

 * Text Domain: wl_test
 * Domain Path: /languages
 */

class WL_test {
	private static $_instance;

	protected function __construct() {

		add_action( 'wp_ajax_wl_test', [ 'WL_test', 'onAjax' ] );
		add_action( 'wp_ajax_nopriv_wl_test', [ 'WL_test', 'onAjax' ] );
		add_action( 'wp_enqueue_scripts', function () {
			wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js' );
			wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css' );

			wp_enqueue_script( 'wl_test', plugin_dir_url( __FILE__ ) . '/assets/scripts/wl_test.js' );
			wp_localize_script( 'wl_test', 'wl_test_inf',
				[
					'url' => admin_url( 'admin-ajax.php' )
				]
			);
		} );
		add_filter( 'page_template', [ 'WL_test', 'page_template' ] );
		add_filter( 'theme_page_templates', [ 'WL_test', 'add_page_template_to_select' ], 10, 4 );

	}

	public static function getInstance(): WL_test {
		if ( ! self::$_instance ) {
			self::$_instance = new static();
		}

		return self::$_instance;
	}

	public static function page_template( $page_template ): string {
		if ( get_page_template_slug() == 'wl_test-page-template.php' ) {
			$page_template = dirname( __FILE__ ) . '/assets/views/wl_test-page-template.php';
		}

		return $page_template;
	}

	public static function add_page_template_to_select( $post_templates ): array {
		$post_templates['wl_test-page-template.php'] = __( 'WL Test Page Template', 'wl_test' );

		return $post_templates;
	}

	public static function onAjax() {
		$post = $_POST;
		$result = [
			'status' => false,
            'status_message' => __('Empty data', 'wl_test'),
            'post' => $post
		];

        $data = $post['data'];

        if (!is_user_logged_in()) {

            switch ( $post['method'] ) {
                case 'login' :
                    if (isset($data['email'])&&isset($data['password'])) {
                        $user = get_user_by('email', $data['email']);
                        if ($user&&!is_wp_error($user)) {
                            if (wp_check_password($data['password'], $user->data->user_pass)) {
                                self::$_instance->login($user->ID);
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
                case 'register' :
                    if (isset($data['email'])&&isset($data['password'])&&isset($data['company'])&&isset($data['position'])) {
                        $user_id = wp_create_user( $data['email'], $data['password'], $data['email'] );
                        if ($user_id&&!is_wp_error($user_id)) {
                            $user_id_role = new WP_User($user_id);
                            $user_id_role->set_role(apply_filters(
                                'WL_test__default_user_role',
                                'subscriber',
                                $user_id,
                                $data
                            ));
                            update_user_meta($user_id, 'company', $data['company']);
                            update_user_meta($user_id, 'position', $data['position']);
                            self::$_instance->login($user_id);
                            $result['status'] = true;
                            $result['status_message'] = __('You are logged in', 'wl_test');
                        } else {
                            $result['status_message'] = $user_id->get_error_message();
                        }
                    } else {
                        $result['status_message'] = __('Please check all fields', 'wl_test');
                    }
                    break;
            }

        } else {
            $result['status_message'] = __('You are logged in', 'wl_test');
        }
		wp_send_json($result);
	}

    public function login($user_id) {
        $user = get_user_by('id', $user_id);
        if ($user&&!is_wp_error($user)) {
            $user_login = $user->user_login;
            wp_set_current_user($user_id, $user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user_login);
        }
    }

	public function __wakeup() {

	}

	public function __clone() {

	}

}

WL_test::getInstance();

