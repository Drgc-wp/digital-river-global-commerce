import CheckoutUtils from './checkout-utils';

const ThankYouModule = (($) => {
  const updateSummaryPricing = (order) => {
    const lineItems = order.lineItems.lineItem;
    const pricing = order.pricing;
    const newPricing = CheckoutUtils.getSeparatedPricing(lineItems, pricing);

    $('div.dr-summary__shipping > .item-value').text(
      pricing.shipping.value === 0 ?
      params.translations.free_label :
      newPricing.formattedShippingAndHandling
    );
    $('div.dr-summary__tax > .item-value').text(newPricing.formattedProductTax);
    $('div.dr-summary__shipping-tax > .item-value').text(newPricing.formattedShippingTax);
    $('div.dr-summary__subtotal > .subtotal-value').text(newPricing.formattedSubtotal);
  };

  return {
    updateSummaryPricing
  };
})(jQuery);

jQuery(document).ready(($) => {
    if ($('.dr-thank-you-wrapper:visible').length) {
        if (drgc_params.order && drgc_params.order.order) ThankYouModule.updateSummaryPricing(drgc_params.order.order);

        $(document).on('click', '#print-button', function() {
            var printContents = $('.dr-thank-you-wrapper').html();
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });

        const digitalriverjs = new DigitalRiver(drgc_params.digitalRiverKey);
        CheckoutUtils.applyLegalLinks(digitalriverjs);

        $(document).on('click', '#my-subs-btn', () => {
            window.location.href = drgc_params.mySubsUrl;
        });
    }
});

export default ThankYouModule;
