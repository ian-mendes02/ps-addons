<?php defined( 'ABSPATH' ) || exit;?>

<div class="wrap">
<h1>Configurações</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'ps-settings-group' );?>
    <?php do_settings_sections( 'ps-settings-group' );?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Nome da função de assinante</th>
            <td>
                <select name="ps_member_role">
                    <?php wp_dropdown_roles( get_option( 'ps_member_role' ) );?>
                </select>
            </td>
        </tr>
    </table>
    <?php submit_button( 'Salvar' );?>
</form>
<?php var_dump( PS()->discount_manager->discounts );?>
</div>
