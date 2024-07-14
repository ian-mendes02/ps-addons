<?php

/**
 * Member-exclusive discount object.
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 *
 * @since      1.0.0
 */

class PS_Discount {
    public $name;
    public $type;
    public $author;
    public $id;
    public $expires_on;
    public $value;
    public $created_on;
    public $last_modified;
    public $last_edited_by;
    public $is_active;
    public $included_products;
    public $priority;

    public function __construct( $data = [] ) {
        $this->id                = $data['id'] ?? null;
        $this->name              = $data['name'] ?? '';
        $this->type              = $data['type'] ?? 'fixed';
        $this->author            = $data['author'] ?? '';
        $this->created_on        = $data['created_on'] ?? '';
        $this->last_modified     = $data['last_modified'] ?? '';
        $this->last_edited_by    = $data['last_edited_by'] ?? '';
        $this->expires_on        = $data['expires_on'] ?? '';
        $this->value             = isset( $data['value'] ) ? (float) $data['value'] : 0;
        $this->is_active         = $data['is_active'] ?? 1;
        $this->included_products = json_decode( $data['included_products'] ?? '[]', true );
        $this->priority          = isset( $data['priority'] ) ? (int) $data['priority'] : 0;
    }
}
