<?php

/**
 * Extends the `WP_User` class with more specific customer-oriented functionality.
 * @since      1.0.0
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */

class PS_Customer extends WP_User {

    /**
     * Does current user have a member role
     */
    public $is_member;

    /**
     * Refers to an instance of the class itself.
     * @var PS_Customer
     */
    protected static $instance = null;

    /** 
     * Returns a new `PS_Customer` object.
     * @since 1.0.0
     */
    public static function instance() {
        if (null === self::$instance) {
            return new self();
        }
    }

    /** 
     * Fetch TheMembers API token. 
     * @since 1.0.0
     */
    private static function get_tm_auth_token() {
        $TM_AUTH_URL = "https://api.themembers.dev.br/api/generate-token";
        $TM_USER_ID = "1d021680-a24d-4dde-8c65-687a44a37c29";
        $TM_APP_TOKEN = "6e35d749-4f4c-46fa-b7e8-5a1e19bfb1fc";

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => ["Content-type: application/json"],
            CURLOPT_POSTFIELDS     => json_encode(["user_id" => $TM_USER_ID]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $TM_AUTH_URL
        ]);

        $data = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($data);

        return $res->token;
    }

    public function __construct($user_id = null) {
        if (!isset($user_id)) {
            $user_id = get_current_user_id();
        }
        parent::__construct($user_id);
        $this->is_member = in_array("assinante-palmilhando", $this->roles);
        add_action('user_register', [$this, 'verify_membership']);
    }

    /** 
     * Promote current user to member status. 
     * @since 1.0.0
     */
    public function promote() {
        $this->set_role("assinante-palmilhando");
    }

    /** 
     * Verify current user's membership status.
     * If membership status is valid, promotes current user and returns true. 
     * @since 1.0.0
     */
    public function verify_membership() {
        if (in_array($this->roles, "customer")) {

            $TM_MAIL_URL = "https://registration.themembers.dev.br/api/users/show-email/";
            $TM_PLATFORM_TOKEN = "b3ce71cb-c406-4e43-971b-62ab1490f73a";
            $auth_token = self::get_tm_auth_token();

            $url = $TM_MAIL_URL . $this->user_email . '/' . $auth_token . '/' . $TM_PLATFORM_TOKEN;
            $response_data = json_decode(file_get_contents($url, false));
            $valid_member = isset($response_data->user, $response_data->subscription);

            if ($valid_member) {
                $this->promote();
            }

            return $valid_member;
        }
    }
}
