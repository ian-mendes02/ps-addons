<?php

/**
 * Extends the `WP_User` class with more specific customer-oriented functionality.
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 *
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 *
 * @since      1.0.0
 */

class PS_Customer extends WP_User {
    /**
     * An intance of the class.
     * @var PS_Customer
     * @since 1.0.0
     */
    private static $instance;

    /**
     * Enables referencing a single instance of this class in a script.
     * @since 1.0.0
     *
     * @return PS_Customer
     */
    public static function instance( $id = null ) {
        if ( null === self::$instance ) {
            self::$instance = new self( $id );
        }
        return self::$instance;
    }

    /**
     * Fetch TheMembers API token.
     * @since 1.0.0
     */
    private static function get_tm_auth_token() {
        $TM_AUTH_URL  = "https://api.themembers.dev.br/api/generate-token";
        $TM_USER_ID   = "1d021680-a24d-4dde-8c65-687a44a37c29";
        $TM_APP_TOKEN = "6e35d749-4f4c-46fa-b7e8-5a1e19bfb1fc";

        $ch = curl_init();

        curl_setopt_array( $ch, [
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => ["Content-type: application/json"],
            CURLOPT_POSTFIELDS     => json_encode( ["user_id" => $TM_USER_ID] ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $TM_AUTH_URL,
        ] );

        $data = curl_exec( $ch );
        curl_close( $ch );
        $res = json_decode( $data );

        return $res->token;
    }

    /**
     * Creates a new `PS_Customer` object from `$user_id`.
     * If `$user_id` is not provided, the current logged in user will be used as reference.
     * @since 1.0.0
     *
     * @param string|null $user_id
     */
    public function __construct( $user_id = null ) {
        if ( ! isset( $user_id ) ) {
            $user_id = get_current_user_id();
        }
        parent::__construct( $user_id );
    }

    /**
     * Promote current user to member status.
     * @since 1.0.0
     */
    public function promote() {
        $this->set_role( get_option( 'ps_member_role' ) );
    }

    /**
     * Checks if current logged in user has a member role.
     * @since 1.0.0
     */
    public function is_member() {
        $member_role = get_option( 'ps_member_role' );
        $is_member   = is_array( $this->roles )
            ? in_array( $member_role, $this->roles )
            : $member_role === $this->roles;
    }

    /**
     * Verify current user's membership status.
     * If membership status is valid, promotes current user and returns true.
     * @since 1.0.0
     */
    public function verify_membership() {
        $default_role = get_option( 'default_role', 'customer' );
        $is_default   = is_array( $this->roles )
            ? in_array( $default_role, $this->roles )
            : $default_role === $this->roles;
        if ( $is_default ) {
            $TM_MAIL_URL       = "https://registration.themembers.dev.br/api/users/show-email/";
            $TM_PLATFORM_TOKEN = "b3ce71cb-c406-4e43-971b-62ab1490f73a";
            $auth_token        = self::get_tm_auth_token();
            $url               = $TM_MAIL_URL . $this->user_email . '/' . $auth_token . '/' . $TM_PLATFORM_TOKEN;
            $response_data     = json_decode( file_get_contents( $url, false ) );
            $valid_member      = isset( $response_data->user, $response_data->subscription );
            if ( $valid_member ) $this->promote();
            return $valid_member;
        }
    }
}
