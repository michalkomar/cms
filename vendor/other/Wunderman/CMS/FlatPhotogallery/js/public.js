var MOBILE_MAX_WIDTH = 767;

$(function(){
    $('.carousel-flatPhotoGallery .carousel').carousel({
        interval: false
    });

    $('.carousel-flatPhotoGallery .carousel').each(function() {
        var galleryId = $(this).attr('id').replace(/carousel-flatPhotoGallery-([0-9]+)/, '$1');

        registerShowDetail(galleryId);
        registerCloseDetail(galleryId);
        updateCaptionText(galleryId);
        registerCarouselCaptionAction(galleryId);
        registerCarouselSlideActions(galleryId);
        registerCarouselDetailActions(galleryId);
        registerCarouselRecalculateOnResize(galleryId);
        registerIndicatorActions(galleryId);
        registerGallerySwipe(galleryId);

        $('#carousel-flatPhotoGallery-'+galleryId).find('.item img').ready(function(){
            // compute images proportions
            calculateImagesProportions(galleryId);
        });
    });

    /* gallery detail events and actions **/
});

$( window ).load(function(){

    $('.carousel-flatPhotoGallery .carousel').each(function() {
        var galleryId = $(this).attr('id').replace(/carousel-flatPhotoGallery-([0-9]+)/, '$1');

        calculateIndicatorInnerWidth(galleryId);
        // compute showing indicators arrows
        if ($('#carousel-flatPhotoGallery-indicators-'+galleryId+' ol.carousel-indicators').outerWidth() > $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .col-lg-12').width())
        {
            $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-control.right').fadeIn();
        }
    });
});
/**
 * Po kliknuti na lupu zobrazeni galerie
 * @param galleryId identifikator galerie
 */
function registerShowDetail(galleryId) {
    $('#carousel-flatPhotoGallery-'+galleryId+' .detail').on('click', function(event){
        showDetail(galleryId);
    });
    // detail is triggered by picture click in mobile view
    $('#carousel-flatPhotoGallery-'+galleryId+' .item img').on('click', function(event){
        if($(window).width() <= MOBILE_MAX_WIDTH) {
            var imageIndex = $(event.currentTarget).parent().index();
            $('#carousel-flatPhotoGallery-'+galleryId).parents(".carousel-flatPhotoGallery").carousel(imageIndex);
            carouselSlideHandler(galleryId);
            showDetail(galleryId);
        }
    });
}

function showDetail(galleryId) {
    showOverlay(galleryId);
    $("body").addClass("gallery-detail");
    $('#carousel-flatPhotoGallery-'+galleryId).parents('.carousel-flatPhotoGallery').addClass("show-detail");
    $('#carousel-flatPhotoGallery-'+galleryId).parents('.carousel-flatPhotoGallery').removeClass("show-basic");
    calculateImagesProportions(galleryId);
}

function registerCloseDetail(galleryId) {
    $('#carousel-flatPhotoGallery-'+galleryId+' .close').on('click', function(event){
        hideOverlay(galleryId);
        $("body").removeClass("gallery-detail");
        $(event.currentTarget).parents(".carousel-flatPhotoGallery").removeClass("show-detail");
        $(event.currentTarget).parents(".carousel-flatPhotoGallery").addClass("show-basic");
        calculateImagesProportions(galleryId);
        synchronizeIndicatorWithActivePicture(galleryId);
    });
}

function showOverlay(galleryId) {
    $('#carousel-flatPhotoGallery-overlay-'+galleryId).show();
}

function hideOverlay(galleryId) {
    $('#carousel-flatPhotoGallery-overlay-'+galleryId).hide();
}

/**
 * Zaregistruje udalost pro zvetsovani/zmensovani okna a pro pruchod sipkami doleva/doprava
 * @param galleryId
 */
function registerCarouselDetailActions(galleryId) {

    var CODE_LEFT_ARROW  = 37;
    var CODE_RIGHT_ARROW = 39;

    $("body").keydown(function(event) {
        if($('#carousel-flatPhotoGallery-'+galleryId).parents('.show-detail').length > 0) {
            if(event.keyCode == CODE_LEFT_ARROW) {
                goToPreviousPicture($('#carousel-flatPhotoGallery-'+galleryId));
            } else if(event.keyCode == CODE_RIGHT_ARROW) {
                goToNextPicture($('#carousel-flatPhotoGallery-'+galleryId));
            }
        }
    });
}

/** caption box in gallery **/

/**
 * Pri zobrazeni vybraneho obrazku bude prekopirovan carousel-caption na cilove misto mimo element
 * gallery-inner, aby byl v popredi. Pokud obrazek nema zadny carousel-caption, bude z ciloveho mista odebran stary
 * @param $item
 */
function updateCaptionText(galleryId) {
    var isMobile = $(window).width() <= MOBILE_MAX_WIDTH;
    var $item = $('#carousel-flatPhotoGallery-'+galleryId+' .item.active');
    $item.parents(".carousel").find(".carousel-caption-target").html("");
    if($item.find(".carousel-caption").length === 1) {
        $item.parents(".carousel").find(".carousel-caption-target").append($item.find(".carousel-caption").clone());
        //$item.parents(".carousel").find(".icon-info").addClass("enabled");
        if(isMobile) {
            $item.parents(".carousel").find(".icon-info").show();
            $item.parents(".carousel").find(".carousel-caption-target .carousel-caption").hide();
        } else {
            $item.parents(".carousel").find(".icon-info").hide();
        }
    } else {
        //$item.parents(".carousel").find(".icon-info").removeClass("enabled");
        $item.parents(".carousel").find(".icon-info").hide();
    }
    registerCarouselCaptionButtonClick(galleryId);
}

// caption show / hide
function registerCarouselCaptionAction(galleryId) {
    registerCarouselCaptionButtonClick(galleryId);
    registerInfoIconAction(galleryId);
}

function registerCarouselCaptionButtonClick(galleryId) {
    $('#carousel-flatPhotoGallery-'+galleryId+' .carousel-caption-target .hide-caption').on('click', function(){
        $(this).parents(".carousel-caption-target").find(".carousel-caption").fadeOut();
        $(this).parents(".carousel").find(".icon-info").fadeIn();
    });
}

function registerInfoIconAction(galleryId) {
    $('#carousel-flatPhotoGallery-'+galleryId).parents('.carousel-flatPhotoGallery').find('.icon-info').click(function(event) {
        var $carouselCaption = $(event.currentTarget).parent().find(".carousel-caption-target .carousel-caption");
        $(event.currentTarget).fadeOut();
        $carouselCaption.fadeIn();
    });
}

/** Thumbnails - indicators actions **/

function getActivePictureIndex(galleryId) {
    var pictureIndex = $('#carousel-flatPhotoGallery-'+galleryId+' .item.active').index();
    return pictureIndex;
}

function getActiveIndicatorIndex(galleryId) {
    var indicatorIndex = $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-indicators li.current').index();
    return indicatorIndex;
}

/**
 * Called when active picture changed by arrows or by thumbnail click
 */
function synchronizeIndicatorWithActivePicture(galleryId) {
    var activePictureIndex = getActivePictureIndex(galleryId);
    scrollToIndicator(galleryId, activePictureIndex);
}

/**
 * Update "current" classes and do movement
 */
function scrollToIndicator(galleryId, newIndicator) {
    var oldIndicator = getActiveIndicatorIndex(galleryId);
    $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-indicators li[data-slide-to='+oldIndicator+']').removeClass("current");
    $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-indicators li[data-slide-to='+newIndicator+']').addClass("current");
    doMovementToIndicator(galleryId, newIndicator);
}

/**
 * Move thumbnails to next thumbnail
 */
function scrollToNextIndicator(galleryId) {
    var activeIndicator = getActiveIndicatorIndex(galleryId);
    if(!isLastIndicator(galleryId, activeIndicator)) {
        scrollToIndicator(galleryId, activeIndicator + 1);
    };
}

/**
 * Move thumbnails to previous thumbnail
 */
function scrollToPrevIndicator(galleryId) {
    var activeIndicator = getActiveIndicatorIndex(galleryId);
    if(!isFirstIndicator(galleryId, activeIndicator)) {
        scrollToIndicator(galleryId, activeIndicator - 1);
    };
}

function isFirstIndicator(galleryId, indicatorIndex) {
    return indicatorIndex == 0;
}

function isLastIndicator(galleryId, indicatorIndex) {
    return indicatorIndex == $('#carousel-flatPhotoGallery-'+galleryId+' .carousel-indicators li').length - 1;
}

/**
 * DO physically movement of thumbnail bar
 */
function doMovementToIndicator(galleryId, indicatorIndex) {
    var thumbnailBar = $('#carousel-flatPhotoGallery-indicators-'+galleryId);
    var thumbnailList = $('#carousel-flatPhotoGallery-indicators-'+galleryId+' ol');
    var newThumbnail = thumbnailList.find('li[data-slide-to='+indicatorIndex+']');
    var thumbnailBarWidth = thumbnailBar.width();
    var thumbnailListWidth = thumbnailList.width();

    var activeIndicatorOffset = Math.abs(thumbnailList.offset().left - newThumbnail.offset().left);
    var newOffset = 0;
    var hideLeftArrow = indicatorIndex == 0;
    var hideRightArrow;

    if(thumbnailListWidth > thumbnailBarWidth) {
        // picture list width is smaller than thumbnail bar width
        if(activeIndicatorOffset + thumbnailBarWidth >= thumbnailListWidth) {
            //reached right border - disable "right arrow"
            newOffset = thumbnailListWidth - thumbnailBarWidth;
            hideRightArrow = true;
        } else {
            //not reached right border - enable "right arrow"
            newOffset = activeIndicatorOffset;
            hideRightArrow = false;
        }
        //Update arrows visibility
        if(hideLeftArrow) {
            thumbnailBar.find(".carousel-control.left").fadeOut();
        } else {
            thumbnailBar.find(".carousel-control.left").fadeIn();
        }
        if(hideRightArrow) {
            thumbnailBar.find(".carousel-control.right").fadeOut();
        } else {
            thumbnailBar.find(".carousel-control.right").fadeIn();
        }
    } else {
        // picture list width is larger than thumbnail bar width
        newOffset = 0;
        thumbnailBar.find(".carousel-control.left").fadeOut();
        thumbnailBar.find(".carousel-control.right").fadeOut();
    }

    //Do animation
    thumbnailList.css('-ms-transform', 'translate('+(-newOffset)+'px, 0px)');
    thumbnailList.css('-webkit-transform', 'translate('+(-newOffset)+'px, 0px)');
    thumbnailList.css('-moz-transform', 'translate('+(-newOffset)+'px, 0p)x');
    thumbnailList.css('-o-transform', 'translate('+(-newOffset)+'px, 0px)');
    thumbnailList.css('transform', 'translate('+(-newOffset)+'px, 0px)');
    if(IE(9)) thumbnailList.css('cssText','-ms-transform: translate('+(-newOffset)+'px, 0px); width: '+thumbnailListWidth+'px');
    //thumbnailList.animate({ left: -newOffset });

}

// thumbnail bar - arrow right click event
function registerIndicatorActions(galleryId) {
    $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-control.right').on('click', function(event) {
        event.preventDefault();
        scrollToNextIndicator(galleryId);
    });

    //thumbnail bar - arrow left click event
    $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-control.left').on('click', function(event) {
        event.preventDefault();
        scrollToPrevIndicator(galleryId);
    });
}

/** slide actions - slide by tapping on 1/3 of picture, update caption text and thumbnail bar on slide **/

function registerCarouselSlideActions(galleryId) {
    $('#carousel-flatPhotoGallery-'+galleryId+' .carousel-control-overlay').click(function(event) {
        var move = $(event.currentTarget).is(".left") ? "prev" : "next";
        doStep($(event.currentTarget).parents(".carousel"), move);
    });

    $('#carousel-flatPhotoGallery-'+galleryId+' .carousel-control-overlay .carousel-control').click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        var move = $(event.currentTarget).is(".left") ? "prev" : "next";
        doStep($(event.currentTarget).parents(".carousel"), move);
    });

    $('#carousel-flatPhotoGallery-'+galleryId).on('slid.bs.carousel', function () {
        carouselSlideHandler(galleryId);
        synchronizeIndicatorWithActivePicture(galleryId);
    });
}

function carouselSlideHandler(galleryId) {
    updateCaptionText(galleryId);
}

function goToPreviousPicture($carousel) {
    doStep($carousel, "prev");
}

function goToNextPicture($carousel) {
    doStep($carousel, "next");
}

function doStep($carousel, move) {
    $carousel.carousel(move);
    $carousel.carousel('pause');
}

function calculateImagesProportions(galleryId) {
    var isMobile = $(window).width() <= MOBILE_MAX_WIDTH;
    var isDetail = $('#carousel-flatPhotoGallery-'+galleryId).parents(".carousel-flatPhotoGallery").is(".show-detail");
    if(isMobile && !isDetail) { //count item container height (width set to 45%)
        $('#carousel-flatPhotoGallery-'+galleryId+' .item').each(function() {
            $(this).css("height", Math.ceil($(this).width() * 3 / 4) + 'px');
        });
    } else {
        $('#carousel-flatPhotoGallery-'+galleryId+' .item').each(function() {
            $(this).css("height", '');
        });
    }

    $.each($('#carousel-flatPhotoGallery-'+galleryId).find('.item img'), function(index, img) {
        var item = $(img).closest('.item');
        var containerWidth = isMobile && !isDetail ? $(item).width() : $(item).parent().width();
        var containerHeight = isMobile && !isDetail ? $(item).height() : $(item).parent().height();
        var imgRealWidth, imgRealHeight, newPosition;

        $("<img/>") // Make in memory copy of image to avoid css issues
            .attr("src", $(img).attr("src"))
            .load(function() {
                imgRealWidth = this.width;
                imgRealHeight = this.height;
                var imgComputedWidth;
                var imgComputedHeight;
                if ((imgRealWidth / imgRealHeight) > (containerWidth / containerHeight))
                {
                    //if it is proportionally wider - fix width to container width and vertical center
                    imgComputedWidth = Math.round(Math.min(imgRealWidth, containerWidth));
                    imgComputedHeight = Math.round(imgRealHeight * imgComputedWidth / imgRealWidth);
                    $(img).css({ width: imgComputedWidth, height: imgComputedHeight});
                }
                else
                {
                    //if it is proportionally narrower - fix height to container height (centering is done by css)
                    imgComputedHeight = Math.round(Math.min(imgRealHeight, containerHeight));
                    imgComputedWidth = Math.round(imgRealWidth * imgComputedHeight / imgRealHeight);
                    $(img).css({ width: imgComputedWidth, height: imgComputedHeight});
                }
                // vertical center of img
                if (imgComputedHeight < containerHeight)
                {
                    newPosition = Math.round((containerHeight - imgComputedHeight) / 2);
                    $(img).css({ top: newPosition});
                } else {
                    newPosition = 0;
                    $(img).css({ top: newPosition});
                }
            });
    });
}

function registerCarouselRecalculateOnResize(galleryId) {
    $(window).resize(function(event) {
        calculateImagesProportions(galleryId);
    });

}

function registerGallerySwipe(galleryId) {
    var isMobile = $(window).width() <= 1024;
    if(swipeFunc && isMobile) {
        var swipeLeftCallback = function() {
            if($("body.gallery-detail").length) {
                var $carousel = $('#carousel-flatPhotoGallery-'+galleryId).parents('.container.show-detail');
                if($carousel.length) {
                    doStep($carousel, 'next');
                }
            }
        }
        var swipeRightCallback = function() {
            if($("body.gallery-detail").length) {
                var $carousel = $('#carousel-flatPhotoGallery-'+galleryId).parents('.container.show-detail');
                if($carousel.length) {
                    doStep($carousel, 'prev');
                }
            }
        }
        swipeFunc.init(swipeLeftCallback, swipeRightCallback);
    }

}



function calculateIndicatorInnerWidth(galleryId) {
    var $galleryThumbnailItems = $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-indicators li');
    var padding = parseInt($galleryThumbnailItems.css("padding-right"));
    var width = padding * ($galleryThumbnailItems.length - 1);
    $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-indicators li').each(function() {
        width += $(this).width();
    });
    $('#carousel-flatPhotoGallery-indicators-'+galleryId+' .carousel-indicators').css("width", width+'px');
}

function IE(v) {
    if(v === 11) {
        return (!!navigator.userAgent.match(/Trident\/7\./));
    } else {
        return RegExp('msie' + (!isNaN(v)?('\\s'+v):''), 'i').test(navigator.userAgent);
    }
}
