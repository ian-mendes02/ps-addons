<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <table class="ps-new-discount-container">
        <tr id="ps-new-discount" data-discount-id="" data-row-action="create">
            <td>
                <div class="ps-inline-flex">
                    <h2>Descontos</h2>
                    <a class="ps-link-button" data-action="edit">Criar Novo</a>
                </div>
            </td>
        </tr>
    </table>
    <div>
        <table id="ps-discounts-list-table" class="ps-list-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Válido até</th>
                    <th>Publicado em</th>
                    <th>Última edição</th>
                    <th>Ativo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( PS_Discount_Manager::get_discounts() as $discount ): ?>
                    <tr data-discount-id="<?php echo esc_attr( $discount->id ); ?>" data-row-action="edit">
                        <td class="ps-discount-name-data">
                            <div>
                                <span><?php echo esc_html( $discount->name ); ?></span>
                                <div class="ps-list-item-controls">
                                    <a data-action="edit">Editar</a>&nbsp;|
                                    &nbsp;<a data-action="copy">Duplicar</a>&nbsp;|
                                    &nbsp;<a data-action="delete">Excluir</a>
                                </div>
                            </div>
                        </td>
                        <td><?php echo esc_html( $discount->type == 'fixed' ? 'Fixo' : 'Porcentagem' ); ?></td>
                        <td><?php echo esc_html( ps_format_value( $discount->value, $discount->type ) ); ?></td>
                        <td><?php echo esc_html( ps_esc_datetime( $discount->expires_on ) ); ?></td>
                        <td><?php echo html_entity_decode( ps_esc_datetime( $discount->created_on ) . '<br/> por ' . $discount->author ); ?></td>
                        <td><?php echo html_entity_decode( ps_esc_datetime( $discount->last_modified ) . '<br/> por ' . $discount->last_edited_by ); ?></td>
                        <td class="ps-slider-container"><span class="ps-slider <?php echo $discount->is_active == 1 ? 'active' : ''; ?>" name="discount_active"></span></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Válido até</th>
                    <th>Publicado em</th>
                    <th>Última edição</th>
                    <th>Ativo</th>
                </tr>
            </tfoot>
        </table>

        <table>
            <tr id="ps-inline-edit">
                <td colspan=''>
                    <div class="ps-inline-edit-wrapper">
                        <div class="ps-inline-col-left">
                            <fieldset data-meta-field="discount_data">
                                <legend>Dados do desconto</legend>
                                <div class="ps-inline-wrapper">
                                    <label>Nome do desconto</label>
                                    <input type='text' name='discount_name' value='' placeholder='Insira um nome...' required maxlength="32" />
                                    <?php ps_tooltip( 'Este campo é obrigatório e aceita até 32 caracteres' );?>
                                </div>
                                <div class="ps-inline-wrapper">
                                    <label>Valor do desconto</label>
                                    <input type='number' name='discount_value' value='' placeholder='Insira o valor...' />
                                </div>
                                <div class="ps-inline-wrapper">
                                    <label>Tipo de desconto</label>
                                    <select name='discount_type'>
                                        <option value='fixed'>Desconto fixo</option>
                                        <option value='percent'>Porcentagem</option>
                                    </select>
                                </div>
                            </fieldset>
                            <fieldset data-meta-field="discount_schedule">
                                <legend>Duração & Prioridade</legend>
                                <div class="ps-inline-wrapper">
                                    <label>Desconto válido até</label>
                                    <input type='datetime-local' name='discount_expires_on' class='ps-input ps-text-input' value='' />
                                    <?php ps_tooltip( 'Insira uma data de validade para esse desconto. Este campo é opcional.' );?>
                                </div>
                                <div class="ps-inline-wrapper">
                                    <label>Prioridade</label>
                                    <input type="number" min="0" max="999" step="1" value="0" name="discount_priority" placeholder="0-999">
                                    <?php ps_tooltip( 'O número deve ser um valor de 0 a 999 e não pode ser negativo. Números menores terão prioridade sobre números maiores. Se dois descontos tiverem a mesma prioridade, o desconto criado mais recentemente será priorizado.' );?>
                                </div>
                            </fieldset>
                        </div>
                        <div class="ps-inline-col-right">
                            <fieldset data-meta-field="included_products">
                                <legend>Aplicar desconto a</legend>
                                <div class="ps-inline-container">
                                    <span class="ps-selection-field">
                                        <ul class="ps-selected-products"></ul>
                                        <input type='text' name='product_name' class='ps-input ps-text-input product-name-input' placeholder="Pesquisar produtos..." />
                                        <ul class="ps-products-dropdown"></ul>
                                    </span>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="ps-inline-edit-footer">
                        <a class="ps-link-button ps-disabled" data-action="save">Salvar</a>
                        <a class="ps-link-button" data-action="cancel">Cancelar</a>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
