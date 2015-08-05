jQuery(document).ready(function ($) {
    'use strict';

    var jsl = {};

    jsl.activated_plugin = (function () {
        var button = $('#activated_plugin'),
            rows = $('.jsl-wrapper tr');

        button.on('click', function (e) {
            e.preventDefault();

            rows.toggle();

            rows.each(function () {
                if ($(this).find('label[title="enable"] input')
                        .attr('checked')) {
                    $(this).show();
                }
            });

        });

    }());

    jsl.find_plugin = (function () {
        var input = $('#plugin_filter'),
            rows = $('.jsl-wrapper tr');

        input.on('keyup', function () {
            var currentInput = input.val().toLowerCase();

            rows.hide();

            rows.each(function () {
                var text = $(this).text().toLowerCase(),
                    ilen = input.val().length,
                    i;

                for (i = 0; i <= ilen; i += 1) {
                    if (text.indexOf(currentInput) !== -1) {
                        $(this).show();
                    }
                }

            });

        });
    }());

});
