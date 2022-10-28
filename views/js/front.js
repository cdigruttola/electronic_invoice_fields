/**
 * 2007-2022 Carmine Di Gruttola
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    cdigruttola <c.digruttola@hotmail.it>
 *  @copyright 2007-2022 Carmine Di Gruttola
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 *
 */

$(document).ready(function () {
    var address_delivery_checked = $('#checkout-addresses-step div.js-address-form #delivery-addresses input[name=id_address_delivery]:checked').val();
    var address_invoice_checked = $('#checkout-addresses-step div.js-address-form #invoice-addresses input[name=id_address_invoice]:checked').val();
    var chk = $('select[name=id_addresscustomertype] option:selected').val();

    var need_invoice = false;
    needInvoice(chk).then((result) => {
        if (result) {
            need_invoice = result.need_invoice;
            toggleCustomerType(need_invoice);
        }
    });

    if (address_delivery_checked != null && address_delivery_checked != 'undefined') {
        addressNeedInvoice(address_delivery_checked).then((result) => {
            if (result) {
                need_invoice = result.need_invoice;
                toggleCustomerType(need_invoice);
            }
        });
    }

    if (address_invoice_checked != null && address_invoice_checked != 'undefined') {
        addressNeedInvoice(address_invoice_checked).then((result) => {
            if (result) {
                need_invoice = result.need_invoice;
                toggleCustomerType(need_invoice);
            }
        });
    }

    $('select[name=id_addresscustomertype]').change(function (e) {
        e.preventDefault();
        chk = $('select[name=id_addresscustomertype] option:selected').val();
        needInvoice(chk).then((result) => {
            if (result) {
                need_invoice = result.need_invoice;
                toggleCustomerType(need_invoice);
            }
        });
    });
    $('#checkout-addresses-step div.js-address-form #delivery-addresses input[name=id_address_delivery]').change(function (e) {
        e.preventDefault();
        chk = $('#checkout-addresses-step div.js-address-form #delivery-addresses input[name=id_address_delivery]:checked').val();
        addressNeedInvoice(chk).then((result) => {
            if (result) {
                need_invoice = result.need_invoice;
                toggleCustomerType(need_invoice);
            }
        });
    });
    $('#checkout-addresses-step div.js-address-form #invoice-addresses input[name=id_address_invoice]').change(function (e) {
        e.preventDefault();
        chk = $('#checkout-addresses-step div.js-address-form #invoice-addresses input[name=id_address_invoice]:checked').val();
        addressNeedInvoice(chk).then((result) => {
            if (result) {
                need_invoice = result.need_invoice;
                toggleCustomerType(need_invoice);
            }
        });
    });
});

function needInvoice(chk) {
    return new Promise((resolve) => {
        $.ajax({
            type: 'GET',
            url: ajax_link,
            dataType: "json",
            headers: {Accept: "application/json"},
            data: {
                id: chk
            },
            success: function (result) {
                resolve(result);
            }
        });
    });
}

function addressNeedInvoice(address) {
    return new Promise((resolve) => {
        $.ajax({
            type: 'GET',
            url: ajax_link,
            dataType: "json",
            headers: {Accept: "application/json"},
            data: {
                id_address: address
            },
            success: function (result) {
                resolve(result);
            }
        });
    });
}

function toggleCustomerType(need_invoice) {
    var obj_first_name = $('input[name=firstname]');
    var obj_last_name = $('input[name=lastname]');
    var obj_company = $('input[name=company]');
    var obj_vat_number = $('input[name=vat_number]');

    var obj_sdi = $('input[name=sdi]');
    var obj_pec = $('input[name=pec]');
    var obj_dni = $('input[name=dni]');
    var address_form_message = $('#checkout-addresses-step div.js-address-form p').first().not('button');
    var address_receipt = $('#checkout-addresses-step input[name=use_same_address]').parent().find('label,p');

    let speed = 50;
    if (need_invoice) {
        if (virtual) {
            address_form_message.text(invoice_virtual);
        } else {
            address_form_message.text(invoice_no_virtual);
        }
        address_receipt.text(address_delivery_as_invoice);

        obj_first_name.prop('required', false);
        obj_first_name.closest('.form-group').hide(speed);
        obj_last_name.prop('required', false);
        obj_last_name.closest('.form-group').hide(speed);

        obj_company.closest('.form-group').show(speed);
        obj_company.prop('required', true);
        if (!obj_company.closest('.form-group').find('label.form-control-label').hasClass('required')) {
            obj_company.closest('.form-group').find('label.form-control-label').addClass('required');
        }

        obj_dni.closest('.form-group').hide(speed);

        obj_company.closest('.form-group').find('.form-control-comment').html('');
        obj_vat_number.closest('.form-group').show(speed);
        obj_vat_number.prop('required', true);
        if (!obj_vat_number.closest('.form-group').find('label.form-control-label').hasClass('required')) {
            obj_vat_number.closest('.form-group').find('label.form-control-label').addClass('required');
        }
        obj_vat_number.closest('.form-group').find('.form-control-comment').html('');
        obj_sdi.closest('.form-group').show(speed);
        if (sdi_required) {
            obj_sdi.prop('required', true);
            if (!obj_sdi.closest('.form-group').find('label.form-control-label').hasClass('required')) {
                obj_sdi.closest('.form-group').find('label.form-control-label').addClass('required');
            }
            obj_sdi.closest('.form-group').find('.form-control-comment').html('');
        }
        obj_pec.closest('.form-group').show(speed);
        if (pec_required) {
            obj_pec.prop('required', true);
            if (!obj_pec.closest('.form-group').find('label.form-control-label').hasClass('required')) {
                obj_pec.closest('.form-group').find('label.form-control-label').addClass('required');
            }
            obj_pec.closest('.form-group').find('.form-control-comment').html('');
        }
    } else {
        if (virtual) {
            address_form_message.text(receipt_virtual);
        } else {
            address_form_message.text(receipt_no_virtual);
        }
        address_form_message.append("<br/><span>" + receipt + "</span>");
        address_receipt.text(address_delivery_as_receipt);

        obj_first_name.prop('required', true);
        obj_first_name.closest('.form-group').show(speed);
        obj_last_name.prop('required', true);
        obj_last_name.closest('.form-group').show(speed);

        obj_company.closest('.form-group').hide(speed);
        obj_company.prop('required', false);
        if (obj_company.closest('.form-group').find('label.form-control-label').hasClass('required')) {
            obj_company.closest('.form-group').find('label.form-control-label').removeClass('required');
        }

        obj_dni.closest('.form-group').show(speed);

        obj_vat_number.closest('.form-group').hide(speed);
        obj_vat_number.prop('required', false);
        if (obj_vat_number.closest('.form-group').find('label.form-control-label').hasClass('required')) {
            obj_vat_number.closest('.form-group').find('label.form-control-label').removeClass('required');
        }
        obj_sdi.closest('.form-group').hide(speed);
        obj_sdi.prop('required', false);
        if (obj_sdi.closest('.form-group').find('label.form-control-label').hasClass('required')) {
            obj_sdi.closest('.form-group').find('label.form-control-label').removeClass('required');
        }
        obj_pec.closest('.form-group').hide(speed);
        obj_pec.prop('required', false);
        if (obj_pec.closest('.form-group').find('label.form-control-label').hasClass('required')) {
            obj_pec.closest('.form-group').find('label.form-control-label').removeClass('required');
        }
    }
}
