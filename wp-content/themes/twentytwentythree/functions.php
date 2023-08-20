<?php
/**
 * Twenty Twenty-Three functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Three
 * @since Twenty Twenty-Three 1.0
 */

add_action( 'wp_enqueue_scripts', 'custom_enqueue_styles');

function custom_enqueue_styles() {
	wp_enqueue_style( 'custom-style', 
					  get_template_directory_uri() . '/custom.css', 
					  array(), 
					  wp_get_theme()->get('Version')
					);
}

remove_action( 'um_after_login_fields', 'um_after_login_submit', 1001 );
function custom_um_after_login_submit( $args ) {
	if ( empty( $args['forgot_pass_link'] ) ) {
		return;
	} ?>

	<div class="um-col-alt-b">
		<a href="<?php echo esc_url( um_get_core_page( 'password-reset' ) ); ?>" class="um-link-alt">
			<?php _e( '¿Olvidaste tu contraseña?', 'ultimate-member' ); ?>
		</a>
	</div>

	<?php
}
add_action( 'um_after_login_fields', 'custom_um_after_login_submit', 1001 );


remove_action( 'um_submit_form_errors_hook_login', 'um_submit_form_errors_hook_login' );
function custom_um_submit_form_errors_hook_login( $submitted_data ) {
	$user_password = $submitted_data['user_password'];

	if ( isset( $submitted_data['username'] ) && $submitted_data['username'] == '' ) {
		UM()->form()->add_error( 'username', __( 'Ingrese el usuario o el correo', 'ultimate-member' ) );
	}

	if ( isset( $submitted_data['user_login'] ) && $submitted_data['user_login'] == '' ) {
		UM()->form()->add_error( 'user_login', __( 'Ingrese el usuario', 'ultimate-member' ) );
	}

	if ( isset( $submitted_data['user_email'] ) && $submitted_data['user_email'] == '' ) {
		UM()->form()->add_error( 'user_email', __( 'Ingrese el correo', 'ultimate-member' ) );
	}

	if ( isset( $submitted_data['username'] ) ) {
		$authenticate = $submitted_data['username'];
		$field = 'username';
		if ( is_email( $submitted_data['username'] ) ) {
			$data = get_user_by('email', $submitted_data['username'] );
			$user_name = isset( $data->user_login ) ? $data->user_login : null;
		} else {
			$user_name  = $submitted_data['username'];
		}
	} elseif ( isset( $submitted_data['user_email'] ) ) {
		$authenticate = $submitted_data['user_email'];
		$field = 'user_email';
		$data = get_user_by('email', $submitted_data['user_email'] );
		$user_name = isset( $data->user_login ) ? $data->user_login : null;
	} else {
		$field = 'user_login';
		$user_name = $submitted_data['user_login'];
		$authenticate = $submitted_data['user_login'];
	}

	if ( $submitted_data['user_password'] == '' ) {
		UM()->form()->add_error( 'user_password', __( 'Ingrese su contraseña', 'ultimate-member' ) );
	}

	$user = get_user_by( 'login', $user_name );
	if ( $user && wp_check_password( $submitted_data['user_password'], $user->data->user_pass, $user->ID ) ) {
		UM()->login()->auth_id = username_exists( $user_name );
	} else {
		UM()->form()->add_error( 'user_password', __( 'Contraseña incorrecta, intente nuevamente.', 'ultimate-member' ) );
	}

	// Integration with 3rd-party login handlers e.g. 3rd-party reCAPTCHA etc.
	$third_party_codes = apply_filters( 'um_custom_authenticate_error_codes', array() );

	// @since 4.18 replacement for 'wp_login_failed' action hook
	// see WP function wp_authenticate()
	$ignore_codes = array( 'empty_username', 'empty_password' );

	$user = apply_filters( 'authenticate', null, $authenticate, $submitted_data['user_password'] );
	if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
		if ( ! empty( $third_party_codes ) && in_array( $user->get_error_code(), $third_party_codes ) ) {
			UM()->form()->add_error( $user->get_error_code(), $user->get_error_message() );
		} else {
			UM()->form()->add_error( 'user_password', __( 'Contraseña incorrecta, intente nuevamente.', 'ultimate-member' ) );
		}
	}

	$user = apply_filters( 'wp_authenticate_user', $user, $submitted_data['user_password'] );
	if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
		if ( ! empty( $third_party_codes ) && in_array( $user->get_error_code(), $third_party_codes ) ) {
			UM()->form()->add_error( $user->get_error_code(), $user->get_error_message() );
		} else {
			UM()->form()->add_error( 'user_password', __( 'Contraseña incorrecta, intente nuevamente.', 'ultimate-member' ) );
		}
	}

	// if there is an error notify wp
	if ( UM()->form()->has_error( $field ) || UM()->form()->has_error( $user_password ) || UM()->form()->count_errors() > 0 ) {
		do_action( 'wp_login_failed', $user_name, UM()->form()->get_wp_error() );
	}
}
add_action( 'um_submit_form_errors_hook_login', 'custom_um_submit_form_errors_hook_login' );



remove_action( 'um_submit_form_errors_hook_', 'um_submit_form_errors_hook_', 10, 2 );
function custom_um_submit_form_errors_hook_( $submitted_data, $form_data ) {
	$form_id = $form_data['form_id'];
	$mode    = $form_data['mode'];

	$fields = maybe_unserialize( $form_data['custom_fields'] );
	if ( empty( $fields ) || ! is_array( $fields ) ) {
		return;
	}

	$um_profile_photo = um_profile( 'profile_photo' );
	if ( empty( $submitted_data['profile_photo'] ) && empty( $um_profile_photo ) && get_post_meta( $form_id, '_um_profile_photo_required', true ) ) {
		UM()->form()->add_error( 'profile_photo', __( 'Profile Photo is required.', 'ultimate-member' ) );
	}

	$can_edit           = false;
	$current_user_roles = array();
	if ( is_user_logged_in() ) {
		if ( array_key_exists( 'user_id', $submitted_data ) ) {
			$can_edit = UM()->roles()->um_current_user_can( 'edit', $submitted_data['user_id'] );
		}

		um_fetch_user( get_current_user_id() );
		$current_user_roles = um_user( 'roles' );
		um_reset_user();
	}

	foreach ( $fields as $key => $array ) {

		if ( 'profile' === $mode ) {
			$restricted_fields = UM()->fields()->get_restricted_fields_for_edit();
			if ( is_array( $restricted_fields ) && in_array( $key, $restricted_fields, true ) ) {
				continue;
			}
		}

		$can_view = true;
		if ( isset( $array['public'] ) && 'register' !== $mode ) {

			switch ( $array['public'] ) {
				case '1': // Everyone
					break;
				case '2': // Members
					if ( ! is_user_logged_in() ) {
						$can_view = false;
					}
					break;
				case '-1': // Only visible to profile owner and admins
					if ( ! is_user_logged_in() ) {
						$can_view = false;
					} elseif ( $submitted_data['user_id'] != get_current_user_id() && ! $can_edit ) {
						$can_view = false;
					}
					break;
				case '-2': // Only specific member roles
					if ( ! is_user_logged_in() ) {
						$can_view = false;
					} elseif ( ! empty( $array['roles'] ) && count( array_intersect( $current_user_roles, $array['roles'] ) ) <= 0 ) {
						$can_view = false;
					}
					break;
				case '-3': // Only visible to profile owner and specific roles
					if ( ! is_user_logged_in() ) {
						$can_view = false;
					} elseif ( $submitted_data['user_id'] != get_current_user_id() && ! empty( $array['roles'] ) && count( array_intersect( $current_user_roles, $array['roles'] ) ) <= 0 ) {
						$can_view = false;
					}
					break;
				default:
					$can_view = apply_filters( 'um_can_view_field_custom', $can_view, $array );
					break;
			}
		}

		$can_view = apply_filters( 'um_can_view_field', $can_view, $array );

		if ( ! $can_view ) {
			continue;
		}

		/**
		 * UM hook
		 *
		 * @type filter
		 * @title um_get_custom_field_array
		 * @description Extend custom field data on submit form error
		 * @input_vars
		 * [{"var":"$array","type":"array","desc":"Field data"},
		 * {"var":"$fields","type":"array","desc":"All fields"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage
		 * <?php add_filter( 'um_get_custom_field_array', 'function_name', 10, 2 ); ?>
		 * @example
		 * <?php
		 * add_filter( 'um_get_custom_field_array', 'my_get_custom_field_array', 10, 2 );
		 * function my_get_custom_field_array( $array, $fields ) {
		 *     // your code here
		 *     return $array;
		 * }
		 * ?>
		 */
		$array = apply_filters( 'um_get_custom_field_array', $array, $fields );

		if ( ! empty( $array['conditions'] ) ) {
			try {
				foreach ( $array['conditions'] as $condition ) {
					$continue = um_check_conditions_on_submit( $condition, $fields, $submitted_data, true );
					if ( $continue === true ) {
						continue 2;
					}
				}
			} catch ( Exception $e ) {
				UM()->form()->add_error( $key, sprintf( __( '%s - wrong conditions.', 'ultimate-member' ), $array['title'] ) );
				$notice = '<div class="um-field-error">' . sprintf( __( '%s - wrong conditions.', 'ultimate-member' ), $array['title'] ) . '</div><!-- ' . $e->getMessage() . ' -->';
				add_action( 'um_after_profile_fields', function() use ( $notice ) {
					echo $notice;
				}, 900 );
			}
		}

		if ( isset( $array['type'] ) && $array['type'] == 'checkbox' && isset( $array['required'] ) && $array['required'] == 1 && ! isset( $submitted_data[ $key ] ) ) {
			UM()->form()->add_error( $key, sprintf( __( '%s es obligatorio.', 'ultimate-member' ), $array['title'] ) );
		}

		if ( isset( $array['type'] ) && $array['type'] == 'radio' && isset( $array['required'] ) && $array['required'] == 1 && ! isset( $submitted_data[ $key ] ) && ! in_array( $key, array( 'role_radio', 'role_select' ) ) ) {
			UM()->form()->add_error( $key, sprintf( __( '%s es obligatorio.', 'ultimate-member'), $array['title'] ) );
		}

		if ( isset( $array['type'] ) && $array['type'] == 'multiselect' && isset( $array['required'] ) && $array['required'] == 1 && ! isset( $submitted_data[ $key ] ) && ! in_array( $key, array( 'role_radio', 'role_select' ) ) ) {
			UM()->form()->add_error( $key, sprintf( __( '%s es obligatorio.', 'ultimate-member' ), $array['title'] ) );
		}

		/* WordPress uses the default user role if the role wasn't chosen in the registration form. That is why we should use submitted data to validate fields Roles (Radio) and Roles (Dropdown). */
		if ( in_array( $key, array( 'role_radio', 'role_select' ) ) && isset( $array['required'] ) && $array['required'] == 1 && empty( UM()->form()->post_form['submitted']['role'] ) ) {
			UM()->form()->add_error( 'role', __( 'Please specify account type.', 'ultimate-member' ) );
			UM()->form()->post_form[ $key ] = '';
		}

		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_add_error_on_form_submit_validation
		 * @description Submit form validation
		 * @input_vars
		 * [{"var":"$field","type":"array","desc":"Field Data"},
		 * {"var":"$key","type":"string","desc":"Field Key"},
		 * {"var":"$args","type":"array","desc":"Form Arguments"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_add_error_on_form_submit_validation', 'function_name', 10, 3 );
		 * @example
		 * <?php
		 * add_action( 'um_add_error_on_form_submit_validation', 'my_add_error_on_form_submit_validation', 10, 3 );
		 * function my_add_error_on_form_submit_validation( $field, $key, $args ) {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action( 'um_add_error_on_form_submit_validation', $array, $key, $submitted_data );

		if ( ! empty( $array['required'] ) ) {
			if ( ! isset( $submitted_data[ $key ] ) || $submitted_data[ $key ] == '' || $submitted_data[ $key ] == 'empty_file' ) {
				if ( empty( $array['label'] ) ) {
					UM()->form()->add_error( $key, __( 'Este campo es obligatorio', 'ultimate-member' ) );
				} else {
					UM()->form()->add_error( $key, sprintf( __( '%s es obligatorio', 'ultimate-member' ), $array['label'] ) );
				}
			}
		}

		if ( ! isset( $submitted_data[ $key ] ) ) {
			continue;
		}

		if ( isset( $array['max_words'] ) && $array['max_words'] > 0 ) {
			if ( str_word_count( $submitted_data[ $key ], 0, "éèàôù" ) > $array['max_words'] ) {
				UM()->form()->add_error( $key, sprintf( __( 'You are only allowed to enter a maximum of %s words', 'ultimate-member' ), $array['max_words'] ) );
			}
		}

		if ( isset( $array['min_chars'] ) && $array['min_chars'] > 0 ) {
			if ( $submitted_data[ $key ] && mb_strlen( $submitted_data[ $key ] ) < $array['min_chars'] ) {
				if ( empty( $array['label'] ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Este campo debe contener un mínimo de %s caracteres', 'ultimate-member' ), $array['min_chars'] ) );
				} else {
					UM()->form()->add_error( $key, sprintf( __( 'Su %s debe contener al menos %s caracteres', 'ultimate-member' ), $array['label'], $array['min_chars'] ) );
				}
			}
		}

		if ( isset( $array['max_chars'] ) && $array['max_chars'] > 0 ) {
			if ( $submitted_data[ $key ] && mb_strlen( $submitted_data[ $key ] ) > $array['max_chars'] ) {
				if ( empty( $array['label'] ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'This field must contain less than %s characters', 'ultimate-member' ), $array['max_chars'] ) );
				} else {
					UM()->form()->add_error( $key, sprintf( __( 'Your %s must contain less than %s characters', 'ultimate-member' ), $array['label'], $array['max_chars'] ) );
				}
			}
		}

		if ( isset( $array['type'] ) && $array['type'] == 'textarea' && UM()->profile()->get_show_bio_key( $submitted_data ) !== $key ) {
			if ( ! isset( $array['html'] ) || $array['html'] == 0 ) {
				if ( wp_strip_all_tags( $submitted_data[ $key ] ) != trim( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'You can not use HTML tags here', 'ultimate-member' ) );
				}
			}
		}

		if ( isset( $array['force_good_pass'] ) && $array['force_good_pass'] && ! empty( $submitted_data['user_password'] ) ) {
			if ( isset( $submitted_data['user_login'] ) && strpos( strtolower( $submitted_data['user_login'] ), strtolower( $submitted_data['user_password'] )  ) > -1 ) {
				UM()->form()->add_error( 'user_password', __( 'Your password cannot contain the part of your username', 'ultimate-member' ));
			}

			if ( isset( $submitted_data['user_email'] ) && strpos( strtolower( $submitted_data['user_email'] ), strtolower( $submitted_data['user_password'] )  ) > -1 ) {
				UM()->form()->add_error( 'user_password', __( 'Your password cannot contain the part of your email address', 'ultimate-member' ));
			}

			if ( ! UM()->validation()->strong_pass( $submitted_data[ $key ] ) ) {
				UM()->form()->add_error( $key, __( 'Your password must contain at least one lowercase letter, one capital letter and one number', 'ultimate-member' ) );
			}
		}

		if ( ! empty( $array['force_confirm_pass'] ) ) {
			if ( ! array_key_exists( 'confirm_' . $key, $submitted_data ) && ! UM()->form()->has_error( $key ) ) {
				UM()->form()->add_error( 'confirm_' . $key, __( 'Por favor confirma su contraseña', 'ultimate-member' ) );
			} else {
				if ( '' === $submitted_data[ 'confirm_' . $key ] && ! UM()->form()->has_error( $key ) ) {
					UM()->form()->add_error( 'confirm_' . $key, __( 'Por favor confirma su contraseña', 'ultimate-member' ) );
				}
				if ( $submitted_data[ 'confirm_' . $key ] !== $submitted_data[ $key ] && ! UM()->form()->has_error( $key ) ) {
					UM()->form()->add_error( 'confirm_' . $key, __( 'Your passwords do not match', 'ultimate-member' ) );
				}
			}
		}

		if ( isset( $array['min_selections'] ) && $array['min_selections'] > 0 ) {
			if ( ( ! isset( $submitted_data[ $key ] ) ) || ( isset( $submitted_data[ $key ] ) && is_array( $submitted_data[ $key ] ) && count( $submitted_data[ $key ] ) < $array['min_selections'] ) ) {
				UM()->form()->add_error( $key, sprintf( __( 'Please select at least %s choices', 'ultimate-member' ), $array['min_selections'] ) );
			}
		}

		if ( isset( $array['max_selections'] ) && $array['max_selections'] > 0 ) {
			if ( isset( $submitted_data[ $key ] ) && is_array( $submitted_data[ $key ] ) && count( $submitted_data[ $key ] ) > $array['max_selections'] ) {
				UM()->form()->add_error( $key, sprintf( __( 'You can only select up to %s choices', 'ultimate-member' ), $array['max_selections'] ) );
			}
		}

		if ( isset( $array['min'] ) && is_numeric( $submitted_data[ $key ] ) ) {
			if ( isset( $submitted_data[ $key ] )  && $submitted_data[ $key ] < $array['min'] ) {
				UM()->form()->add_error( $key, sprintf( __( 'Minimum number limit is %s', 'ultimate-member' ), $array['min'] ) );
			}
		}

		if ( isset( $array['max'] ) && is_numeric( $submitted_data[ $key ] )  ) {
			if ( isset( $submitted_data[ $key ] ) && $submitted_data[ $key ] > $array['max'] ) {
				UM()->form()->add_error( $key, sprintf( __( 'Maximum number limit is %s', 'ultimate-member' ), $array['max'] ) );
			}
		}

		if ( empty( $array['validate'] ) ) {
			continue;
		}

		switch ( $array['validate'] ) {

			case 'custom':
				$custom = $array['custom_validate'];
				/**
				 * UM hook
				 *
				 * @type action
				 * @title um_custom_field_validation_{$custom}
				 * @description Submit form validation for custom field
				 * @input_vars
				 * [{"var":"$key","type":"string","desc":"Field Key"},
				 * {"var":"$field","type":"array","desc":"Field Data"},
				 * {"var":"$args","type":"array","desc":"Form Arguments"}]
				 * @change_log
				 * ["Since: 2.0"]
				 * @usage add_action( 'um_custom_field_validation_{$custom}', 'function_name', 10, 3 );
				 * @example
				 * <?php
				 * add_action( 'um_custom_field_validation_{$custom}', 'my_custom_field_validation', 10, 3 );
				 * function my_custom_field_validation( $key, $field, $args ) {
				 *     // your code here
				 * }
				 * ?>
				 */
				do_action( "um_custom_field_validation_{$custom}", $key, $array, $submitted_data );
				break;

			case 'numeric':
				if ( $submitted_data[ $key ] && ! is_numeric( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Please enter numbers only in this field', 'ultimate-member' ) );
				}
				break;

			case 'phone_number':
				if ( ! UM()->validation()->is_phone_number( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Please enter a valid phone number', 'ultimate-member' ) );
				}
				break;

			case 'youtube_url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'youtube.com' ) && ! UM()->validation()->is_url( $submitted_data[ $key ], 'youtu.be' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s username or profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'spotify_url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'open.spotify.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'telegram_url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 't.me' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s username or profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'soundcloud_url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'soundcloud.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s username or profile URL','ultimate-member'), $array['label'] ) );
				}
				break;

			case 'facebook_url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'facebook.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s username or profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'twitter_url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'twitter.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s username or profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'instagram_url':

				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'instagram.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'linkedin_url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'linkedin.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s username or profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'discord':
				if ( ! UM()->validation()->is_discord_id( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Please enter a valid Discord ID', 'ultimate-member' ) );
				}
				break;

			case 'tiktok_url':

				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'tiktok.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'twitch_url':

				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'twitch.tv' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'reddit_url':

				if ( ! UM()->validation()->is_url( $submitted_data[ $key ], 'reddit.com' ) ) {
					UM()->form()->add_error( $key, sprintf( __( 'Please enter a valid %s profile URL', 'ultimate-member' ), $array['label'] ) );
				}
				break;

			case 'url':
				if ( ! UM()->validation()->is_url( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Please enter a valid URL', 'ultimate-member' ) );
				}
				break;

			case 'unique_username':

				if ( $submitted_data[ $key ] == '' ) {
					UM()->form()->add_error( $key, __( 'You must provide a username', 'ultimate-member' ) );
				} elseif ( $mode == 'register' && username_exists( sanitize_user( $submitted_data[ $key ] ) ) ) {
					UM()->form()->add_error( $key, __( 'The username you entered is incorrect', 'ultimate-member' ) );
				} elseif ( is_email( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Username cannot be an email', 'ultimate-member' ) );
				} elseif ( ! UM()->validation()->safe_username( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Your username contains invalid characters', 'ultimate-member' ) );
				}

				break;

			case 'unique_username_or_email':

				if ( $submitted_data[ $key ] == '' ) {
					UM()->form()->add_error( $key, __( 'You must provide a username or email', 'ultimate-member' ) );
				} elseif ( $mode == 'register' && username_exists( sanitize_user( $submitted_data[ $key ] ) ) ) {
					UM()->form()->add_error( $key, __( 'El nombre de usuario es incorrecto', 'ultimate-member' ) );
				} elseif ( $mode == 'register' && email_exists( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Este no es un correo electrónico correcto', 'ultimate-member' ) );
				} elseif ( ! UM()->validation()->safe_username( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'Your username contains invalid characters', 'ultimate-member' ) );
				}

				break;

			case 'unique_email':

				$submitted_data[ $key ] = trim( $submitted_data[ $key ] );

				if ( in_array( $key, array( 'user_email' ) ) ) {

					if ( ! isset( $submitted_data['user_id'] ) ){
						$submitted_data['user_id'] = um_get_requested_user();
					}

					$email_exists = email_exists( $submitted_data[ $key ] );

					if ( $submitted_data[ $key ] == '' && in_array( $key, array( 'user_email' ) ) ) {
						UM()->form()->add_error( $key, __( 'You must provide your email', 'ultimate-member' ) );
					} elseif ( in_array( $mode, array( 'register' ) ) && $email_exists  ) {
						UM()->form()->add_error( $key, __( 'Este no es un correo electrónico correcto', 'ultimate-member' ) );
					} elseif ( in_array( $mode, array( 'profile' ) ) && $email_exists && $email_exists != $submitted_data['user_id']  ) {
						UM()->form()->add_error( $key, __( 'Este no es un correo electrónico correcto', 'ultimate-member' ) );
					} elseif ( ! is_email( $submitted_data[ $key ] ) ) {
						UM()->form()->add_error( $key, __( 'Este no es un correo electrónico correcto', 'ultimate-member') );
					} elseif ( ! UM()->validation()->safe_username( $submitted_data[ $key ] ) ) {
						UM()->form()->add_error( $key,  __( 'Your email contains invalid characters', 'ultimate-member' ) );
					}

				} else {

					if ( $submitted_data[ $key ] != '' && ! is_email( $submitted_data[ $key ] ) ) {
						UM()->form()->add_error( $key, __( 'Este no es un correo electrónico correcto', 'ultimate-member' ) );
					} elseif ( $submitted_data[ $key ] != '' && email_exists( $submitted_data[ $key ] ) ) {
						UM()->form()->add_error( $key, __( 'Este no es un correo electrónico correcto', 'ultimate-member' ) );
					} elseif ( $submitted_data[ $key ] != '' ) {

						$users = get_users( 'meta_value=' . $submitted_data[ $key ] );

						foreach ( $users as $user ) {
							if ( $user->ID != $submitted_data['user_id'] ) {
								UM()->form()->add_error( $key, __( 'Este no es un correo electrónico correcto', 'ultimate-member' ) );
							}
						}

					}

				}

				break;

			case 'is_email':

				$submitted_data[ $key ] = trim( $submitted_data[ $key ] );

				if ( $submitted_data[ $key ] != '' && ! is_email( $submitted_data[ $key ] ) ) {
					UM()->form()->add_error( $key, __( 'This is not a valid email', 'ultimate-member' ) );
				}

				break;

			case 'unique_value':

				if ( $submitted_data[ $key ] != '' ) {

					$args_unique_meta = array(
						'meta_key'      => $key,
						'meta_value'    => $submitted_data[ $key ],
						'compare'       => '=',
						'exclude'       => array( $submitted_data['user_id'] ),
					);

					$meta_key_exists = get_users( $args_unique_meta );

					if ( $meta_key_exists ) {
						UM()->form()->add_error( $key , __( 'You must provide a unique value', 'ultimate-member' ) );
					}
				}
				break;

			case 'alphabetic':

				if ( $submitted_data[ $key ] != '' ) {

					if ( ! preg_match( '/^\p{L}+$/u', str_replace( ' ', '', $submitted_data[ $key ] ) ) ) {
						UM()->form()->add_error( $key, __( 'You must provide alphabetic letters', 'ultimate-member' ) );
					}

				}

				break;

			case 'lowercase':

				if ( $submitted_data[ $key ] != '' ) {

					if ( ! ctype_lower( str_replace(' ', '', $submitted_data[ $key ] ) ) ) {
						UM()->form()->add_error( $key , __( 'You must provide lowercase letters.', 'ultimate-member' ) );
					}
				}

				break;

		}

		if ( isset( $submitted_data['description'] ) ) {
			$max_chars = UM()->options()->get( 'profile_bio_maxchars' );
			$profile_show_bio = UM()->options()->get( 'profile_show_bio' );

			if ( $profile_show_bio ) {
				if ( mb_strlen( str_replace( array( "\r\n", "\n", "\r\t", "\t" ), ' ', $submitted_data['description'] ) ) > $max_chars && $max_chars ) {
					UM()->form()->add_error( 'description', sprintf( __( 'Your user description must contain less than %s characters', 'ultimate-member' ), $max_chars ) );
				}
			}
		}
	} // end if ( isset in args array )
}
add_action( 'um_submit_form_errors_hook_', 'custom_um_submit_form_errors_hook_', 10, 2 );

function custom_msg_reset_password( ) {
	?>
	<div class="um-field um-field-block um-field-type_block">
		<div class="um-field-block">
			<div style="text-align:center;">
				<?php esc_html_e( 'Para reiniciar su contraseña debe introducir su usuario o correo electrónico.', 'ultimate-member' ); ?>
			</div>
		</div>
	</div>
	
	<?php
}
add_action( 'custom_msg_reset_password', 'custom_msg_reset_password', 1001 );

function custom_msg_error_reset_password( ) {
	esc_html_e( 'Usted recibirá un correo electrónico con los detalles para reestablecer su contraseña.', 'ultimate-member' );
}
add_action( 'custom_msg_error_reset_password', 'custom_msg_error_reset_password', 1001 );

function custom_button_reset_password( ) {
	?>
	<input type="submit" value="<?php esc_attr_e( 'Reiniciar contraseña', 'ultimate-member' ); ?>" class="um-button" id="um-submit-btn" />
	<?php
}
add_action( 'custom_button_reset_password', 'custom_button_reset_password', 1001 );

add_filter( 'woocommerce_currency_symbol', 'change_currency_symbol', 10, 2 );

function change_currency_symbol( $symbols, $currency ) {
	if ( 'USD' === $currency ) {
		return 'USD';
	}

	if ( 'EUR' === $currency ) {
		return 'Euro';
	}

	if ( 'AED' === $currency ) {
		return 'AED';
	}
    return $symbols;
}


add_shortcode( 'product_reviews', 'silva_product_reviews_shortcode' );
 
function silva_product_reviews_shortcode() {
   
	$product = wc_get_product();
    $rating = $product->get_average_rating();
    
    $html .= '<div class="woocommerce-tabs">';

    $html .= '<il class="review">';
    if ( $rating ) $html .= wc_get_rating_html( $rating );
    $html .= '</il>';
    
   	$html .= '</div>';
    
   	return $html;
}