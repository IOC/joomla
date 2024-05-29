<?php
/*------------------------------------------------------------------------
# author Gonzalo Suez
# copyright Copyright © 2013 gsuez.cl. All rights reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website http://www.gsuez.cl
-------------------------------------------------------------------------*/	// no direct access
require_once(__DIR__ .'/../../configuration.php');

defined('_JEXEC') or die;
//queueit
$CONF = new Jconfig();

$file_queueit = $CONF->jsonQueueit . 'queueit.txt';

if(!empty($_SERVER['HTTP_VIA'])){
    if (strpos($_SERVER['HTTP_VIA'], 'translate') !== false || strpos($_SERVER['HTTP_VIA'], 'google') !== false) {
        $file_queueit = false;
    }
}

if (file_exists($file_queueit)) {
    require_once(__DIR__ . '/../../queueit/iocqueue.php');
    iocqueue($CONF->jsonQueueit);
}
//End Queueit

require_once('includes/params.php');
if ($params->get('compile_sass', '0') === '1')
{
    require_once("includes/sass.php");
}
if ($params->get('refresh_media_version', '0') === '1')
{
    JFactory::getApplication()->flushAssets();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
<?php
require_once('includes/head.php');
$app = JFactory::getApplication();
$menu = $app->getMenu();
$lang = JFactory::getLanguage();
if ($menu->getActive() == $menu->getDefault($lang->getTag())
    && JRequest::getCmd('view') == 'featured') {
    $frontpage = 'ioc-front-page';
} else {
    $frontpage = '';
}
$multilang = ($this->language != 'ca-es');
$pageclass = '';
$imgpath = 'templates/' . $app->getTemplate() . '/images/';
$itemid = JRequest::getVar('Itemid');
if ($itemid && JRequest::getCmd('view') != 'search') {
    $active = $menu->getItem($itemid);
    if ($active) {
        $params = $menu->getParams( $active->id );
    }
    $pageclass = $params->get( 'pageclass_sfx' );
    $suffixes = array(
        'logo_',
        'subpage_'
    );
    $pageclass = str_replace($suffixes, '', $pageclass);
    if ($pageclass == 'news' && !empty($frontpage)) {
        $pageclass = '';
    }
}
?>
<body class="<?php echo $frontpage; ?>">
<?php
 if($layout=='boxed'){ ?>
<div class="layout-boxed">
  <?php  } ?>
<div id="wrap">
<!--Navigation-->
<header id="header" class="header header--fixed hide-from-print <?php echo $pageclass;?>">
<div id="navigation">
<div class="fake-menu-bg"></div>
<div class="navbar navbar-default">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
    <div id="brand">
        <a href="http://ensenyament.gencat.cat" class="ioc-departament" tabindex="1" aria-label="<?php echo JText::_('TPL_IOC_JUMP_TO_DEPARTAMENT');?>">
            <img class=" logo" src="<?php echo $imgpath; ?>logo-dep-edu_feder_blanc.png" alt="Departament d'Educacio" />
        </a>
        <a href="<?php echo $this->params->get('logo_link');?>" class="ioc-logo" tabindex="1" aria-label="<?php echo JText::_('TPL_IOC_JUMP_TO_START');?>">
            <img class="logo" src="<?php echo $imgpath; ?>logo-ioc-petit.svg" alt="Institut Obert de Catalunya" />
        </a>
    </div>
    <!-- Campus -->
    <?php  if ($this->countModules('login-campus')) : ?>
        <div id="login-campus-small" class="login-campus-mobile visible-xs visible-sm tiny-campus">
            <button type="button" class="btn-lg" data-toggle="modal" data-target="#login-campus">
                <span class="custom-icon"></span>
            </button>
        </div>
    <?php endif; ?>
    <!-- Search -->
    <?php if(!$multilang) : ?>
        <div class="visible-xs visible-sm tiny-search">
            <button type="button" class="btn-lg" data-toggle="collapse" data-target="#search">
                <span class="custom-icon"></span>
            </button>
        </div>
    <?php endif; ?>
</div>
<?php  if ($this->countModules('login-campus')) : ?>
    <button id="login-campus-medium" class="hidden-xs hidden-sm login-clone-campus" data-toggle="modal" data-target="#login-campus">
        <span class="custom-icon" aria-hidden="true"></span>
        <span class="login-text"><?php echo JText::_('TPL_IOC_LOGIN_CAMPUS') . ' ';?></span>
    </button>
<?php  endif; ?>
<div class="social hidden-sm hidden-xs text-left">
        <a href="https://es.linkedin.com/in/ioc-institut-obert-de-catalunya-bb4805b1" target="_blank" aria-label="<?php echo JText::_('TPL_IOC_LINKEDIN');?>" tabindex="9">
            <span class="linkedin custom-icon"></span>
        </a>
        <a href="http://twitter.com/ioc" target="_blank" aria-label="<?php echo JText::_('TPL_IOC_TWITTER');?>" tabindex="10">
            <span class="twitter custom-icon"></span>
        </a>
        <a href="https://vimeo.com/institutobert" target="_blank" aria-label="<?php echo JText::_('TPL_IOC_VIMEO');?>" tabindex="11">
            <span class="vimeo custom-icon"></span>
        </a>
</div>
<div class="navbar-collapse collapse col-md-1 col-lg-1 col-sm-1 hidden-xs ioc-search">
    <?php if (!$multilang): ?>
        <ul class="nav navbar-nav search">
            <li>
                <span class="hidden-md hidden-sm hidden-xs">|</span>
                <button id="search-button" type="button" class="btn-md" data-toggle="collapse" data-target="#search" tabindex="8">
                    <span class="custom-icon" aria-hidden="true"></span>
                    <span class="string-search hidden-md hidden-sm hidden-xs"><?php echo JText::_('JSEARCH_FILTER_SUBMIT');?></span>
                </button>
            </li>
        </ul>
    <?php endif; ?>
</div>

<div class="navbar-collapse collapse ioc-menu col-sm-6 col-md-8 col-lg-9" aria-expanded="false">
<?php  if ($this->countModules('navigation')) : ?>
                        <jdoc:include type="modules" name="navigation" style="none" />
                        <?php  endif; ?>
</div>
<?php if (!$multilang) : ?>
<div class="megaphone hidden-xs hidden-sm">
    <a href="/educacio/#news" aria-label="<?php echo JText::_('TPL_IOC_LINK_NEWS');?>" tabindex="2" title="<?php echo JText::_('TPL_IOC_TAG_NEWS');?>">
        <svg width="32px" height="22px" viewBox="0 0 32 22" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g id="ioc-news-icon" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <path d="M18,3.12132034 L16.5606602,4.56066017 C15.6454749,5.4758454 14.224626,6.08478065 12.2941742,6.47087101 L12.1485293,6.5 L5,6.5 C3.61928813,6.5 2.5,7.61928813 2.5,9 L2.5,11 C2.5,12.3807119 3.61928813,13.5 5,13.5 L12.1846584,13.5 L12.3638034,13.5447862 C13.9629043,13.9445615 15.3698519,14.7485315 16.5606602,15.9393398 L18,17.3786797 L18,3.12132034 Z M19.5,18 C19.5,18.3333333 19.3333333,18.6666667 19,19 C18.5,19.5 17.5,19 17.5,19 L15.5,17 C14.5,16 13.3333333,15.3333333 12,15 L5,15 C2.790861,15 1,13.209139 1,11 L1,9 C1,6.790861 2.790861,5 5,5 L12,5 C13.6666667,4.66666667 14.8333333,4.16666667 15.5,3.5 L17.5,1.5 C18.1666667,1.16666667 18.6666667,1.16666667 19,1.5 C19.3333333,1.83333333 19.5,2.16666667 19.5,2.5 L19.5,18 Z" id="main_shape" fill="white" fill-rule="nonzero"></path>
                <polygon id="line" fill="white" fill-rule="nonzero" points="11.25 5.75 12.75 5.75 12.75 14.25 11.25 14.25"></polygon>
                <path d="M9.5,19.5 C9.22385763,19.5 9,19.7238576 9,20 L9,19.5 L9.5,19.5 Z M9,13.5 L10.5,13.5 L10.5,20 C10.5,20.5522847 10.0522847,21 9.5,21 L7.8198039,21 C7.34312288,21 6.93270807,20.6635403 6.83922323,20.1961161 L5.5,13.5 L7.02970585,13.5 L8.22970585,19.5 L9,19.5 L9,13.5 Z M8.31009424,19.9019419 C8.26335182,19.6682298 8.05814441,19.5 7.8198039,19.5 L8.22970585,19.5 L8.31009424,19.9019419 Z M7.8198039,19.5 C8.05814441,19.5 8.26335182,19.6682298 8.31009424,19.9019419 L8.22970585,19.5 L7.8198039,19.5 Z M9,19.5 L9,13.5 L10.5,13.5 L10.5,20 C10.5,20.5522847 10.0522847,21 9.5,21 L7.8198039,21 C7.34312288,21 6.93270807,20.6635403 6.83922323,20.1961161 L5.5,13.5 L7.02970585,13.5 L8.22970585,19.5 L9,19.5 Z M9.5,19.5 C9.22385763,19.5 9,19.7238576 9,20 L9,19.5 L9.5,19.5 Z" id="handler" fill="white" fill-rule="nonzero"></path>
                <path d="M19,13.25 L19,11.75 C19.9664983,11.75 20.75,10.9664983 20.75,10 C20.75,9.03350169 19.9664983,8.25 19,8.25 L19,6.75 C20.7949254,6.75 22.25,8.20507456 22.25,10 C22.25,11.7949254 20.7949254,13.25 19,13.25 Z" id="oval" fill="white" fill-rule="nonzero"></path>
                <path d="M24.8911949,12.8882164 C24.6590224,13.2312453 24.1927296,13.3211122 23.8497007,13.0889397 C23.5066718,12.8567672 23.4168049,12.3904744 23.6489774,12.0474455 C24.0351659,11.4768618 24.25,10.7708819 24.25,10.0225971 C24.25,9.29060193 24.0444594,8.59892224 23.6734833,8.0344898 C23.4459793,7.68834707 23.542155,7.22331448 23.8882977,6.99581046 C24.2344405,6.76830643 24.699473,6.8644821 24.9269771,7.21062483 C25.4593099,8.0205583 25.75,8.99878077 25.75,10.0225971 C25.75,11.0695092 25.4459982,12.0685087 24.8911949,12.8882164 Z" id="wave_one" fill="white" fill-rule="nonzero"></path>
                <path d="M26.6937033,14.9049977 C26.3716592,15.165498 25.8994135,15.1156069 25.6389132,14.7935628 C25.3784128,14.4715186 25.4283039,13.999273 25.7503481,13.7387726 C26.6523664,13.0091333 27.25,11.5758802 27.25,9.96792383 C27.25,8.31934702 26.6216219,6.85698409 25.6866718,6.14715844 C25.3567655,5.89668949 25.2923687,5.42620238 25.5428377,5.09629605 C25.7933066,4.76638973 26.2637938,4.70199302 26.5937001,4.95246196 C27.9289435,5.96619523 28.75,7.87695976 28.75,9.96792383 C28.75,12.0042766 27.9714328,13.8714469 26.6937033,14.9049977 Z" id="wave_two" fill="white" fill-rule="nonzero"></path>
                <path d="M28.7057866,17.0319703 C28.3981968,17.3093894 27.9239533,17.2849311 27.6465342,16.9773413 C27.3691152,16.6697514 27.3935735,16.1955079 27.7011633,15.9180888 C29.3539679,14.4274042 30.25,12.490523 30.25,9.9990276 C30.25,7.50574074 29.3541374,5.57079382 27.698083,4.07819096 C27.3903996,3.80087567 27.3657813,3.32664049 27.6430966,3.01895704 C27.9204119,2.71127359 28.3946471,2.68665534 28.7023305,2.96397063 C30.668122,4.73573973 31.75,7.07245575 31.75,9.9990276 C31.75,12.92355 30.6681564,15.2620851 28.7057866,17.0319703 Z" id="wave_three" fill="white" fill-rule="nonzero"></path>
            </g>
    </svg>
    </a>
</div>
<?php  endif; ?>
</div></div>
</div>
</header>
<div class="clearfix"></div>
<!--Navigation-->
<section>
<!--fullwidth-->
<?php  if($this->countModules('fullwidth')) : ?>
<div id="fullwidth">
<div class="row">
<jdoc:include type="modules" name="fullwidth" style="block"/>
</div>
</div>
<?php  endif; ?>
<!--fullwidth-->
<!--Showcase-->
<?php  if($this->countModules('showcase')) : ?>
<div id="showcase">
<div class="container">
<div class="row">
<jdoc:include type="modules" name="showcase" style="block"/>
</div>
</div>
</div>
<?php  endif; ?>
<!--Showcase-->
<!--Feature-->
<?php  if($this->countModules('feature')) : ?>
<div id="feature">
<div class="container">
<div class="row">
<jdoc:include type="modules" name="feature" style="block" />
</div>
</div>
</div>
<?php  endif; ?>
<!--Feature-->
<!--Breadcrum-->
<?php  if($this->countModules('breadcrumbs')) : ?>
<div id="breadcrumbs" class="hidden-xs">
<div class="container">
<div class="row">
<jdoc:include type="modules" name="breadcrumbs" style="block" />
</div>
</div>
</div>
<!--Breadcrum-->
<?php  endif; ?>
<!-- Content -->
<div class="fluid-container">
<div id="main" class="row show-grid">
<!-- Left -->
<?php  if($this->countModules('left')) : ?>
<div id="sidebar" class="col-sm-<?php  echo $leftcolgrid; ?>">
<jdoc:include type="modules" name="left" style="block" />
</div>
<?php  endif; ?>
<!-- Component -->
<div id="container" class="col-sm-<?php  echo (12-$leftcolgrid-$rightcolgrid); ?>">
<!-- Content-top Module Position -->
<?php  if($this->countModules('content-top')) : ?>
<div id="content-top" class="container">
<button type="button" class="collapsed" data-toggle="collapse" data-target=".menu-nav">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<div class="collapse menu-nav">
    <jdoc:include type="modules" name="content-top" style="block" />
</div>
</div>
<?php  endif; ?>
<!-- Front page show or hide -->
<?php
	if ($frontpageshow){
		// show on all pages
		?>
<div id="main-box">

<jdoc:include type="message" />
<div class="collapse form-search search-top" id="search" aria-labelledby="search">
    <div class="container">
        <jdoc:include type="modules" name="search" style="modules" />
    </div>
</div>
<jdoc:include type="component" />
<?php  if ($this->countModules('sub_studies')) : ?>
    <div class="container all-studies">
        <jdoc:include type="modules" name="sub_studies" style="sub_studies" />
    </div>
<?php  endif; ?>
<?php  if ($this->countModules('login-campus')) : ?>
<div class="modal fade login-form-campus" id="login-campus" role="dialog" aria-labelledby="form-title">
    <div class="modal-dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-body">
                    <jdoc:include type="modules" name="login-campus" style="modal" />
                </div>
            </div>
        </div>
    </div>
</div>
<?php  endif; ?>
</div>
<?php
	} else {
		if ($menu->getActive() !== $menu->getDefault()) {
			// show on all pages but the default page
			?>
<div id="main-box">
<jdoc:include type="component" />
</div>
<?php
 }} ?>
<!-- Front page show or hide -->
<!-- Below Content Module Position -->
<?php  if($this->countModules('content-bottom')) : ?>
<div id="content-bottom">
<div class="row">
<jdoc:include type="modules" name="content-bottom" style="block" />
</div>
</div>
<?php  endif; ?>
</div>
<!-- Right -->
<?php  if($this->countModules('right')) : ?>
<div id="sidebar-2" class="col-sm-<?php  echo $rightcolgrid; ?>">
<jdoc:include type="modules" name="right" style="block" />
</div>
<?php  endif; ?>
</div>
</div>

<!-- Content -->
<!-- bottom -->
<?php  if($this->countModules('bottom')) : ?>
<div id="bottom">
<div class="container">
<div class="row">
<jdoc:include type="modules" name="bottom" style="block" />
</div>
</div>
</div>
<?php  endif; ?>
<!-- bottom -->
<!-- footer -->
<?php  if($this->countModules('footer')) : ?>
<div id="footer" class="fluid-container footer">
    <div class="logo-mobile container visible-xs visible-sm">
        <img src="<?php echo $imgpath?>logo-ioc-negatiu.svg" alt="Institut Obert de Catalunya" />
    </div>
    <div id="footer-collapse" class="top container">
        <div class="top">
            <jdoc:include type="modules" name="footer-top-col1" style="xhtml" />
            <jdoc:include type="modules" name="footer-top-col2" style="xhtml" />
            <jdoc:include type="modules" name="footer-top-col3" style="xhtml" />
            <jdoc:include type="modules" name="footer-top-col4" style="xhtml" />
        </div>
    </div>
    <div class="visible-xs visible-sm container mobile">
        <div class="social">
            <span class="text"><?php echo JText::_('TPL_IOC_FOLLOWUS'); ?></span>
            <a href="https://es.linkedin.com/in/ioc-institut-obert-de-catalunya-bb4805b1">
                <span class="custom-icon linkedin"></span>
            </a>
            <a href="https://twitter.com/ioc"><span class="custom-icon twitter"></span></a>
            <a href="https://vimeo.com/institutobert"><span class="custom-icon vimeo"></span></a>
        </div>
        <div class="contacte">
            <span class="text"><?php echo JText::_('TPL_IOC_CONTACTUS'); ?>
                <a href="component/contactioc">
                    <span class="custom-icon correu"></span>
                </a>
            </span>
        </div>
        <div class="contacte-gran">
            <span class="text"><?php echo JText::_('TPL_IOC_CONTACT_ADVICE'); ?>
                <a href="https://appe.isotools.org/wip/wip2015/registro.cfm?token=MEUwMENBRkJDRTU1QjM4OUZFMDlFNUNERjNBOTBGMkExNkMxRkRDREE5QTcxRkU4QjlCNzkyQkY1NjM4NkY1QzEzNUJCQjdENTI4Nw==" target="_blank">
                    <span class="custom-icon correu"></span>
                </a>
            </span>
        </div>
    </div>
    <?php  if ($this->countModules('lang-menu')) : ?>
        <div class="container ioc-languages">
            <jdoc:include type="modules" name="lang-menu" style="none" />
        </div>
    <?php endif; ?>
    <div class="bottom container">
        <div class="ioc-banners">
            <div class="col-xs-3 col-sm-3 col-md-3 col-1">
                <jdoc:include type="modules" name="footer-bottom-col1" style="xhtml" />
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
                <jdoc:include type="modules" name="footer-bottom-col2" style="xhtml" />
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3">
                <jdoc:include type="modules" name="footer-bottom-col3" style="xhtml" />
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-4">
                <jdoc:include type="modules" name="footer-bottom-col4" style="xhtml" />
            </div>
        </div>
    </div>
    <jdoc:include type="modules" name="footer" />
</div>
<?php  endif; ?>
<!-- footer -->
<!--<div id="push"></div>-->
<!-- copy -->
<?php  if($this->countModules('copy')) : ?>
<div id="copy"  class="well">
<div class="container">
<div class="row">
<jdoc:include type="modules" name="copy" style="block" />
</div>
</div>
</div>
<?php  endif; ?>
<!-- copy -->
<!-- menu slide -->
<?php  if($this->countModules('panelnav')): ?>
<div id="panelnav">
    <jdoc:include type="modules" name="panelnav" style="none" />
</div><!-- end panelnav -->
<?php  endif;// end panelnav  ?>
<!-- menu slide -->
<a href="#" class="back-to-top" tabindex="0"><span class="custom-icon"></span><span class="hidden-xs hidden-sm text"><?php echo JText::_('TPL_IOC_BACKTOTOP'); ?></span></a>
<jdoc:include type="modules" name="debug" />
</section></div>
<?php
 if($layout=='boxed'){ ?>
</div>
  <?php  } ?>
<!-- page -->
<?php
JText::script('TPL_IOC_ERROR_CAMPUS_1');
JText::script('TPL_IOC_ERROR_CAMPUS_2');
JText::script('TPL_IOC_ERROR_CAMPUS_3');
JText::script('TPL_IOC_ERROR_CAMPUS_4');
JText::script('TPL_IOC_TAB_NEXT');
JText::script('TPL_IOC_TAB_PREVIOUS');
?>
<!-- JS -->
<script type="text/javascript" src="<?php echo $tpath; ?>/js/template.min.js"></script>
<!-- JS -->
</body>
</html>
