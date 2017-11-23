<?php defined('_JEXEC') or die;
include_once('includes/includes.php'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>">
    <head>
        <?php echo $viewport; ?>
        <jdoc:include type="head"/>
    </head>
    <body class="<?php echo $bodyClass; ?>">
        <?php echo $ie_warning; ?>
        <!-- Body -->
        <div id="wrapper">
            <div class="wrapper-inner">
                <a id="fake" href='#'></a>
                <!-- Top -->
                <?php 
                $layout = $params->get('top_layout','normal');
                $style = 'html5nosize';
                $class = '';
                if($layout == "normal"){
                    $style = 'themeHtml5';
                    $class = ' class="col-sm-'.$this->params->get('logoBlockWidth').'"';
                }
                $top_html = '<!-- Logo -->
                            <div id="logo"'.$class.'>
                                <a href="'.JURI::base().'">
                                    ';
                if(isset($logo)){
                $top_html.= '<img src="'.$logo.'" alt="'.$sitename.'">';
                }                    
                $top_html.= '
                                    <span class="site-logo">'.wrap_with_span($sitename).'</span>
                                </a>
                                ';
                if($this->params->get('LogoDescription')){                
                    $top_html.= '<div class="site-description">'.$this->params->get("logo_description_title").'</div>';
                }
                $top_html.= '</div>
                            <jdoc:include type="modules" name="top" style="'.$style.'"/>';
                echo display_position('top',$top_html); ?>

                <?php if($this->countModules('breadcrumbs')){
                    echo display_position('breadcrumbs');
                } ?>

                <?php if(!$hideByView){
                    if($this->countModules('header')){
                        echo display_position('header');
                    }
                    if($this->countModules('navigation')){
                        echo display_position('navigation');
                    }
                    if($this->countModules('showcase')){
                        echo display_position('showcase');
                    }
                    if($this->countModules('feature')){
                        echo display_position('feature');
                    }
                    if($this->countModules('maintop')){
                        echo display_position('maintop');
                    }
                } ?>
                <!-- Main Content row -->
                <div id="content">
                    <?php if($this->countModules('map') && !$hideByView){
                        echo display_position('map');
                    }
                    $layout = $params->get('content_layout');
                    if($layout != "fullwidth"){ ?>
                    <div class="container<?php if($layout == "fluid") echo '-fluid'; ?>">
                        <div class="row">
                    <?php } ?>
                            <div class="content-inner">
                                <!-- Left sidebar -->
                                <?php if($this->countModules('aside-left') && !$hideByOption && $view !== 'form'){
                                    echo display_position('aside-left', null, 'fullwidth', $asideLeftClass);
                                } ?>
                                
                                <div id="component"<?php echo $mainContentClass; ?>>
                                    <main role="main">
                                        <?php if($this->countModules('content-top') && !$hideByView){
                                            echo display_position('content-top', null, 'content');
                                        } ?>
                                        <jdoc:include type="message"/>
                                        <jdoc:include type="component"/>
                                        <?php if($this->countModules('content-bottom') && !$hideByOption && $view !== 'form'){
                                            echo display_position('content-bottom', null, 'content');
                                        } ?>
                                    </main>
                                </div>
                                <?php if($this->countModules('aside-right') && !$hideByOption && $view !== 'form'){
                                    echo display_position('aside-right', null, 'fullwidth', $asideRightClass);
                                } ?>
                            </div>
                            <?php if($layout != "fullwidth"){ ?>
                        </div>
                    </div>
                            <?php } ?>
                </div>
                <?php if(!$hideByView){
                    if($this->countModules('video')){
                        echo display_position('video');
                    }
                    if($this->countModules('mainbottom')){
                        echo display_position('mainbottom');
                    }
                    if($this->countModules('mainbottom-2')){
                        echo display_position('mainbottom-2');
                    }
                    if($this->countModules('mainbottom-3')){
                        echo display_position('mainbottom-3');
                    }
                    if($this->countModules('mainbottom-4')){
                        echo display_position('mainbottom-4');
                    }
                    if($this->countModules('mainbottom-5')){
                        echo display_position('mainbottom-5');
                    }
                    if($this->countModules('bottom')){
                        echo display_position('bottom');
                    }
                } ?>
            </div>
        </div>
        <div id="footer-wrapper">
            <div class="footer-wrapper-inner">
                <?php if(!$hideByView){
                    if($this->countModules('footer')){
                        echo display_position('footer');
                    }
                } ?>
                <!-- Copyright -->
                <div id="copyright" role="contentinfo">
                    <?php $layout = $params->get('copyright_layout');
                    if($layout == "normal"){
                        global $containerClass;
                        echo "<div class=\"container\">";
                    }
                    else{
                        echo "<div class=\"container-fluid\">";
                    } ?>
                        <div class="row">
                            <jdoc:include type="modules" name="copyright" style="themeHtml5"/>
                            <div class="copyright col-sm-<?php echo $this->params->get('footerWidth'); ?>">
                                <?php if($this->params->get('footerLogo')){ ?>
                                <!-- Footer Logo -->
                                <a class="footer_logo" href="<?php echo $this->baseurl; ?>">
                                    <img src="<?php echo $footerLogo; ?>" alt="<?php echo $sitename; ?>">
                                </a>
                                <?php }else{ ?>
                                <span class="siteName"><?php echo $sitename; ?></span>
                                <?php }
                                if($this->params->get('footerCopy')) echo '<span class="copy">&copy; </span>';
                                if($this->params->get('footerYear')) echo '<span class="year">'.date('Y').'</span>';
                                if($this->params->get('privacyLink')){ ?>
                                <a class="privacy_link" rel="license" href="<?php echo $privacy_link_url; ?>">
                                    <?php echo $this->params->get('privacy_link_title'); ?>
                                </a>
                                <?php }
                                if($this->params->get('termsLink')){ ?>
                                <a class="terms_link" href="<?php echo $terms_link_url; ?>">
                                    <?php echo $this->params->get('terms_link_title'); ?>
                                </a>
                                <?php } ?>
                            </div>
                            <?php echo $todesktop; ?>
                            More <a  rel='nofollow' href='http://www.templatemonster.com/category.php?category=346&type=24' target='_blank'>Software Company Templates at TemplateMonster.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $back_top;
        if($this->countModules('modal')){
        $doc->addScriptdeclaration('
            ;(function($){
                $(document).ready(function(){
                    var o=$(\'a[href="#modal"]\');
                    if(o.length>0){
                        o.attr("data-toggle","modal").attr("data-target", "#modal");
                    }
                    o.click(function(e){
                        $("#modal").addClass(\'in\');
                    });
                });
            })(jQuery);'); 
        ?>
        <div class="container">
            <div id="modal" class="modal fade fade loginPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <button type="button" class="close modalClose" data-dismiss="modal">×</button>
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <jdoc:include type="modules" name="modal" style="modal"/>
                    </div>
                </div>
            </div>
        </div>
        <?php }
        if($this->countModules('fixed-sidebar-left')){ ?>
        <jdoc:include type="modules" name="fixed-sidebar-left" style="sidebar"/>
        <?php }
        if($this->countModules('fixed-sidebar-right')){ ?>
        <div id="fixed-sidebar-right">
            <jdoc:include type="modules" name="fixed-sidebar-right" style="sidebar"/>
        </div>
        <?php } ?>
        <jdoc:include type="modules" name="debug" style="none"/>
    </body>
</html>