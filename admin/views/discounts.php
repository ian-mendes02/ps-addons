<?php
function ps_addons_admin_discounts() {
?>
    <div class="wrap">
        <h1>Descontos Podoshop Clube</h1>

        <div>
            <form id="ps-member-discounts">
                <input type="text" name="name" id="name" placeholder="Insira um nome..." />
                <select name="type" id="type">
                    <option value="">Selecione o tipo de desconto</option>
                    <option value="fixed">Desconto fixo</option>
                    <option value="percent">Porcentagem</option>
                </select>
                <input type="text" name="value" id="value" placeholder="Insira o valor..." />
                <button type="submit">Enviar</button>
            </form>
            <div id="ps-status-message-container"></div>
        </div>

        <div>
            <form id="ps-product-lookup">
                <label for="product-name">Selecione os produtos:</label>
                <input type="text" name="product-name" id="product-name-input" />
            </form>
            <div id="ps-products-container"></div>
            <div id="ps-selected-products"></div>
        </div>

        <div id="ps-created-discounts">
            <h2>Descontos Cadastrados:</h2>
            <div>
                <?php foreach (PS_Addons_Discount_Manager::get_discounts() as $discount) : ?>
                    <div class="ps-discount-tile" data-discount-id="<?php echo esc_attr($discount->id); ?>">
                        <span class="ps-discount-name"><?php echo esc_html($discount->name); ?></span>
                        <div class="ps-button-container">
                            <div class="ps-button ps-edit"></div>
                            <div class="ps-button ps-duplicate"></div>
                            <div class="ps-button ps-delete ps-delete-discount" ></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php
}
