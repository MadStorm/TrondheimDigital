<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_camera_slideshow
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_BASE.'/components/com_content/helpers');
$document = JFactory::getDocument();
?>
<div class="owl-carousel owl-carousel_<?php echo $module->id; ?> mod_owl_carousel mod_owl_carousel__<?php echo $moduleclass_sfx; ?>" id="module_<?php echo $module->id; ?>"
data-margin="<?php if($params->get('items_padding_0')!=0){
	$res_0 = (int)$params->get('items_padding_0');
	echo $res_0+1;
} else {
	echo $params->get('items_padding_0');
}
?>"
data-xs-margin="<?php if($params->get('items_padding_480')!=0){
	$res_480 = (int)$params->get('items_padding_480');
	echo $res_480+1;
} else {
	echo $params->get('items_padding_480');
}
?>"
data-sm-margin="<?php if($params->get('items_padding_768')!=0){
	$res_768 = (int)$params->get('items_padding_768');
	echo $res_768+1;
} else {
	echo $params->get('items_padding_768');
}
?>"
data-md-margin="<?php if($params->get('items_padding_992')!=0){
	$res_992 = (int)$params->get('items_padding_992');
	echo $res_992+1;
} else {
	echo $params->get('items_padding_992');
}
?>"
data-lg-margin="<?php if($params->get('items_padding_1200')!=0){
	$res_1200 = (int)$params->get('items_padding_1200');
	echo $res_1200+1;
} else {
	echo $params->get('items_padding_1200');
}
?>"
data-stage-padding="<?php echo $params->get('scene_padding_0')?>"
<?php if($params->get('use_responsive')==1):?>
data-xs-stage-padding="<?php echo $params->get('scene_padding_480')?>"
data-sm-stage-padding="<?php echo $params->get('scene_padding_768')?>"
data-md-stage-padding="<?php echo $params->get('scene_padding_992')?>"
data-lg-stage-padding="<?php echo $params->get('scene_padding_1200')?>"
<?php endif; ?>

data-autoplay="<?php if ($params->get('autoplay')): ?>true<?php else: ?>false<?php endif;?>"

data-nav="<?php if ($params->get('navigation')): ?>true<?php else: ?>false<?php endif;?>"
data-dots="<?php if ($params->get('pagination')): ?>true<?php else: ?>false<?php endif;?>"
data-lg-items="<?php if($params->get('items')<1):?>1<?php else: ?><?php echo $params->get('items')?><?php endif;?>"
data-sm-items="<?php if($params->get('items')==1):?>1<?php else: ?>3<?php endif;?>"
data-xs-items="<?php if($params->get('items')==1):?>1<?php else: ?>2<?php endif;?>"
data-items="1"
data-loop="true"
>
	<?php
	foreach ($list as $key => $item) : ?>
		<div class="owl-item" id="item_<?php echo $item->id; ?>">
			<div class="owl-item_content"><?php require JModuleHelper::getLayoutPath('mod_owl_carousel', '_item'); ?></div>
		</div>	
	<?php endforeach;	?>
</div>
<?php
	if($params->get('autoplay')==1){
		$autoplay = $params->get('autoplay_speed');
		$autoplay_opt = $autoplay;
		if($autoplay!=5000){
			$autoplay_opt = $params->get('autoplay_speed');
		}
	} else {
		$autoplay_opt = 5000;
	}
	$script='
	(function ($) {
	    $(document).ready(function(){
	    	var o = $(".owl-carousel_'.$module->id.'");
	    	    if (o.length) {
	    	        var isTouch = "ontouchstart" in window;
	    
	    	        function preventScroll(e) {
	    	            e.preventDefault();
	    	        }
	    
	    	        $(document).ready(function () {
	    	            o.each(function () {
	    	                var c = $(this), responsive = {};
	    	                var aliaces = ["-", "-xs-", "-sm-", "-md-", "-lg-"], values = [0, 480, 768, 992, 1200], i, j;
	    	                for (i = 0; i < values.length; i++) {
	    	                    responsive[values[i]] = {};
	    	                    for (j = i; j >= -1; j--) {
	    	                        if (!responsive[values[i]]["items"] && c.attr("data" + aliaces[j] + "items")) {
	    	                            responsive[values[i]]["items"] = j < 0 ? 1 : parseInt(c.attr("data" + aliaces[j] + "items"));
	    	                        }
	    	                        if (!responsive[values[i]]["stagePadding"] && responsive[values[i]]["stagePadding"] !== 0 && c.attr("data" + aliaces[j] + "stage-padding")) {
	    	                            responsive[values[i]]["stagePadding"] = j < 0 ? 0 : parseInt(c.attr("data" + aliaces[j] + "stage-padding"));
	    	                        }
	    	                        if (!responsive[values[i]]["margin"] && responsive[values[i]]["margin"] !== 0 && c.attr("data" + aliaces[j] + "margin")) {
	    	                            responsive[values[i]]["margin"] = j < 0 ? 30 : parseInt(c.attr("data" + aliaces[j] + "margin"));
	    	                        }
	    	                    }
	    	                }
	    	                c.owlCarousel({
	    	                    autoplay: c.attr("data-autoplay") === "true",
	    	                    autoplayTimeout: '.$autoplay_opt.',
	    	                    loop: c.attr("data-loop") !== "false",
	    	                    nav: c.attr("data-nav") === "true",
	    	                    dots: c.attr("data-dots") === "true",
	    	                    dotsEach: c.attr("data-dots-each") ? parseInt(c.attr("data-dots-each")) : false,
	    	                    responsive: responsive,
	    	                    navText: [],
	    	                    onInitialized: function () {
	    	                        if ($.fn.magnificPopup) {
	    	                            var o = this.$element.find("[data-lightbox]").not("[data-lightbox=\'gallery\'] [data-lightbox]"), g = this.$element.find("[data-lightbox^=\'gallery\']");
	    	                            if (o.length) {
	    	                                o.each(function () {
	    	                                    var $this = $(this);
	    	                                    $this.magnificPopup({
	    	                                        type: $this.attr("data-lightbox"),
	    	                                        callbacks: {
	    	                                            open: function () {
	    	                                                if (isTouch) {
	    	                                                    $(document).on("touchmove", preventScroll);
	    	                                                    $(document).swipe({
	    	                                                        swipeDown: function () {
	    	                                                            $.magnificPopup.close();
	    	                                                        }
	    	                                                    });
	    	                                                }
	    	                                            }, close: function () {
	    	                                                if (isTouch) {
	    	                                                    $(document).off("touchmove", preventScroll);
	    	                                                    $(document).swipe("destroy");
	    	                                                }
	    	                                            }
	    	                                        }
	    	                                    });
	    	                                })
	    	                            }
	    	                            if (g.length) {
	    	                                g.each(function () {
	    	                                    var $gallery = $(this);
	    	                                    $gallery.find("[data-lightbox]").each(function () {
	    	                                        var $item = $(this);
	    	                                        $item.addClass("mfp-" + $item.attr("data-lightbox"));
	    	                                    }).end().magnificPopup({
	    	                                        delegate: "[data-lightbox]",
	    	                                        type: "image",
	    	                                        gallery: {enabled: true},
	    	                                        callbacks: {
	    	                                            open: function () {
	    	                                                if (isTouch) {
	    	                                                    $(document).on("touchmove", preventScroll);
	    	                                                    $(document).swipe({
	    	                                                        swipeDown: function () {
	    	                                                            $.magnificPopup.close();
	    	                                                        }
	    	                                                    });
	    	                                                }
	    	                                            }, close: function () {
	    	                                                if (isTouch) {
	    	                                                    $(document).off("touchmove", preventScroll);
	    	                                                    $(document).swipe("destroy");
	    	                                                }
	    	                                            }
	    	                                        }
	    	                                    });
	    	                                })
	    	                            }
	    	                        }
	    	                    }
	    	                });
	    	            });
	    	        });
	    	    }
	    	});
	})(jQuery);';
$document->addScriptdeclaration($script);
?>

