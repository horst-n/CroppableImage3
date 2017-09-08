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
                items: 'a:hover',
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
                hide: false,
                position: {
                    my: 'center bottom-25',
                    collision: 'none'
                }
            });
        };


    /* Croppable image tooltip */
    croppableTooltip();

});


function caiCloseReviewWindow(imgUrl) {
    var selector = "button.ui-button.ui-dialog-titlebar-close",
        $cropLinks = $('.InputfieldImage .cropLinks a');

    $(selector).click();

    if (!$cropLinks.data('ui-tooltip')) {
        $cropLinks.tooltip('disable');
    }

    croppableTooltip();
    $cropLinks.tooltip('enable');

    $('.InputfieldImage .cropLinks a[data-croppable] img').each(function() {

        // update only current image
        if(imgUrl.indexOf($(this).parent('a').attr('data-image')) === -1) {
            return true;    // continue
        } else {
            var src = $(this).attr('src'),
                newSrc,
                dimensions = '.0x48';
        }

        if(src.indexOf(dimensions) !== -1) {
            newSrc = imgUrl;
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
