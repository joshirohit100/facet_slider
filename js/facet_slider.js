/**
 * @file
 */

(function ($) {

    "use strict";

    Drupal.behaviors.facetsSlider = {
        attach: function (context, settings) {
            $('input[type="range"]').on("input", function(){
                var parent = $(this).parents('.facet-slider-facet')[0];
                if (parent) {
                    $(parent).find('.facet-slider-val').html(this.value);
                }
            });
        }
    };
})(jQuery);
