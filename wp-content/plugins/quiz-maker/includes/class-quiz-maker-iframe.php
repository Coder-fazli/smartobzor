<?php
/**
 * Create a functionality for using quiz in iframes and in amp pages.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 */

/**
 * Create a functionality for using quiz in iframes and in amp pages.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_iFrame {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
     * The public class object of the plugin.
     *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $public_obj The public class object.
	 */
	private $public_obj;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

        $this->public_obj = new Quiz_Maker_Public( $this->plugin_name, $this->version );
	}

	/**
	 * @return bool
	 */
	public static function isEmbed() {
		if ( $_SERVER["HTTP_SEC_FETCH_SITE"] === "same-origin" && $_SERVER["HTTP_SEC_FETCH_DEST"] === "iframe" ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isAMP() {
        if( Quiz_Maker_Data::isServerSideRenderRequest() ) {
            return false;
        }

		if ( isset( $_REQUEST['ays-amp'] ) && absint( $_REQUEST['ays-amp'] ) === 1 ) {
			return true;
		}elseif ( function_exists( 'ampforwp_is_amp_endpoint' ) ) {
            return ampforwp_is_amp_endpoint();
		} elseif ( function_exists( 'amp_is_request' ) ) {
			return amp_is_request();
		} elseif ( function_exists( 'is_amp_endpoint' ) ) {
			return is_amp_endpoint();
		}

		return false;
	}

	public function enqueue_styles() {
		ob_start();
        $public_url = AYS_QUIZ_PUBLIC_URL . '/';
		?>
        <link rel="stylesheet"
              href="<?php echo $public_url . 'css/quiz-maker-public.css?ver=' . $this->version ?>">
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
              integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
              crossorigin="anonymous"
              referrerpolicy="no-referrer"/>
        <link rel="stylesheet"
              href="<?php echo $public_url . 'css/quiz-maker-sweetalert2.min.css?ver=' . $this->version ?>">
        <link rel="stylesheet"
              href="<?php echo $public_url . 'css/animate.css?ver=' . $this->version ?>">
        <link rel="stylesheet"
              href="<?php echo $public_url . 'css/animations.css?ver=' . $this->version ?>">
        <link rel="stylesheet"
              href="<?php echo $public_url . 'css/rating.min.css?ver=' . $this->version ?>">
        <link rel="stylesheet"
              href="<?php echo $public_url . 'css/quiz-maker-select2.min.css?ver=' . $this->version ?>">
        <link rel="stylesheet"
              href="<?php echo $public_url . 'css/loaders.css?ver=' . $this->version ?>">
      	<link rel="stylesheet"
              href="<?php echo $public_url . 'css/stripe-client.css?ver=' . $this->version ?>">
		<?php
		return ob_get_clean();
	}

	public function enqueue_scripts( $id ) {
		ob_start();
		$public_url = AYS_QUIZ_PUBLIC_URL . '/';
		$this->public_obj->enqueue_scripts();

		?>
        <script src="<?php echo includes_url('js/jquery/jquery.min.js') . '?ver=3.6.1'; ?>" id="jquery-core-js"></script>
        <script src="<?php echo includes_url('js/jquery/ui/effect.min.js') . '?ver=1.13.2'; ?>"
                id="jquery-effects-core-js"></script>
        <script src="<?php echo $public_url . 'js/quiz-maker-select2.min.js?ver=' . $this->version ?>"
                id="quiz-maker-select2js-js"></script>
        <script src="<?php echo $public_url . 'js/quiz-maker-sweetalert2.all.min.js?ver=' . $this->version ?>"
                id="quiz-maker-sweetalert-js-js"></script>
        <script src="<?php echo $public_url . 'js/rating.min.js?ver=' . $this->version ?>"
                id="quiz-maker-rate-quiz-js"></script>
        <script src="<?php echo $public_url . 'js/quiz-maker-functions.js?ver=' . $this->version ?>"
                id="quiz-maker-functions.js-js"></script>
        <script id="quiz-maker-ajax-public-js-extra">
            var quiz_maker_ajax_public = <?php echo json_encode( array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'warningIcon' => $public_url . "images/warning.svg",
				'AYS_QUIZ_PUBLIC_URL'   => AYS_QUIZ_PUBLIC_URL,
			) ); ?>;
        </script>
        <script id="quiz-maker-js-extra">
            var quizLangObj = <?php echo json_encode( array(
				'notAnsweredText'        => __( 'You have not answered this question', $this->plugin_name ),
				'areYouSure'             => __( 'Do you want to finish the quiz? Are you sure?', $this->plugin_name ),
				'selectPlaceholder'      => __( 'Select an answer', $this->plugin_name ),
				'correctAnswerVariants'  => __( 'Variants of the correct answer', $this->plugin_name ),
				'shareDialog'            => __( 'Share Dialog', $this->plugin_name ),
				'expiredMessage'         => __( 'The quiz has expired!', $this->plugin_name ),
				'day'                    => __( 'day', $this->plugin_name ),
				'days'                   => __( 'days', $this->plugin_name ),
				'hour'                   => __( 'hour', $this->plugin_name ),
				'hours'                  => __( 'hours', $this->plugin_name ),
				'minute'                 => __( 'minute', $this->plugin_name ),
				'minutes'                => __( 'minutes', $this->plugin_name ),
				'second'                 => __( 'second', $this->plugin_name ),
				'seconds'                => __( 'seconds', $this->plugin_name ),
				'startButtonText'        => $this->public_obj->buttons_texts['startButton'],
				'defaultStartButtonText' => __( 'Start', $this->plugin_name ),
				'loadResource'           => __( "Can't load resource.", $this->plugin_name ),
				'somethingWentWrong'     => __( "Maybe something went wrong.", $this->plugin_name ),
				'passwordIsWrong'        => __( 'Password is wrong!', $this->plugin_name ),
				'requiredError'          => __( 'This is a required question', $this->plugin_name ),
				'show'                   => __( 'Show', $this->plugin_name ),
				'hide'                   => __( 'Hide', $this->plugin_name ),

				'AYS_QUIZ_PUBLIC_URL'   => AYS_QUIZ_PUBLIC_URL,
			) ); ?>;
        </script>
        <script src="<?php echo $public_url . 'js/quiz-maker-public-ajax.js?ver=' . time() ?>"
                id="quiz-maker-ajax-public-js"></script>
        <script src="<?php echo $public_url . 'js/quiz-maker-public.js?ver=' . time() ?>"
                id="quiz-maker-js"></script>
		<?php
		echo $this->ays_quiz_payment_scripts( $id, $public_url, $this->plugin_name ,$this->version );

		return ob_get_clean();
	}

	public static function ays_quiz_translate_content($content) {
        $in = str_replace("\n", "-ays-quiz-break-line-", $content);
        $out = preg_replace_callback("/\[:(.*?)\[:]/", function($part){
            $language_slug = explode('-', get_bloginfo("language"))[0];
            preg_match("/\[\:".$language_slug."\](.*?)\[\:/is", $part[0], $out);
            return (is_array($out) && isset($out[1])) ? $out[1] : $part[0];
        }, $in);
        $out = str_replace("-ays-quiz-break-line-", "\n", $out);
        return $out;
    }

    public function ays_quiz_payment_scripts($id, $public_url, $plugin_name, $plugin_version) {
    	$quiz = Quiz_Maker_Data::get_quiz_by_id($id);
        
        if (is_null($quiz)) {
            $content = "";
            return $content;
        }
        if (intval($quiz['published']) === 0) {
            $content = "";
            return $content;
        }

        $content = array();
        
        $is_elementor_exists = Quiz_Maker_Data::ays_quiz_is_elementor();
        $is_editor_exists = Quiz_Maker_Data::ays_quiz_is_editor();

        
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
        $enable_copy_protection = (isset($options['enable_copy_protection']) && $options['enable_copy_protection'] == "on") ? true : false;
        $quiz_integrations = (get_option( 'ays_quiz_integrations' ) == null || get_option( 'ays_quiz_integrations' ) == '') ? array() : json_decode( get_option( 'ays_quiz_integrations' ), true );
        $payment_terms = isset($quiz_integrations['payment_terms']) ? $quiz_integrations['payment_terms'] : "lifetime";
        $paypal_client_id = isset($quiz_integrations['paypal_client_id']) && $quiz_integrations['paypal_client_id'] != '' ? $quiz_integrations['paypal_client_id'] : null;
        $quiz_paypal = (isset($options['enable_paypal']) && $options['enable_paypal'] == "on") ? true : false;
        $quiz_paypal_message = (isset($options['paypal_message']) && $options['paypal_message'] != "") ? $options['paypal_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
        $quiz_paypal_message = stripslashes( wpautop( $quiz_paypal_message ) );
        $paypal_subscribtion_duration = isset( $quiz_integrations['subscribtion_duration'] ) && $quiz_integrations['subscribtion_duration'] != '' ? absint( $quiz_integrations['subscribtion_duration'] ) : '';
        $paypal_subscribtion_duration_by = isset( $quiz_integrations['subscribtion_duration_by'] ) && $quiz_integrations['subscribtion_duration_by'] != '' ? $quiz_integrations['subscribtion_duration_by'] : 'day';

        // Stripe
        $stripe_res = (Quiz_Maker_Settings_Actions::ays_get_setting('stripe') === false) ? json_encode(array()) : Quiz_Maker_Settings_Actions::ays_get_setting('stripe');
        $stripe = json_decode($stripe_res, true);
        $stripe_secret_key = isset($stripe['secret_key']) ? $stripe['secret_key'] : '';
        $stripe_api_key = isset($stripe['api_key']) ? $stripe['api_key'] : '';
        $stripe_payment_terms = isset($stripe['payment_terms']) ? $stripe['payment_terms'] : 'lifetime';

        // Stripe parameters
        $options['enable_stripe'] = !isset( $options['enable_stripe'] ) ? 'off' : $options['enable_stripe'];
        $enable_stripe = ( isset($options['enable_stripe']) && $options['enable_stripe'] == 'on' ) ? true : false;
        $stripe_amount = (isset($options['stripe_amount'])) ? $options['stripe_amount'] : '';
        $stripe_currency = (isset($options['stripe_currency'])) ? $options['stripe_currency'] : '';
        $stripe_message = (isset($options['stripe_message'])) ? $options['stripe_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
        $stripe_message = stripslashes( wpautop( $stripe_message ) );

        // Paypal And Stripe Paymant type
        $payment_type = (isset($options['payment_type']) && sanitize_text_field( $options['payment_type'] ) != '') ? sanitize_text_field( esc_attr( $options['payment_type']) ) : 'prepay';

        $paypal_connection = Quiz_Maker_Data::get_payment_connection( 'paypal', $payment_type, $payment_terms, $id, array(
            'subsctiptionDuration' => $paypal_subscribtion_duration,
            'subsctiptionDurationBy' => $paypal_subscribtion_duration_by,
        ));

        if($quiz_paypal && $paypal_connection === true){
            if($paypal_client_id == null || $paypal_client_id == ''){
            }else{
                $link = "https://www.paypal.com/sdk/js?client-id=".$quiz_integrations['paypal_client_id']."&currency=".$options['paypal_currency'];
                $content[] = "<script src='". $link ."' id='quiz-maker-paypal-js' data-namespace='aysQuizPayPal'></script>";
            }
        }

        $stripe_connection = Quiz_Maker_Data::get_payment_connection( 'stripe', $payment_type, $stripe_payment_terms, $id, array());

        if($enable_stripe && $stripe_connection === true){
            if($stripe_secret_key == '' || $stripe_api_key == ''){
            }else{
                $enqueue_stripe_scripts = true;
                if( !is_user_logged_in() && $stripe_payment_terms == "lifetime" ){
                    $enqueue_stripe_scripts = false;
                }

                if( $is_elementor_exists ){
                    $enqueue_stripe_scripts = false;
                }

                if( $enqueue_stripe_scripts ){

                    $link = "https://js.stripe.com/v3/";
                	$content[] = "<script src='". $link ."' id='quiz-maker-stripe-js'></script>";

                    $link = $public_url . "js/stripe_client.js?ver=". $this->version;
                	$content[] = "<script src='". $link ."' id='quiz-maker-stripe-client-js'></script>";
                }
            }
        }

        if($enable_copy_protection){
            if ( ! $is_elementor_exists && ! $is_editor_exists ) {
            	$link = $public_url . "js/quiz_copy_protection.min.js?ver=". $this->version;
            	$content[] = "<script src='". $link ."' id='quiz-maker-quiz_copy_protection-js'></script>";
            }
        }

        $content = implode("", $content);
        return $content;
    }

	public function iframe_shortcode() {
        self::headers();
		$id = ( isset( $_REQUEST['quiz'] ) && $_REQUEST['quiz'] != '' ) ? absint( intval( $_REQUEST['quiz'] ) ) : null;
        $attr = array(
            'id' => $id
        );

		$attr['chain'] = (isset($attr['chain'])) ? absint(intval($attr['chain'])) : null;
		$attr['report'] = (isset($attr['report'])) ? $attr['report'] : null;

		if ( is_null( $id ) ) {
			$quiz_content = "<p class='wrong_shortcode_text' style='color:red;'>" . __( 'Wrong shortcode initialized', $this->plugin_name ) . "</p>";
			echo str_replace( array( "\r\n", "\n", "\r" ), "\n", $quiz_content );
			wp_die();
		}

		include_once( AYS_QUIZ_PUBLIC_PATH . '/partials/quiz-maker-iframe-template.php' );

		wp_die();
	}

	/**
	 * @return void
	 */
    public static function headers(){
	    if ( $_SERVER["HTTP_SEC_FETCH_SITE"] !== "same-origin" && $_SERVER["HTTP_SEC_FETCH_DEST"] !== "iframe" ) {
		    header( 'HTTP/1.0 403 Forbidden', true, 403 );
		    http_response_code( 403 );
		    die();
	    }

	    header( 'Access-Control-Allow-Origin: *' );
	    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS' );
	    header( 'Access-Control-Max-Age: 1000' );
	    header( 'Access-Control-Allow-Headers: Content-Type' );
	    header( 'Content-Type: text/html; charset=utf-8' );
	    header( "Content-Security-Policy: frame-ancestors * " );
	    header( 'X-Frame-Options: ALLOW-FROM *' );
    }

	/**
	 * @return void
	 */
    public static function headers_for_ajax(){
	    header('Access-Control-Allow-Origin: *');
	    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	    header('Access-Control-Max-Age: 1000');
	    header('Access-Control-Allow-Headers: Content-Type');
	    header( "Content-Security-Policy: frame-ancestors * " );
	    header( 'X-Frame-Options: ALLOW-FROM *' );
    }

    public static function get_iframe_for_amp( $id, $attr ) {
	    $url_query = http_build_query( array_merge( array(
		    'action' => 'ays_quiz_iframe_shortcode',
		    'quiz' => $id,
		    'ays-amp' => '1',
	    ), $attr ) );

        $same_origin = ' allow-same-origin ';
        if( function_exists( 'ampforwp_is_amp_endpoint' ) ){
	        $same_origin = '';
        }

        $url = admin_url( 'admin-ajax.php' );
	    if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === "on" ) {
		    //$url = str_replace( 'https', 'http', $url );
	    }

        $url .= '?' . $url_query;
        
	    $options = array();
	    if ( !is_null($id) && intval($id) > 0 ) {
	        $quiz = Quiz_Maker_Data::get_quiz_by_id($id);
	        $options = ( isset( $quiz['options'] ) && json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
	    }

	    // Quiz min-height
        $quiz_height = (isset($options['height']) && $options['height'] != '' && intval( $options['height'] ) > 0) ? absint( sanitize_text_field($options['height']) ) : 400;

        if ($quiz_height != "" && $quiz_height > 0) {
        	$quiz_height += 100;
        }

	    $content = '
            <iframe
              width="400"
              height="'. $quiz_height .'"
              frameborder="0"
              scrolling="yes"
              layout="responsive"
              sandbox="allow-downloads '. $same_origin .' allow-forms allow-modals allow-orientation-lock allow-pointer-lock allow-popups allow-popups-to-escape-sandbox allow-presentation allow-scripts allow-top-navigation-by-user-activation"
              resizable
              id="aysQuizAMPIframe' . $id . '"
              src="' . $url . '"
              style="width:100%; max-width: 100%; margin: 0 auto; overflow: hidden;">
              <div overflow tabindex="0" role="button" aria-label="Quiz content"></div>
            </iframe>';

        return $content;
    }

    public static function get_iframe( $id, $attr ) {
	    $url_query = http_build_query( array_merge( array(
		    'action' => 'ays_quiz_iframe_shortcode',
		    'quiz' => $id,
        ), $attr ) );

	    $url = admin_url( 'admin-ajax.php' );
	    if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === "on" ) {
		    //$url = str_replace( 'https', 'http', $url );
	    }

	    $url .= '?' . $url_query;

	    $options = array();
	    if ( !is_null($id) && intval($id) > 0 ) {
	        $quiz = Quiz_Maker_Data::get_quiz_by_id($id);
	        $options = ( isset( $quiz['options'] ) && json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
	    }

	    // Quiz min-height
        $quiz_height = (isset($options['height']) && $options['height'] != '' && intval( $options['height'] ) > 0) ? absint( sanitize_text_field($options['height']) ) : 400;

        if ($quiz_height != "" && $quiz_height > 0) {
        	$quiz_height += 100;
        }

	    $content = '
            <iframe
              width="400"
              height="'. $quiz_height .'"
              frameborder="0"
              scrolling="yes"
              layout="responsive"
              sandbox="allow-downloads allow-same-origin allow-forms allow-modals allow-orientation-lock allow-pointer-lock allow-popups allow-popups-to-escape-sandbox allow-presentation allow-scripts allow-top-navigation-by-user-activation"
              resizable
              id="aysQuizIframe'. $id .'"
              src="' . $url . '"
              style="width:100%; max-width: 100%; margin: 0 auto; overflow: hidden;">
              <div overflow tabindex="0" role="button" aria-label="Quiz content"></div>
            </iframe>';

        $content .= '
        <script>

			window.addEventListener("message", receiveMessage, false);

			function receiveMessage(event) {
			  if (event.data === "getParentUrl") {
			    event.source.postMessage(window.location.href, event.origin);
			  }
			}

		</script>';

        return $content;
    }
}