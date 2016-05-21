(function ($) {

    Drupal.behaviors.facet_slider = {
        attach: function(context, settings) {
            // Iterates over facets, applies slider widgets for block realm facets.
            $.each($('.facet-slider-facet form'), function(index, form) {
                var form_id = $(form)[0].id;
                var facet_id = form_id.replace("facets-slider-widget-", "field_").replace('-', '_');
                $('#' + form_id + ' .facet-slider-slider').slider({
                    min: 0,
                    max: settings.facet_slider[facet_id].max,
                    step: settings.facet_slider[facet_id].step,
                    slide: function (event, ui) {
                        ui.value;
                    },
                    stop: function(event, ui) {
                        $(form).submit();
                    },
                });
            });
        }
    }

})(jQuery);
