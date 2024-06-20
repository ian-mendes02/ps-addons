<?php

/**
 * Member-exclusive discount object.
 * @since      1.0.0
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Discount {

    public $id;
    public $name;
    public $type;
    public $value;
    public $included_products;

    /**
     * Initialize discount object with correct attribute types.
     * @since 1.0.0
     * @param array $data
     */
    public function __construct($data) {
        $this->id                = (int) $data['id'];
        $this->name              = (string) $data['name'];
        $this->type              = (string) $data['type'];
        $this->value             = (float) $data['value'];
        $this->included_products = json_decode(wp_unslash($data['included_products']), true);
    }
}
