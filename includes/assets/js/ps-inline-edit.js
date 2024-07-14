( function( $, discount_data, ajax_url, loading_url ) {

    window.ps ??= {};
    window.ps.inline_edit = {};

    $( 'tr[data-discount-id]' ).each( function() {

        const row = $( this )
            , is_edit = row.attr( 'data-row-action' ) == 'edit'
            , id = is_edit ? row.attr( 'data-discount-id' ) : 'ps-new-discount'
            , discount = is_edit ? discount_data.find( ( obj ) => obj.id == id ) : null
            , inline_id = 'inline-edit-' + id
            , controls = {
                edit: row.find( 'a[data-action=edit]' ),
                copy: row.find( 'a[data-action=copy]' ),
                delete: row.find( 'a[data-action=delete]' ),
                toggle: row.find( '[name="discount_active"]' )
            }
            , wp_json_parse = r => r.slice( -1 ) == 0 && JSON.parse( r.slice( 0, -1 ) )
            , is_obj = ( o1, o2 ) => JSON.stringify( o1 ) == JSON.stringify( o2 );

        function loading( width = 24, className = '' ) {
            return `<img 
                src='${loading_url}' 
                width='${width}px' 
                height='${width}px'
                class='${className}'
                draggable='false' 
            />`;
        }

        /**
         * Update the global form data object.
         * @since 1.0.0
         */
        function update_data() {
            window.ps.inline_edit.data = get_field_data();
            var row_edit = $( '#' + inline_id )
                , save = row_edit.find( '[data-action=save]' )
                , inline = window.ps.inline_edit
                , change = !is_obj( inline._data, inline.data );
            change ? save.removeClass( 'ps-disabled' ) : save.addClass( 'ps-disabled' );
            return change;
        }

        /**
         * Remove selected product from list and global array.
         * @since 1.0.0
         */
        function remove_selected() {
            var t = $( this )
                , li = t.closest( 'li' )
                , id = li.attr( 'id' )
                , prods = window.ps.inline_edit.data.included_products;
            for ( var i = 0; i < prods.length; i++ ) if ( prods[i].id == id ) {
                window.ps.inline_edit.data.included_products.splice( i, 1 );
                li.remove();
                update_data();
            }
        }

        /**
         * Render selected product(s) to list container.
         * @param {object[]} products 
         * @since 1.0.0
         */
        function render_selected( products ) {
            var container = $( '#' + inline_id ).find( 'ul.ps-selected-products' );
            for ( const p of products ) if ( !window.ps.inline_edit.included_products.find( ( prod ) => prod.id == p.id ) ) {
                var li = '<li class="ps-included-product" id="' + p.id + '"><span>' + p.name + '</span></li>'
                    , del = '<span class="ps-remove-selected-product"></span>';
                container.append( $( li ).prepend( $( del ).on( 'click', remove_selected ) ) );
                window.ps.inline_edit.included_products.push( p );
            };
            update_data();
        }

        /**
         * List item template for product dropdown.
         * @param {object} product
         * @since 1.0.0
         */
        function list_item( p ) {
            var li = '<li class="ps-list-item ps-product-option" data-product-id="' + p.id + '">' + p.name + '</li>';
            return $( li ).on( 'click', () => render_selected( [p] ) );
        }

        /**
         * Ajax product lookup.
         * @since 1.0.0
         */
        function product_lookup() {
            var prods, params
                , input = $( this )
                , query = input.val()
                , dropdown = $( '#' + inline_id + ' .ps-products-dropdown' );
            query.length < 2
                ? dropdown.empty().hide()
                : ( params = {action: 'product_lookup', query: query, limit: 5},
                    dropdown.show().html( `<span class="ps-full ps-center">${loading()}</span>` ),
                    $.post( ajax_url, params, function( res ) {
                        if ( res && res.length != 0 ) {
                            prods = wp_json_parse( res );
                            dropdown.empty();
                            // Render queried products to list
                            for ( let prod of prods )
                                dropdown.append( list_item( prod ) );
                        } else if ( res.length == 0 ) dropdown.empty();
                    } ) );
        };

        /**
         * Bind functions to keypresses.
         * @since 1.0.0
         */
        const keybind = e => {
            e.key == 'Enter' && save();
            e.key == 'Escape' && cancel();
        };

        /**
         * Save changes and reload page if successful.
         * @since 1.0.0
         */
        function save() {
            if ( update_data() ) {
                $( loading() ).insertBefore( $( this ) );
                var data = window.ps.inline_edit.data, params;
                data.is_active = is_edit ? ( controls.toggle.hasClass( 'active' ) ? 1 : 0 ) : 1;
                if ( !data.name || data.name == '' ) {
                    alert( 'O campo \'Nome\' não pode ficar em branco.' );
                } else {
                    params = {action: 'submit_discount', data: data},
                        $.post( ajax_url, params, function( res ) {
                            var data = wp_json_parse( res );
                            data != 1
                                ? data.status == 'error' && alert( data.message )
                                : location.reload();
                        } );
                };
            }
        };

        /**
         * Cancel editing, dismiss form and unbind event keys.
         * @since 1.0.0
         */
        function cancel() {
            $( document ).off( 'keydown', keybind );
            $( '#ps-new-discount-header' ).remove();
            // Dismiss and remove form.
            $( '#' + inline_id ).siblings( 'tr.hidden' ).addBack().remove();
            row.show();
        }

        /**
         * Set a global `fields` object if undefined and return it.
         * @since 1.0.0
         */
        function get_fields() {
            var row_edit = $( '#' + inline_id );
            window.ps.inline_edit.fields ??= {
                name: row_edit.find( '[name="discount_name"]' ),
                type: row_edit.find( '[name="discount_type"]' ),
                value: row_edit.find( '[name="discount_value"]' ),
                schedule: row_edit.find( '[name="discount_expires_on"]' ),
                priority: row_edit.find( '[name="discount_priority"]' ),
            }; return window.ps.inline_edit.fields;
        }

        /**
         * Gather field data.
         * @since 1.0.0
         */
        function get_field_data() {
            var fields = get_fields();
            return {
                id: id,
                name: fields.name.val(),
                type: fields.type.val(),
                value: fields.value.val(),
                schedule: fields.schedule.val().replace( 'T', ' ' ),
                priority: fields.priority.val(),
                included_products: window.ps.inline_edit.included_products
            };
        }

        /**
         * Render form and attach event listeners.
         * Other rows will be collapsed and changes will be discarded.
         * @since 1.0.0
         */
        function edit_discount() {

            // Collapse sibling rows and discard changes
            $( 'tr[data-discount-id]' ).each( function() {
                $( '[id^="inline-edit-"]' ).siblings( 'tr.hidden' ).addBack().remove();
                $( this ).show().removeClass( 'active' );
            } );

            // Empty global inline object
            window.ps.inline_edit = {};

            // Empty global products array
            window.ps.inline_edit.included_products = [];

            row.addClass( 'active' );

            var row_edit = $( '#ps-inline-edit' ).clone( true );

            // Hide row and display form with an empty row underneath to preserve zebra striping.
            row.hide().after( row_edit ).after( '<tr class="hidden"></tr>' );

            // Add title if creating new item.
            !is_edit && row.before( $( '<h2 id="ps-new-discount-header">Novo Desconto</h2>' ) );

            // Set row attributes
            row_edit.attr( 'id', inline_id );
            row_edit.find( 'td' ).attr( 'colspan', $( '#ps-discounts-list-table thead th' ).length );

            var fields = get_fields();

            is_edit && (
                // Populate form fields
                fields.name.val( discount.name ),
                fields.type.find( `option[value="${discount.type}"]` ).attr( 'selected', 'selected' ),
                fields.value.val( discount.value ),
                fields.priority.val( discount.priority ),
                fields.schedule.val( discount.expires_on.replace( ' ', 'T' ) )
            );

            var dropdown = row_edit.find( '.ps-products-dropdown' ),
                lookup = row_edit.find( '[name="product_name"]' );

            // Attach listeners to input fields
            row_edit.find( 'input[name]:not(.product-name-input)' ).on( 'input', update_data );
            row_edit.find( 'select' ).on( 'change', update_data );
            lookup.on( 'input', product_lookup );
            lookup.on( 'focus', () => dropdown.show() );
            fields.type.on( 'change', function() {
                $( this ).val() != 'percent'
                    ? fields.value.attr( 'max', '' )
                    : (
                        fields.value.val() > 100 && fields.value.val( 100 ),
                        fields.value.attr( 'max', '100' )
                    );
            } );
            fields.value.on( 'change', function() {
                var t = $( this );
                fields.type.val() == 'percent' && t.val() > 100 && t.val( 100 );
            } );
            $( document ).on( 'click', e => {
                var t = $( e.target );
                !t.parent().is( dropdown ) && !t.is( lookup ) && !t.is( dropdown ) && (
                    dropdown.hide(),
                    lookup.val( '' )
                );
            } );

            var inc_prods = is_edit ? discount.included_products : [];

            // Store initial form state for comparison
            window.ps.inline_edit._data = get_field_data();
            window.ps.inline_edit._data.included_products = inc_prods;

            // Render existing included products.
            render_selected( inc_prods );

            // Attach save and cancel listeners
            row_edit.find( '[data-action=save]' ).on( 'click', save );
            row_edit.find( '[data-action=cancel]' ).on( 'click', cancel );
            $( document ).on( 'keydown', keybind );
        };

        /**
         * Create a copy of current row.
         * @since 1.0.0
         */
        function duplicate_discount() {
            var tp = $( this ).parent(), ld = $( loading( 12 ) );
            tp.append( ld );
            $.post( ajax_url, {action: 'duplicate_discount', id: id}, () => {
                tp.remove();
                location.reload();
            } ).catch( () => tp.remove() );
        };

        /**
         * Delete current row.
         * @since 1.0.0
         */
        function delete_discount() {
            var params, tp = $( this ).parent(), ld = $( loading( 12 ) );
            confirm( `Excluir o item '${discount.name}'?\n(Essa ação não pode ser desfeita)` ) && (
                tp.append( ld ),
                params = {action: 'delete_discount', id: id},
                $.post( ajax_url, params, () => {
                    tp.remove();
                    location.reload();
                } ).catch( () => tp.remove() )
            );
        };

        /**
         * Update 'is_active' data without reloading the page.
         * @since 1.0.0
         */
        function update_status() {
            var t = $( this ), ld = $( `<span class='ps-append-left'>${loading( 16 )}</span>` );
            t.parent().append(ld);
            $.post( ajax_url, {
                action: 'update_discount_status',
                id: id,
                is_active: t.hasClass( 'active' ) ? 0 : 1
            }, () => {
                t.toggleClass( 'active' );
                ld.remove();
            } ).catch( () => ld.remove() );
        };

        //Attach event listeners for current row.
        controls.copy.on( 'click', duplicate_discount );
        controls.edit.on( 'click', edit_discount );
        controls.delete.on( 'click', delete_discount );
        controls.toggle.on( 'click', update_status );

    } );

    $( '.ps-tooltip' ).on( 'mouseenter', function() {$( this ).find( 'p' ).show();} );
    $( '.ps-tooltip' ).on( 'mouseleave', function() {$( this ).find( 'p' ).hide();} );

}( jQuery, ps_ajax.discount_data, ps_ajax.ajax_url, ps_ajax.loading_url ) );