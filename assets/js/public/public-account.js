import FloatLabel from './float-label';
import $ from 'jquery';
import DRCommerceApi from './commerce-api';
import CheckoutUtils from './checkout-utils';
import LoginModule from './public-login';

const AccountModule = (($) => {
    const appendAutoRenewalTerms = (digitalriverjs, entityCode, locale) => {
        const terms = CheckoutUtils.getLocalizedAutoRenewalTerms(digitalriverjs, entityCode, locale);

        if (terms) {
            $('#dr-autoRenewalPlanTerms').find('.dr-modal-body > p').append(terms);
        }
    };

    return {
        appendAutoRenewalTerms
    };
})(jQuery);

$(() => {
    const localizedText = drgc_params.translations;

    if ($('#dr-account-page-wrapper').length < 1) return;

    window.drActiveOrderId = '';

    var $body = $('body');

    var $ordersModal = $('#ordersModal');

    $body.append($ordersModal);

    // Order detail click
    function fillOrderModal(e) {
        var orderID = $(this).attr('data-order');

        if (!drOrders[orderID]) alert('order details not available');

        const requestShipping = drOrders[orderID].shippingMethodCode !== '';

        if (orderID === drActiveOrderId) {
            $ordersModal.drModal('show');
        } else {
            // orderID
            $('.dr-modal-orderNumber').text(orderID);
            // Order Pricing
            drOrders[orderID].pricing = $.parseJSON(drOrders[orderID].encodedPricing);
            $('.dr-modal-subtotal').text(drOrders[orderID].formattedSubtotal);
            $('.dr-modal-tax').text(drOrders[orderID].formattedTax);
            $('.dr-modal-shipping').text(drOrders[orderID].formattedShipping);
            var isDiscount = parseInt(drOrders[orderID].formattedIncentive.replace(/\D/g, ''));
            if (isDiscount) {
                $('.dr-modal-discount').text(drOrders[orderID].formattedIncentive);
                $('.dr-summary__discount').show();
            } else {
                $('.dr-summary__discount').hide();
            }
            $('.dr-modal-total').text(drOrders[orderID].formattedTotal);
            // Billing
            $('.dr-modal-billingName').text(drOrders[orderID].billingAddress.firstName + ' ' + drOrders[orderID].billingAddress.lastName);
            var billingAddress1 = drOrders[orderID].billingAddress.line1;
            billingAddress1 += (drOrders[orderID].billingAddress.line2) ? '<br>' + drOrders[orderID].billingAddress.line2 : '';
            $('.dr-modal-billingAddress1').html(billingAddress1);
            var billingAddress2 = (drOrders[orderID].billingAddress.city) ? drOrders[orderID].billingAddress.city : '';
            billingAddress2 += (drOrders[orderID].billingAddress.state) ? ', ' + drOrders[orderID].billingAddress.state : '';
            billingAddress2 += (drOrders[orderID].billingAddress.zip) ? ' ' + drOrders[orderID].billingAddress.zip : '';
            $('.dr-modal-billingAddress2').text(billingAddress2);
            $('.dr-modal-billingCountry').text(drOrders[orderID].billingAddress.country);
            // Shipping
            $('.dr-modal-shippingName').text(drOrders[orderID].shippingAddress.firstName + ' ' + drOrders[orderID].shippingAddress.lastName);
            var shippingAddress1 = drOrders[orderID].shippingAddress.line1;
            shippingAddress1 += (drOrders[orderID].shippingAddress.line2) ? '<br>' + drOrders[orderID].shippingAddress.line2 : '';
            $('.dr-modal-shippingAddress1').html(shippingAddress1);
            var shippingAddress2 = (drOrders[orderID].shippingAddress.city) ? drOrders[orderID].shippingAddress.city : '';
            shippingAddress2 += (drOrders[orderID].shippingAddress.state) ? ', ' + drOrders[orderID].shippingAddress.state : '';
            shippingAddress2 += (drOrders[orderID].shippingAddress.zip) ? ' ' + drOrders[orderID].shippingAddress.zip : '';
            $('.dr-modal-shippingAddress2').text(shippingAddress2);
            $('.dr-modal-shippingCountry').text(drOrders[orderID].shippingAddress.country);

            // Summary Labels
            const isTaxInclusive = drOrders[orderID].isTaxInclusive === 'true';
            const forceExclTax = drgc_params.forceExclTax === 'true';
            const shouldDisplayVat = drOrders[orderID].shouldDisplayVat === 'true';
            const taxSuffixLabel = isTaxInclusive ?
                forceExclTax ? ' ' + localizedText.excl_vat_label : ' ' + localizedText.incl_vat_label :
                '';
            $('.dr-summary__subtotal .subtotal-label').text(localizedText.subtotal_label + taxSuffixLabel);
            $('.dr-summary__tax .item-label').text(shouldDisplayVat ?
                localizedText.vat_label :
                localizedText.tax_label
            );
            $('.dr-summary__shipping .item-label').text(localizedText.shipping_label + taxSuffixLabel);
            $('.dr-summary__shipping-tax .item-label').text(shouldDisplayVat ?
                localizedText.shipping_vat_label :
                localizedText.shipping_tax_label
            );
            if (isTaxInclusive && !forceExclTax) {
              $('.dr-summary__tax, .dr-summary__shipping-tax').addClass('tree-sub-item');
            } else {
              $('.dr-summary__tax, .dr-summary__shipping-tax').removeClass('tree-sub-item');
            }

            // Products
            var html = '';
            for (var i = 0; i < drOrders[orderID].products.length; i++) {
                var prod = drOrders[orderID].products[i];
                prod.pricing = $.parseJSON(prod.encodedPricing);

                html += `<div class="dr-product">
                <div class="dr-product-content">
                    <div class="dr-product__img dr-modal-productImgBG" style="background-image:url(${prod.image});"></div>
                    <div class="dr-product__info">
                        <a class="product-name dr-modal-productName">${prod.name}</a>
                        <div class="product-sku">
                            <span>Product </span>
                            <span class="dr-modal-productSku">${prod.sku}</span>
                        </div>
                        <div class="product-qty">
                            <span class="qty-text">Qty <span class="dr-modal-productQty">${prod.qty}</span></span>
                            <span class="dr-pd-cart-qty-minus value-button-decrease"></span>
                            <input
                                type="number"
                                class="product-qty-number"
                                step="1"
                                min="1"
                                max="999"
                                value="${prod.qty}"
                                maxlength="5"
                                size="2"
                                pattern="[0-9]*"
                                inputmode="numeric"
                                readonly="true"/>
                            <span class="dr-pd-cart-qty-plus value-button-increase"></span>
                        </div>
                    </div>
                </div>
                <div class="dr-product__price">
                    <span class="sale-price dr-modal-salePrice">${prod.salePrice}</span>
                    <span class="regular-price dr-modal-strikePrice" ${prod.salePrice === prod.strikePrice ? 'style="display:none"' : ''}>${prod.strikePrice}</span>
                </div>
            </div>`;
            }

            $('.dr-summary__products').html(html);

            CheckoutUtils.updateSummaryPricing(drOrders[orderID], isTaxInclusive);

            if (!requestShipping) {
                $('.dr-order-address__shipping, .dr-summary__shipping, .dr-summary__shipping-tax').hide();
            } else {
                $('.dr-order-address__shipping, .dr-summary__shipping, .dr-summary__shipping-tax').show();
            }

            // set this last
            drActiveOrderId = orderID;
            $ordersModal.drModal('show');
        }
    }
    $('.order-details .btn').on('click', fillOrderModal);

    // modal print click
    $ordersModal.find('.dr-modal-footer .print-button').on('click', function() {
        var $dialog = $ordersModal.find('.dr-modal-dialog');
        $dialog.css('max-width', '100%');
        window.print();
        $dialog.css('max-width', '');
    });

    // Address
    var $addresses = $('#dr-account-page-wrapper .address');
    const $deleteAddressModal = $('#dr-deleteAddressConfirm');
    const $deleteAcceptBtn = $deleteAddressModal.find('.dr-delete-confirm');

    $body.append($deleteAddressModal);

    // change primary address
    $addresses.on('click', function(e) {
        var $this = $(this);

        if ($(e.target).is('.address-edit-btn') || $(e.target).is('.address-add-btn')) {
            if ($(e.target).is('.address-add-btn')) {
                $(e.target).hide();
                $this.find('.address-add-text').hide();
            }

            $this.parent().addClass('expand');
            setTimeout(function(){
                $this.find('.address-edit').slideDown(200, function() {
                    $('html, body').animate({
                        scrollTop: $this.offset().top - 50
                    }, 200);
                });
            }, 200);
        } else if ($(e.target).is('.address-delete-btn')) {
            $deleteAddressModal.find('.dr-delete-confirm').data('id' , $(e.target).data('id'));
            $deleteAddressModal.find('p > strong').text($(e.target).data('nickname'));
            $deleteAddressModal.drModal({
                backdrop: 'static',
                keyboard: false
            });
        } else if ($(e.target).is('.address-cancel-btn')) {
            $this.parent().removeClass('expand');
            $this.removeClass('ajax-error');
            setTimeout(function() {
                $this.find('.address-edit').slideUp(200, function() {
                    $('html, body').animate({
                        scrollTop: $this.offset().top - 50
                    }, 200);

                    $this.find('.address-add-btn').show();
                    $this.find('.address-add-text').show();
                });
            }, 200);
        } else if ($(e.target).closest('.address-edit').length) {

            return; // handled by form submit callback

        } else {
            if ($this.attr('data-primary') || $this.hasClass('address-add-new')) return;
            $addresses.removeAttr('data-primary');
            $this.attr('data-primary', 'Primary');
            saveAddress($this.find('form.dr-panel-edit'));
        }
    });

    $deleteAcceptBtn.on('click', (e) => {
        $('body').addClass('dr-loading');
        DRCommerceApi.deleteShopperAddress($(e.target).data('id'))
            .then(() => {
                location.reload();
            })
            .catch((jqXHR) => {
                $('body').removeClass('dr-loading');
                CheckoutUtils.apiErrorHandler(jqXHR);
            });
    });

    // Payment
    $('#dr-account-page-wrapper .payment').on('click', function(e) {
      var $this = $(this);
      if ($(e.target).is('.payment-edit-btn')) {
          $this.parent().addClass('expand');
          setTimeout(function(){
              $this.find('.payment-edit').slideDown(200, function() {
                  $('html, body').animate({
                      scrollTop: $this.offset().top - 50
                  }, 200);
              });
          }, 200);
      } else {
          if ($this.attr('data-primary')) return;
          $('#dr-account-page-wrapper .payment').removeAttr('data-primary');
          $this.attr('data-primary', 'Primary');
          let $form = $this.find('form');
          let payload = {
              'isDefault': true,
              'sourceId': $form.find('input[name="sourceId"]').val(),
              'id'      : $form.find('input[name="id"]').val(),
          };

          updateShopperPayment(payload);
      }
    });

    $('#dr-account-page-wrapper .payment').find('form.dr-panel-edit').on('submit', function(e) {
      e.preventDefault();

      let payload = {
          'nickName': $(this).find('input[name="nickName"]').val(),
          'sourceId': $(this).find('input[name="sourceId"]').val(),
          'id'      : $(this).find('input[name="id"]').val(),
      };

      $(this).find('input[type="submit"]').attr('disabled');
      updateShopperPayment(payload);
    });

    $('#paymentDeleteConfirm .dr-confirm-payment-off').on('click', function() {
      var payment = $body.data('currentPayment');
      deleteShopperPayment(payment.id);
    });

    $('#dr-account-page-wrapper .payment').on('click', '.payment-delete-btn', function(e) {
      e.preventDefault();

      $body.data({
          currentPayment: {
              id: $(this).closest('.payment').find('input[name="id"]').val()
          }
      });

      $body.append($('#paymentDeleteConfirm'));
      $('#paymentDeleteConfirm').drModal({
          backdrop:'static',
          keyboard:false
      });
    });



    function fillAddress() {
        var $this = $(this);
        var target = $this.attr('name');
        $this.closest('.address').find('.' + target).text( $this.val() );
    }

    function updateShopperPayment(payload) {

      $.ajax({
          type: 'POST',
          headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${drgc_params.accessToken}`
          },
          data: JSON.stringify({'paymentOption': payload}),
          url: 'https://' + drgc_params.domain + '/v1/shoppers/me/payment-options/',
          success: () => {
              location.reload();
          },
          error: (jqXHR) => {
              console.error(jqXHR);
              location.reload();
          }
      });
    }

    function deleteShopperPayment(id) {
      $.ajax({
          type: 'DELETE',
          headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${drgc_params.accessToken}`
          },
          url: 'https://' + drgc_params.domain + '/v1/shoppers/me/payment-options/' + id,
          success: () => {
              location.reload();
          },
          error: (jqXHR) => {
              console.error(jqXHR);
              location.reload();
          }
      });
    }


    $addresses.find('input[name="firstName"], input[name="lastName"], input[name="companyName"], input[name="line1"], input[name="line2"], input[name="city"], select[name="countrySubdivision"], input[name="postalCode"], input[name="phoneNumber"]').on('change keyup', fillAddress);

    function saveAddress(form) {
        var $form = $(form);
        const addressObj = {
            address: {
                nickName: $form.find('input[name="nickname"]').val(),
                firstName: $form.find('input[name="firstName"]').val(),
                lastName: $form.find('input[name="lastName"]').val(),
                companyName: $form.find('input[name="companyName"]').val(),
                line1: $form.find('input[name="line1"]').val(),
                line2: $form.find('input[name="line2"]').val(),
                city: $form.find('input[name="city"]').val(),
                countrySubdivision: $form.find('select[name="countrySubdivision"]').val(),
                postalCode: $form.find('input[name="postalCode"]').val(),
                countryName: $form.find('select[name="country"] :selected').text(),
                country: $form.find('select[name="country"]').val(),
                phoneNumber: $form.find('input[name="phoneNumber"]').val(),
                isDefault: false
            }
        };

        if (!$form.is('#dr-new-address-form')) {
            addressObj.address.id = $form.find('input[name="id"]').val();
            addressObj.address.isDefault = !!($form.closest('.address').length && $form.closest('.address').attr('data-primary'));

            if ($form.closest('.expand').length) {
                $form.addClass('dr-loading');
            } else {
                $form.closest('.address-col').addClass('dr-loading');
            }
    
            DRCommerceApi.updateShopperAddress(addressObj)
                .then(() => {
                    location.reload();
                })
                .catch((jqXHR) => {
                    $form.removeClass('dr-loading');
                    $form.closest('.address-col').removeClass('dr-loading');
                    $form.closest('.address').addClass('ajax-error');
                    CheckoutUtils.apiErrorHandler(jqXHR);
                });
        } else {
            $form.addClass('dr-loading');
            DRCommerceApi.saveShopperAddress(addressObj)
                .then(() => {
                    location.reload();
                })
                .catch((jqXHR) => {
                    $form.removeClass('dr-loading');
                    $form.closest('.address').addClass('ajax-error');
                    CheckoutUtils.apiErrorHandler(jqXHR);
                });
        }
    }

    $addresses.find('form.dr-panel-edit').on('submit', function(e) {
        e.preventDefault();
        saveAddress(e.target);
    });

    // Subscriptions
    var $subs = $('#dr-account-page-wrapper .subscription');
    var $subscriptionError = $('#subscriptionError');
    var $subscriptionConfirm = $('#subscriptionConfirm');
    const $autoRenewalPlanTerms = $('#dr-autoRenewalPlanTerms');

    function updateSubscription(data = {}, $toggle) {
        $('body').addClass('dr-loading');
        $.post(drgc_params.ajaxUrl, data, function(response) {
            if (response.success) {
                var $renewalDate = $toggle.closest('.subscription').find('.subscription-dates .nextRenewalDate');

                if ($renewalDate.length) {
                    var renewalText = (data.renewalType === 'Auto') ? $renewalDate.attr('data-on') : $renewalDate.attr('data-off');
                    $renewalDate.find('strong').text(renewalText);
                }
            } else {
                $subscriptionError.drModal('show');
                $toggle.prop('checked', !(data.renewalType === 'Auto'));
            }

            $('body').removeClass('dr-loading');
        });
    }

    $subs.find('.subscription-ar .switch input[type="checkbox"]').on('change', function() {
        var $this = $(this);
        var subID = ($this.closest('.subscription').length && $this.closest('.subscription').attr('data-id')) ? $this.closest('.subscription').attr('data-id') : '';
        var ar = $this.is(':checked') ? 'Auto' : 'Manual';
        const modalId = {
            Auto: '#dr-autoRenewalPlanTerms',
            Manual: '#subscriptionConfirm'
        } 

        $body.data({
            currentToggle: {
                selector: $this,
                subID: subID,
                ar: ar
            }
        });

        $(modalId[ar]).drModal({
            backdrop: 'static',
            keyboard: false
        });
    });
    // subscription confirm click events
    $('button.dr-confirm-ar-off, button.dr-confirm-ar-on').on('click', function() {
        var toggle = $body.data('currentToggle');
        var data = {
            action         : 'drgc_toggle_auto_renewal_ajax',
            nonce          : drgc_params.ajaxNonce,
            subscriptionId : toggle.subID,
            renewalType    : toggle.ar
        };
        updateSubscription(data, toggle.selector);
    });
    // reset toggle if event is canceled
    $('button.dr-confirm-cancel').on('click', function() {
        var toggle = $body.data('currentToggle');
        toggle.selector.prop('checked', !(toggle.ar === 'Auto'));
    });

    $('#list-subscriptions .dr-renew-btn').on('click', (e) => {
        const payload = {
            cart: {
                lineItems: {
                    lineItem: [
                        {
                            quantity: e.target.dataset.renewalQty,
                            product: {
                                id: e.target.dataset.productId
                            },
                            customAttributes: {
                                attribute: [
                                    {
                                        name: 'RenewingSubscriptionID',
                                        value: e.target.dataset.subsId
                                    }
                                ]
                            }
                        }
                    ]
                }
            }
        };

        $('body').addClass('dr-loading');
        DRCommerceApi.updateCart({testOrder: drgc_params.testOrder}, payload)
            .then(() => {
                window.location.href = drgc_params.cartUrl;
            })
            .catch((jqXHR) => {
                CheckoutUtils.apiErrorHandler(jqXHR);
                $('body').removeClass('dr-loading');
            });
    });

    $body.append($subscriptionError).append($subscriptionConfirm).append($autoRenewalPlanTerms);

    if ($('#list-subscriptions .subscription').length && $autoRenewalPlanTerms.length) {
        const locale = drgc_params.drLocale || 'en_US';
        const digitalriverjs = new DigitalRiver(drgc_params.digitalRiverKey, {
            'locale': locale.split('_').join('-')
        });
        let entityCode = CheckoutUtils.getEntityCode();

        if (entityCode) {
            AccountModule.appendAutoRenewalTerms(digitalriverjs, entityCode, locale);
        } else {
            const subsId = $('#list-subscriptions .subscription').first().data('id');

            DRCommerceApi.getSubsDetails(subsId)
                .then((data) => {
                    const orderId = data.subscription.orders.order[0].uri.split('orders/')[1];
                    entityCode = drOrders[orderId].entityCode;
                    AccountModule.appendAutoRenewalTerms(digitalriverjs, entityCode, locale);
                })
                .catch((jqXHR) => {
                    CheckoutUtils.apiErrorHandler(jqXHR);
                });
        }
    }

    // mobile back button
    $('#dr-account-page-wrapper .back').on('click', function() {
        $('.dr-tab-pane').removeClass('active show');
        $('.dr-list-group-item').removeClass('active').attr('aria-selected', 'false');
    });

    // Change Password
    $('#pw-new').on('input', (e) => {
        LoginModule.validatePassword(e);
    });

    $('#pw-current, #pw-new, #pw-confirm').on('input', () => {
        const $form = $('#change-password-form');
        const pw = $form.find('input[type=password]')[0];
        const npw = $form.find('input[type=password]')[1];
        const cpw = $form.find('input[type=password]')[2];

        $form.find('.dr-err-field').text('');
        npw.setCustomValidity(pw.value === npw.value ? localizedText.new_password_error_msg : 
            npw.validationMessage !== localizedText.new_password_error_msg ? npw.validationMessage : '');
        cpw.setCustomValidity(npw.value !== cpw.value ? localizedText.password_confirm_error_msg : '');

        if (npw.validity.valueMissing) {
            $(npw).next('.invalid-feedback').text(localizedText.required_field_msg);
        } else if (npw.validity.customError) {
            $(npw).next('.invalid-feedback').text(npw.validationMessage);
        } else {
            $(npw).next('.invalid-feedback').text('');
        }

        if (cpw.validity.valueMissing) {
            $(cpw).next('.invalid-feedback').text(localizedText.required_field_msg);
        } else if (cpw.validity.customError) {
            $(cpw).next('.invalid-feedback').text(cpw.validationMessage);
        } else {
            $(cpw).next('.invalid-feedback').text('');
        }

        $form.addClass('was-validated');
    });

    $('#change-password-form').on('submit', (e) => {
        e.preventDefault();

        const $form = $(e.target);
        const $error = $form.find('.dr-err-field');

        $form.addClass('was-validated');

        if ($form.data('processing')) return false;
        if (!$form[0].checkValidity()) return false;

        $form.data('processing', true);
        $error.text('');

        const data = {
            action: 'drgc_change_password',
            nonce: drgc_params.ajaxNonce,
            current_password: $('#pw-current').val(),
            new_password: $('#pw-new').val(),
            confirm_new_password: $('#pw-confirm').val()
        };

        $('body').addClass('dr-loading');
        $.post(drgc_params.ajaxUrl, data, (response) => {
            if (!response.success) {
                if (response.data && response.data.errors && response.data.errors.error[0].hasOwnProperty('description')) {
                    $error.text(response.data.errors.error[0].description);
                } else if (Object.prototype.toString.call(response.data) === '[object String]') {
                    $error.text(response.data);
                } else {
                    $error.text(localizedText.undefined_error_msg);
                }

                $error.css('color', 'red');
                sessionStorage.setItem('drgc-pw-changed', 'false');
                $('body').removeClass('dr-loading');
            } else {
                sessionStorage.setItem('drgc-pw-changed', 'true');
                location.reload();
            }

            $('#pw-current, #pw-new, #pw-confirm').val('').removeClass('is-invalid').removeClass('is-valid');
            $form.data('processing', false).removeClass('was-validated');
        });
    });

    // watch account page active tab to start on the same tab after reload
    $('#dr-account-page-wrapper a[data-toggle="dr-list"]').on('shown.dr.bs.tab', function(e) {
        sessionStorage.drAccountTab = $(e.target).attr('href');

        if ((e.target.id === 'list-password-list') && (sessionStorage.getItem('drgc-pw-changed') === 'true')) {
            sessionStorage.setItem('drgc-pw-changed', 'false');
            $('#dr-passwordUpdated').drModal('show');
        }
    });

    if (sessionStorage.drAccountTab && $('#dr-account-page-wrapper a[data-toggle="dr-list"][href="' + sessionStorage.drAccountTab + '"]').length) {
        $('#dr-account-page-wrapper a[data-toggle="dr-list"][href="' + sessionStorage.drAccountTab + '"]').drTab('show');
    } else if (window.matchMedia && window.matchMedia('(min-width:768px)').matches) {
        $('#dr-account-page-wrapper a[data-toggle="dr-list"]').eq(0).drTab('show');
    }

    //floating labels
    FloatLabel.init();
});

export default AccountModule;
