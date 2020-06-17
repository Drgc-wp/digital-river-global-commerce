<?php
/**
 * Provide a publidr-facing view for the plugin
 *
 * This file is used to markup the publidr-facing aspects of the plugin.
 *
 * @link       https://www.digitalriver.com
 * @since      1.0.0
 *
 * @package    Digital_River_Global_Commerce
 * @subpackage Digital_River_Global_Commerce/public/partials
 */
?>

<?php foreach ($customer_address as $key => $address): ?>

    <div class="col-12 col-lg-6 address-col">
        <address class="address" <?php if ($address['isDefault'] === 'true') echo 'data-primary="' . __( 'Primary', 'digital-river-global-commerce' ) . '"';?>>
            <div class="address-name">
                <span class="firstName"><?php echo $address['firstName'] ?></span>
                <span class="lastName"><?php echo $address['lastName']; ?></span>
            </div>

            <div class="address-company">
                <span class="companyName"><?php echo $address['companyName']; ?></span>
            </div>

            <div class="address-location">
                <div class="line1"><?php echo $address['line1']; ?></div>
                <div class="line2"><?php echo $address['line2']; ?></div>
                <div>
                    <span class="city'"><?php echo $address['city']; ?></span>,
                    <span class="countrySubdivision"><?php echo $address['countrySubdivision']; ?></span>
                    <span class="postalCode"><?php echo $address['postalCode']; ?></span>
                </div>
                <div class="phoneNumber"><?php echo $address['phoneNumber']; ?></div>
            </div>
            <div class="address-edit" style="display:none;">
                
                <form class="dr-panel-edit">

                    <input type="hidden" name="id" value="<?php echo $address['id'] ?>">

                    <div class="required-text">
                        <?php echo __( 'Fields marked with * are mandatory' ); ?>
                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--first-name">

                            <label for="first-name-<?php echo $key ?>" class="float-label ">

                                <?php echo __( 'First Name *' ); ?>

                            </label>

                            <input id="first-name-<?php echo $key ?>" type="text" value="<?php echo $address['firstName'] ?>" name="firstName" class="form-control float-field float-field--first-name" required>

                            <div class="invalid-feedback">

                                <?php echo __( 'This field is required.' ); ?>

                            </div>

                        </div>

                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--last-name">

                            <label for="last-name-<?php echo $key ?>" class="float-label ">

                                <?php echo __( 'Last Name *' ); ?>

                            </label>

                            <input id="last-name-<?php echo $key ?>" type="text" value="<?php echo $address['lastName'] ?>" name="lastName" class="form-control float-field float-field--last-name" required>

                            <div class="invalid-feedback">

                                <?php echo __( 'This field is required.' ); ?>

                            </div>

                        </div>

                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--company">

                            <label for="company-<?php echo $key ?>" class="float-label ">

                                <?php echo __( 'Company Name' ); ?>

                            </label>

                            <input id="company-<?php echo $key ?>" type="text" value="<?php echo $address['companyName'] ?>" name="companyName" class="form-control float-field float-field--company">

                            <div class="invalid-feedback">

                                <?php echo __( 'This field is required.' ); ?>

                            </div>

                        </div>

                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--address1">

                            <label for="address1-<?php echo $key ?>" class="float-label ">

                                <?php echo __( 'Address line 1 *' ); ?>

                            </label>

                            <input id="address1-<?php echo $key ?>" type="text" value="<?php echo $address['line1'] ?>" name="line1" class="form-control float-field float-field--address1" required>

                            <div class="invalid-feedback">

                                <?php echo __( 'This field is required.' ); ?>

                            </div>

                        </div>

                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--address2">

                            <label for="address2-<?php echo $key ?>" class="float-label">

                                <?php echo __( 'Address line 2' ); ?>

                            </label>

                            <input id="address2-<?php echo $key ?>" type="text" name="line2" value="<?php echo $address['line2'] ?>" class="form-control float-field float-field--address2">
                        
                        </div>

                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--city">

                            <label for="city-<?php echo $key ?>" class="float-label">

                                <?php echo __( 'City *' ); ?>
                                
                            </label>

                            <input id="city-<?php echo $key ?>" type="text" name="city" value="<?php echo $address['city'] ?>" class="form-control float-field float-field--city" required>

                            <div class="invalid-feedback">

                                <?php echo __( 'This field is required.' ); ?>

                            </div>
                        
                        </div>
                        
                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <select class="form-control custom-select" name="country" id="country-<?php echo $key ?>">
                            <option value="">
                                <?php echo __( 'Select Country *' ); ?>
                            </option>

                            <?php foreach ( $locales['locales'] as $locale => $currency ): ?>
                                <?php
                                    $country = drgc_code_to_counry($locale);
                                    $abrvCountyName = drgc_code_to_counry($locale, true);

                                    $output = "<option ";
                                    $output .= ($address['country'] === $abrvCountyName ? 'selected ' : '');
                                    $output .= "value=\"{$abrvCountyName}\">{$country}</option>";
                                    echo $output;
                                ?>
                            <?php endforeach; ?>
                        </select>

                        <div class="invalid-feedback">

                            <?php echo __( 'This field is required.' ); ?>

                        </div>

                    </div>


                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--state active">
                            <label for="state-<?php echo $key ?>" class="float-label">

                                <?php echo __( 'State *' ); ?>
                                
                            </label>

                            <select class="form-control custom-select" name="countrySubdivision" id="state-<?php echo $key ?>">

                                <option value="">
                                    <?php echo __( 'Select State *' ); ?>
                                </option>

                                <?php foreach ($usa_states as $key2 => $state): ?>
                                    <?php 
                                        $option = "<option ";
                                        $option .= $address['countrySubdivision'] === $key2 ? 'selected ' : '';
                                        $option .= "value=\"{$key2}\">{$state}</option>";
                                        echo $option;
                                    ?>
                                <?php endforeach; ?>

                            </select>

                            <div class="invalid-feedback">

                                <?php echo __( 'This field is required.' ); ?>

                            </div>

                        </div>

                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--zip">

                            <label for="zip-<?php echo $key ?>" class="float-label">

                                <?php echo __( 'Zipcode *' ); ?>

                            </label>

                            <input id="zip-<?php echo $key ?>" type="text" name="postalCode" value="<?php echo $address['postalCode'] ?>" class="form-control float-field float-field--zip" required>

                            <div class="invalid-feedback">

                                <?php echo __( 'This field is required.' ); ?>

                            </div>

                        </div>
                        
                    </div>

                    <div class="form-group dr-panel-edit__el">

                        <div class="float-container float-container--phone">

                            <label for="phone-<?php echo $key ?>" class="float-label">

                                <?php echo __( 'Phone' ); ?>

                            </label>
                            
                            <input id="phone-<?php echo $key ?>" type="text" name="phoneNumber" value="<?php echo $address['phoneNumber'] ?>" class="form-control float-field float-field--phone">
                        
                        </div>

                    </div>
                    
                    <div class="invalid-feedback dr-err-field" style="display: none"></div>

                    <input type="submit" class="dr-btn dr-btn-green" value="<?php echo __( 'Save', 'digital-river-global-commerce' );?>">

                </form>
                
            </div>
            <button class="address-edit-btn" role="img" aria-label="Edit Address" title="Edit Address"></button>
        </address>

    </div>

<?php endforeach; ?>