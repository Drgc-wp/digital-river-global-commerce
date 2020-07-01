<?php
$current_locale = DRGC()->shopper->get_locale();
$companyEIN = '';
$billingAddress = $cart['cart']['billingAddress'];
// var_dump($cart['cart']);
// exit;
if ( $customer_address ) {
    $billingAddress = $customer_address[0];
} elseif ( $customer ) {
    $billingAddress['firstName'] = $billingAddress['firstName'] ?: $customer['firstName'];
    $billingAddress['lastName'] = $billingAddress['lastName'] ?: $customer['lastName'];
}
if ( $cart['cart']['billingAddress']['line1'] != '') {
    $billingAddress = $cart['cart']['billingAddress'];
}
if( isset( $cart['cart']['customAttributes']['attribute'] ) ) {
    foreach( $cart['cart']['customAttributes']['attribute'] as $attr ) {
        if ( 'companyEIN' == $attr['name'] ) {
            $companyEIN = $attr['value'];
            break;
        }
    }
}

?>
<div class="dr-checkout__billing dr-checkout__el">
    <div class="dr-accordion">

        <span class="dr-accordion__name">

            <span class="dr-accordion__title-long">

                <?php echo isset( $steps_titles['billing'] ) ? $steps_titles['billing'] : ''; ?>

            </span>

            <span class="dr-accordion__title-short">

                <?php echo __( 'Billing', 'digital-river-global-commerce' ); ?>

            </span>

        </span>

        <span class="dr-accordion__edit"><?php echo __( 'Edit', 'digital-river-global-commerce' ); ?>></span>

    </div>

    <form id="checkout-billing-form" class="dr-panel-edit dr-panel-edit--billing needs-validation" novalidate>

        <div class="form-group dr-panel-edit__check" <?php echo !$cart['cart']['hasPhysicalProduct'] ? 'style="display: none;"' : '' ?>>

            <p class="field-text">

                <?php echo __( 'What is your billing address?', 'digital-river-global-commerce' ); ?>

            </p>

            <div class="field-checkbox">

                <input type="checkbox" name="checkbox-billing" id="checkbox-billing" checked="checked">

                <label for="checkbox-billing" class="checkbox-label">

                    <?php echo __( 'Billing address is the same as delivery address', 'digital-river-global-commerce' ); ?>

                </label>

            </div>

        </div>

        <div class="billing-section" <?php echo !$cart['cart']['hasPhysicalProduct'] ? 'style="display: block;"' : '' ?>>

          <?php if ( 'en_US' === $current_locale ) : ?>
          <div class="form-group dr-panel-edit__el">

              <div class="field-checkbox">

                  <input type="checkbox" name="checkbox-business" id="checkbox-business" <?php echo $billingAddress['companyName'] ? 'checked="checked"' : '' ?> value="on">

                  <label for="checkbox-business" class="checkbox-label">

                      <?php echo __( 'Business Checkout' ); ?>

                  </label>

              </div>

          </div>
          <div class="form-group dr-panel-edit__el form-group-business<?php echo !$billingAddress['companyName'] ? ' hide' : '' ?>">

                <div class="float-container float-container--company-name">

                    <label for="billing-field-company-name" class="float-label">

                        <?php echo __( 'Company Name', 'digital-river-global-commerce' ); ?>

                    </label>

                    <input id="billing-field-company-name" type="text" name="billing-companyName" value="<?php echo $billingAddress['companyName'] ?>" class="form-control float-field float-field--company-name" >

                </div>

            </div>
            <div class="form-group dr-panel-edit__el form-group-business <?php echo !$billingAddress['companyName'] ? ' hide' : '' ?>">

                <div class="float-container float-container--company-ein">

                    <label for="billing-field-company-ein" class="float-label">

                        <?php echo __( 'Company EIN', 'digital-river-global-commerce' ); ?>

                    </label>

                    <input id="billing-field-company-ein" type="text" name="billing-companyEIN" value="<?php echo $companyEIN; ?>" class="form-control float-field float-field--company-ein" >

                </div>

            </div>
            <?php endif; ?>

            <div class="form-group dr-panel-edit__el">

                <div class="float-container float-container--first-name">

                    <label for="billing-field-first-name" class="float-label ">

                        <?php echo __( 'First Name', 'digital-river-global-commerce' ); ?> *

                    </label>

                    <input id="billing-field-first-name" type="text" name="billing-firstName" value="<?php echo $billingAddress['firstName'] ?>" class="form-control float-field float-field--first-name" required>

                    <div class="invalid-feedback">

                        <?php echo __( 'This field is required.', 'digital-river-global-commerce' ); ?>

                    </div>

                </div>

            </div>

            <div class="form-group dr-panel-edit__el">

                <div class="float-container float-container--last-name">

                    <label for="billing-field-last-name" class="float-label">

                        <?php echo __( 'Last Name', 'digital-river-global-commerce' ); ?> *

                    </label>

                    <input id="billing-field-last-name" type="text" name="billing-lastName" value="<?php echo $billingAddress['lastName'] ?>" class="form-control float-field float-field--last-name" required>

                    <div class="invalid-feedback">

                        <?php echo __( 'This field is required.', 'digital-river-global-commerce' ); ?>

                    </div>

                </div>

            </div>

            <div class="form-group dr-panel-edit__el">

                <div class="float-container float-container--address1">

                    <label for="billing-field-address1" class="float-label ">

                        <?php echo __( 'Address line 1', 'digital-river-global-commerce' ); ?> *

                    </label>

                    <input id="billing-field-address1" type="text" name="billing-line1" value="<?php echo $billingAddress['line1'] ?>" class="form-control float-field float-field--address1" required>

                    <div class="invalid-feedback">

                        <?php echo __( 'This field is required.', 'digital-river-global-commerce' ); ?>

                    </div>

                </div>

            </div>

            <div class="form-group dr-panel-edit__el">

                <div class="float-container float-container--address2">

                    <label for="billing-field-address2" class="float-label">

                        <?php echo __( 'Address line 2/Company', 'digital-river-global-commerce' ); ?>

                    </label>

                    <input id="billing-field-address2" type="text" name="billing-line2" value="<?php echo $billingAddress['line2'] ?>" class="form-control float-field float-field--address2" >

                </div>

            </div>

            <div class="form-group dr-panel-edit__el">

                <div class="float-container float-container--city">

                    <label for="billing-field-city" class="float-label">

                        <?php echo __( 'City', 'digital-river-global-commerce' ); ?> *

                    </label>

                    <input id="billing-field-city" type="text" name="billing-city" value="<?php echo $billingAddress['city'] ?>" class="form-control float-field float-field--city" required>

                    <div class="invalid-feedback">

                        <?php echo __( 'This field is required.', 'digital-river-global-commerce' ); ?>

                    </div>

                </div>

            </div>

            <div class="form-group dr-panel-edit__el">

                <select class="form-control custom-select" name="billing-country" id="billing-field-country" required>

                    <option value="">
                        <?php echo __( 'Select Country', 'digital-river-global-commerce' ); ?> *
                    </option>

                    <?php foreach ( $locales['locales'] as $locale => $currency ): ?>
                        <?php
                            $country = drgc_code_to_counry($locale);
                            $abrvCountyName = drgc_code_to_counry($locale, true);

                            $output = "<option ";
                            $output .= ($billingAddress['country'] === $abrvCountyName ? 'selected ' : '');
                            $output .= "value=\"{$abrvCountyName}\">{$country}</option>";
                            echo $output;
                        ?>
                    <?php endforeach; ?>

                </select>

                <div class="invalid-feedback">

                    <?php echo __( 'This field is required.', 'digital-river-global-commerce' ); ?>

                </div>

            </div>

            <div class="form-group dr-panel-edit__el d-none">

                <select class="form-control custom-select" name="billing-countrySubdivision" id="billing-field-state" required>

                    <option value="">
                        <?php echo __( 'Select State', 'digital-river-global-commerce' ); ?> *
                    </option>

                    <?php foreach ($usa_states as $key => $state): ?>
                        <?php
                            $option = "<option ";
                            $option .= $billingAddress['countrySubdivision'] === $key ? 'selected ' : '';
                            $option .= "value=\"{$key}\">{$state}</option>";
                            echo $option;
                        ?>
                    <?php endforeach; ?>

                </select>

                <div class="invalid-feedback">

                    <?php echo __( 'This field is required.', 'digital-river-global-commerce' ); ?>

                </div>

            </div>

            <div class="dr-panel-edit__el">

                <div class="float-container float-container--zip">

                    <label for="billing-field-zip" class="float-label">

                        <?php echo __( 'Zipcode', 'digital-river-global-commerce' ); ?> *

                    </label>

                    <input id="billing-field-zip" type="text" name="billing-postalCode" value="<?php echo $billingAddress['postalCode'] ?>" class="form-control float-field float-field--zip" required>

                    <div class="invalid-feedback">

                        <?php echo __( 'This field is required.', 'digital-river-global-commerce' ); ?>

                    </div>

                </div>

            </div>

            <div class="form-group dr-panel-edit__el">

                <div class="float-container float-container--phone">

                    <label for="billing-field-phone" class="float-label ">

                        <?php echo __( 'Phone', 'digital-river-global-commerce' ); ?>

                    </label>

                    <input id="billing-field-phone" type="text" name="billing-phoneNumber" value="<?php echo $billingAddress['phoneNumber'] ?>" class="form-control float-field float-field--phone" >

                </div>

            </div>

        </div>

        <div class="invalid-feedback dr-err-field" style="display: none"></div>

        <button type="submit" class="dr-panel-edit__btn dr-btn">

            <?php echo __( 'Save and continue', 'digital-river-global-commerce' ); ?>

        </button>

    </form>

    <div class="dr-panel-result">

        <p class="dr-panel-result__text"></p>

    </div>

</div>
