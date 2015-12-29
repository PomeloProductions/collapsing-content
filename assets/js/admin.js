/**
 * Created by bryce on 12/29/15.
 */
jQuery("document").ready(function() {

    jQuery("#using_post-checkbox").change(function() {
        if (jQuery(this).is(":checked")) {
            jQuery("#post-select-container").show();
            jQuery(".edit-above-entries").hide();
            jQuery(".edit-below-entries").hide()
        } else {
            jQuery("#post-select-container").hide();
            jQuery(".edit-above-entries").show();
            jQuery(".edit-below-entries").show();
        }
    })
});