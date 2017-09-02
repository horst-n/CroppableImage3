/**
 * InputfieldCroppableImage3  - v 0.9.9
 *
 */

var croppableTooltip;

$(function () {

    'use strict';

    /**
     * Croppable image & warning tooltip
     * @var $elements, undefined/object, jquery collection object with list items
     *
     */
    croppableTooltip = function ($elements) {

            if ($elements === undefined) {
                $elements = $('.InputfieldImage .cropLinks a');
            }

            // hover image tooltip
            function showImage($link, json) {
                var $cropWrap = $("<div class='crop-border'></div>"),
                    timestamp = new Date().getTime();

                $cropWrap.append("<img src='" + $link.data('image') + "?t=" + timestamp + "'>");
                $cropWrap.append("<div class='cropInfo'>" + json.width + "×" + json.height + "</div>");
                return $cropWrap;
            }

            $elements.tooltip({
                items: 'a',
                content: function() {
                    var $link = $(this),
                        suffix = $link.data('suffix'),
                        json = config.CroppableImage3.crops[suffix];

                    // return content
                    if($link.data('croppable')) {
                        return showImage($link, json);
                    }

                    return "<div class='cropWarning'>" + $link.data('warning') + "</div>";
                },
                show: {
                    effect: 'fade',
                    delay: 330,
                    duration: 100
                },
                open: function(event, ui) {
                    if($(this).data('croppable')) {
                        //$(ui.tooltip).css({'min-width': $(this).data('width')});
                    } else {
                        $(ui.tooltip).addClass('warning');
                    }
                },
                tooltipClass: 'croppableTooltip',
                track: true,
                position: {
                    my: 'center bottom-25'
                }
            });
        };


    /* Croppable image tooltip */
    croppableTooltip();

});


function caiCloseReviewWindow() {
    var selector = "button.ui-button.ui-dialog-titlebar-close";
    $(selector).click();
    $('.InputfieldImage .cropLinks a').tooltip('disable');
    croppableTooltip();
    $('.InputfieldImage .cropLinks a').tooltip('enable');

    $('.InputfieldImage .cropLinks a[data-croppable] img').each(function() {

        var src = $(this).attr('src'),
            newSrc,
            dimensions = '.0x48';

        if(src.indexOf(dimensions) !== -1) {
            // if 0x48 present in the image src, remove it get the current cropped image
            // size may be large but it's already loaded by the browser
            // display size is controlled by CSS
            newSrc = src.replace(dimensions, '');
        } else {
            if(src.indexOf('?v=') === -1) {
                // there is no ?v= present, add ?v=1
                newSrc = src + '?v=1';
            } else {
                // replace ?v=1 with ?v=2, or vice versa
                newSrc = (src.indexOf('?v=1') !== -1) ? src.replace('?v=1', '?v=2') : src.replace('?v=2', '?v=1');
            }
        }

        $(this).attr('src', newSrc);
    });
}

