<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.digitalriver.com
 * @since      1.0.0
 *
 * @package    Digital_River_Global_Commerce
 * @subpackage Digital_River_Global_Commerce/public/templates/parts
 */

$product_image_url = get_post_meta( get_the_ID(), 'gc_product_images_url', true );
$product_thumbnail_url = get_post_meta( get_the_ID(), 'gc_thumbnail_url', true );
$short_description = get_post_meta( get_the_ID(), 'short_description', true );
$long_description = get_post_meta( get_the_ID(), 'long_description', true );

$gc_id = get_post_meta( get_the_ID(), 'gc_product_id', true );
$purchasable = get_post_meta( get_the_ID(), 'purchasable', true );

$variations = drgc_get_product_variations( get_the_ID() );
if ( $variations && isset( $variations[0] ) ) {
    $all_variation_attributes = get_post_meta( get_the_ID(), 'variations', true );
    $var_attributes_names = get_post_meta( get_the_ID(), 'var_attribute_names', true );
    $var_select_options = get_post_meta( get_the_ID(), 'var_select_options', true );
} else {
	$pricing = drgc_get_product_pricing( get_the_ID() );

}
$list_price_value = isset( $pricing['list_price_value'] ) ? $pricing['list_price_value'] : '';
$sale_price_value = isset( $pricing['sale_price_value'] ) ? $pricing['sale_price_value'] : '';
$price = isset( $pricing['price'] ) ? $pricing['price'] : '';
$regular_price = isset( $pricing['regular_price'] ) ? $pricing['regular_price'] : '';
?>
<script type="text/javascript">
    var drgcVarAttrs = <?php echo json_encode( $all_variation_attributes, JSON_FORCE_OBJECT ); ?>;
</script>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="row">
        <div class="col-12 col-md-6">
            <img src="<?php echo $product_thumbnail_url ?: $product_image_url ?>" alt="<?php the_title_attribute() ?>" class="dr-pd-img" />
        </div>

        <div class="col-12 col-md-6">
		    <?php the_title( '<h1 class="entry-title dr-pd-title">', '</h1>' ); ?>
            <div class="dr-pd-content">
			    <?php if ( $short_description ) { ?>
                    <p class="dr-pd-short-desc"><?php echo $short_description; ?></p>
			    <?php } ?>
			    <?php the_content(); ?>

                <?php if ( $variations ): ?>

                    <?php if ( isset( $var_select_options ) && is_array( $var_select_options ) ): ?>

                        <h6><?php echo __( 'Select your product', 'digital-river-global-commerce'); ?>:</h6>

                        <?php
                            $index = 0;
                            foreach ( array_keys( $var_select_options ) as $label ) { 
                                $key = array_search( $label, $var_attributes_names );
                        ?>

                            <div class="dr-prod-variations">

                                <select name="dr-variation-<?php echo $key; ?>" data-var-attribute="<?php echo $key; ?>" data-index="<?php echo $index; ?>" disabled>
                                    <option value=""><?php echo ( $lang === 'en' ) ? ucwords( $label ) : $label; ?></option>
                                    <?php foreach ( $var_select_options[ $label ] as $value ): ?>
                                        <option value="<?php echo $value; ?>"><?php echo ( $lang === 'en' ) ? ucwords( $value ) : $value; ?></option>
                                    <?php endforeach; ?>

                                </select>

                            </div>

                        <?php 
                                $index++;
                            } 
                        ?>

                    <?php endif; ?>

                <?php endif; ?>

                <form id="dr-pd-form">
                    <div class="dr-pd-price-wrapper" id="dr-pd-price-wrapper">

					    <?php if ( (float) $list_price_value > (float) $sale_price_value ) : ?>
                            <p class="dr-pd-price">
                                <del class="dr-strike-price"><?php echo $list_price_value; ?></del>
                                <strong class="dr-sale-price"><?php echo $price; ?></strong>
                            </p>
					    <?php else: ?>
                            <p class="dr-pd-price">
                                <strong class="dr-sale-price"><?php echo $price; ?></strong>
                            </p>
					    <?php endif; ?>

                    </div>
                    <div class="dr-pd-qty-wrapper">
                    <span class="dr-pd-qty-minus" style="background-image: url('<?php echo get_site_url(); ?>/wp-content/plugins/digital-river-global-commerce/assets/images/product-minus.svg');" >
                    </span>
                        <input type="number" class="dr-pd-qty no-spinners" id="dr-pd-qty" step="1" min="1" max="999" value="1" maxlength="5" size="2" pattern="[0-9]*" inputmode="numeric" readonly />
                        <span class="dr-pd-qty-plus"  style="background-image: url('<?php echo DRGC_PLUGIN_URL; ?>assets/images/icons-plus.svg');" >
                    </span>
                    </div>
                    <p>
                        <button type="button" class="dr-btn dr-buy-btn" data-product-id="<?php echo $gc_id; ?>" <?php echo 'true' !== $purchasable ? 'disabled' : ''; ?> >
						    <?php echo __( 'Add to Cart', 'digital-river-global-commerce'); ?>
                        </button>
                    </p>
                </form>
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col">
	        <?php if ( $long_description ) { ?>
                <section class="dr-pd-info">
                    <div class="dr-pd-long-desc">
				        <?php echo $long_description; ?>
                    </div>
                </section>
	        <?php } ?>
        </div>
        <section id="dr-pd-offers"></section>
    </div>

</div>
