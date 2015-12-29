/**
 * Created by bryce on 12/29/15.
 */
jQuery("document").ready(function() {

    function toggleUsingPost() {

        if (jQuery("#using_post-checkbox").is(":checked")) {
            jQuery("#post-select-container").show();
            jQuery(".edit-above-entries").hide();
            jQuery(".edit-below-entries").hide()
        } else {
            jQuery("#post-select-container").hide();
            jQuery(".edit-above-entries").show();
            jQuery(".edit-below-entries").show();
        }
    }

    jQuery("#using_post-checkbox").change(toggleUsingPost);

    jQuery("#template-select").change(function() {
        if (jQuery(this).val() == "nested") {
            jQuery("#using_post-container").hide();
            jQuery("#post-select-container").hide();
            jQuery(".edit-above-entries").hide();
            jQuery(".edit-below-entries").hide();
        }
        else {

            jQuery("#using_post-container").show();

            toggleUsingPost();
        }
    });
});