import CheckoutModule from '../../../assets/js/public/public-checkout';

describe('Test initPreTAndC', () => {

  document.body.innerHTML = `
    <div class="dr-checkout__el dr-checkout__cloudpay closed">
      <div class="dr-preTAndC-wrapper">
        <input type="checkbox" id="dr-preTAndC">
        <label for="dr-preTAndC">I agree to Terms of Sale and Privacy Policy of Digital River, Inc.</label>
      </div>
      <div class="dr-cloudpay-btn-wrapper dr-checkout__googlepay">
        <div class="dr-cloudpay-btn DRElement" id="dr-googlepay-button">GooglePay</div>
      </div>
      <div class="invalid-feedback" id="dr-preTAndC-err-msg"></div>
    </div>
  `;
  CheckoutModule.initPreTAndC();

  test('.dr-cloudpay-btn should be locked on page load (T&C is unticked)', () => {
    expect($('.dr-cloudpay-btn').css('pointer-events')).toEqual('none');
  });

  test('.dr-cloudpay-btn should be clickable when T&C is ticked', () => {
    $('#dr-preTAndC').prop('checked', true).trigger('change');
    expect($('.dr-cloudpay-btn').css('pointer-events')).toEqual('auto');
  });

  test('#dr-preTAndC-err-msg should be displayed when T&C is unticked', () => {
    $('#dr-preTAndC').prop('checked', false).trigger('change');
    $('.dr-cloudpay-btn-wrapper').trigger('click');
    expect($('#dr-preTAndC-err-msg').text()).toEqual('Please indicate you have read and accepted the privacy policy and terms of sale.');
  });

  test('#dr-preTAndC-err-msg should be hidden when T&C is ticked', () => {
    $('#dr-preTAndC').prop('checked', true).trigger('change');
    expect($('#dr-preTAndC-err-msg').text()).toEqual('');
  });

});

describe('Test updateSummaryLabels', () => {

  test('It should display "Estimated" at tax/shipping label when the section is unfinished', () => {
    document.body.innerHTML = `
      <div class="dr-checkout-wrapper__content">
        <div class="dr-checkout">
          <div class="dr-checkout__email"></div>
          <div class="dr-checkout__shipping"></div>
          <div class="dr-checkout__billing"></div>
          <div class="dr-checkout__delivery"></div>
          <div class="dr-checkout__payment"></div>
          <div class="dr-checkout__confirmation"></div>
        </div>
        <div class="dr-summary">
          <div class="dr-summary__tax">
            <p class="item-label">Estimated Tax</p>
            <p class="item-label">0.00USD</p>
          </div>
          <div class="dr-summary__shipping">
            <p class="item-label">Estimated Shipping</p>
            <p class="item-label">5.00USD</p>
          </div>
        </div>
      </div>`;

    const deliverySection = document.getElementsByClassName('dr-checkout__shipping')[0];
    const paymentSection = document.getElementsByClassName('dr-checkout__payment')[0];
    const taxLabel = document.querySelector('.dr-summary__tax .item-label');
    const shippingLabel = document.querySelector('.dr-summary__shipping .item-label');

    deliverySection.classList.add('active');
    CheckoutModule.updateSummaryLabels();
    expect(taxLabel.innerHTML).toEqual('Estimated Tax');
    expect(shippingLabel.innerHTML).toEqual('Estimated Shipping');

    deliverySection.classList.remove('active');
    paymentSection.classList.add('active');
    CheckoutModule.updateSummaryLabels();
    expect(taxLabel.innerHTML).toEqual('Tax');
    expect(shippingLabel.innerHTML).toEqual('Shipping');
  });

});
