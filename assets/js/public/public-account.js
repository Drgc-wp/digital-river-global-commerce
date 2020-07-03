import FloatLabel from './float-label';
import $ from 'jquery';
import DRCommerceApi from './commerce-api';
import CheckoutUtils from './checkout-utils';

const AccountModule = {};

$(() => {

    if ($('#dr-account-page-wrapper').length < 1) return;

    window.drActiveOrderId = '';

    var $body = $('body');

    var $ordersModal = $('#ordersModal');

    $body.append($ordersModal);

    // Order detail click
    function fillOrderModal(e) {
        var orderID = $(this).attr('data-order');

        if (!drOrders[orderID]) alert('order details not available');

        if (orderID === drActiveOrderId) {
            $ordersModal.drModal('show');
        } else {
            // orderID
            $('.dr-modal-orderNumber').text(orderID);
            // Order Pricing
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
            // Products
            var html = '';
            for (var i = 0; i < drOrders[orderID].products.length; i++) {
                var prod = drOrders[orderID].products[i];
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

    // watch account page active tab to start on the same tab after reload
    if (sessionStorage.drAccountTab && $('#dr-account-page-wrapper a[data-toggle="dr-list"][href="' + sessionStorage.drAccountTab + '"]').length) {
        $('#dr-account-page-wrapper a[data-toggle="dr-list"][href="' + sessionStorage.drAccountTab + '"]').drTab('show');
    } else if (window.matchMedia && window.matchMedia('(min-width:768px)').matches) {
        $('#dr-account-page-wrapper a[data-toggle="dr-list"]').eq(0).drTab('show');
    }
    $('#dr-account-page-wrapper a[data-toggle="dr-list"]').on('shown.dr.bs.tab', function(e) {
        //console.log('shown');
      sessionStorage.drAccountTab = $(e.target).attr('href');
    });

    // Address
    var $addresses = $('#dr-account-page-wrapper .address');
    // change primary address
    $addresses.on('click', function(e) {

        //console.log('address click');

        var $this = $(this);
        if ($(e.target).is('.address-edit-btn')) {

            //console.log('address edit btn');

            $this.parent().addClass('expand');
            setTimeout(function(){
                $this.find('.address-edit').slideDown(200, function() {
                    $('html, body').animate({
                        scrollTop: $this.offset().top - 50
                    }, 200);
                });
            }, 200);
        } else if ($(e.target).closest('.address-edit').length) {

            return; // handled by form submit callback

        } else {
            if ($this.attr('data-primary')) return;
            $addresses.removeAttr('data-primary');
            $this.attr('data-primary', 'Primary');
            saveAddress($this.find('form.dr-panel-edit'));
        }
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
        var address = {
            address: {
                id: $form.find('input[name="id"]').val(),
                nickName: $form.find('input[name="line1"]').val(),
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
                isDefault: !!( $form.closest('.address').length && $form.closest('.address').attr('data-primary') )
            }
        };
        // console.log(address);
        saveShopperAddress(address, $form);
    }

    function saveShopperAddress(address, $selector) {
        $.ajax({
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${drgc_params.accessToken}`
            },
            data: JSON.stringify(address),
            url: 'https://' + drgc_params.domain + '/v1/shoppers/me/addresses/' + address.address.id,
            success: () => {
                console.log('%c%s', 'color:#155724; background:#d4edda; padding:2px; border:1px solid #155724; display: block;', 'address update success.');
                $selector.closest('.address-edit').slideUp(200, function(){
                    if ($selector.closest('.expand').length < 1) return;
                    $selector.closest('.expand').removeClass('expand');
                    setTimeout(function(){
                        $('html, body').animate({
                            scrollTop: $selector.closest('.address').offset().top - 50
                        }, 200);
                    }, 200);
                });
            },
            error: (jqXHR) => {
                console.error(jqXHR);
                $selector.closest('.address').css('border', '2px solid red');
                location.reload();
            }
        });
    }

    $addresses.find('form.dr-panel-edit').on('submit', function(e) {
        e.preventDefault();
        saveAddress(e.target);
    });

    //floating labels
    FloatLabel.init();

    // Subscriptions
    var $subs = $('#dr-account-page-wrapper .subscription');
    var $subscriptionError = $('#subscriptionError');
    var $subscriptionConfirm = $('#subscriptionConfirm');
    var $subscriptionConfirmAccept = $subscriptionConfirm.find('.dr-confirm-ar-off');
    var $subscriptionConfirmCancel = $subscriptionConfirm.find('.dr-confirm-cancel');


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

        $body.data({
            currentToggle: {
                selector: $this,
                subID: subID,
                ar: ar
            }
        });

        var data = {
            action         : 'drgc_toggle_auto_renewal_ajax',
            nonce          : drgc_params.ajaxNonce,
            subscriptionId : subID,
            renewalType    : ar
        };

        if (ar === 'Manual') {
            $subscriptionConfirm.drModal({
                backdrop: 'static',
                keyboard: false
            });
        } else {
            updateSubscription(data, $this);
        }

    });
    // subscription confirm click events
    $subscriptionConfirmAccept.on('click', function() {
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
    $subscriptionConfirmCancel.on('click', function() {
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

    $body.append($subscriptionError).append($subscriptionConfirm);

    // mobile back button
    $('#dr-account-page-wrapper .back').on('click', function() {
        $('.dr-tab-pane').removeClass('active show');
        $('.dr-list-group-item').removeClass('active').attr('aria-selected', 'false');
    });



});

export default AccountModule;