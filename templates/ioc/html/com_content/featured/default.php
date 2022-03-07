<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('behavior.caption');

if (!defined('MAXFEATUREDLENGHT')) {
    define('MAXFEATUREDLENGHT', 260);
}

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space

$imgpath = JURI::base() . 'templates/' . $template . '/images';
$lang = JFactory::getLanguage();
$multilang = $lang->getTag() != 'ca-ES';
?>

<?php
$menuitems = array();
?>

<?php if (!empty($this->important)) : ?>
<?php
  //Force ioc-welcome to be first page regardless of ordering
  usort($this->important, function( $a, $b) {
    if ($a->alias == 'ioc-welcome') {
      return ~PHP_INT_MAX;
    } else if ($b->alias == 'ioc-welcome') {
      return PHP_INT_MAX;
    }
    return $a->ordering - $b->ordering;
  });
?>

<div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="0">
  <!-- Indicators -->
  <?php if (!empty($this->intro_items) && !$multilang) : ?>
    <ol class="carousel-indicators hidden">
    	<?php foreach ($this->important as $key => $article) : ?>
          <li data-target="#myCarousel" data-slide-to="<?php echo $key;?>" <?php if (!$key) { ?>class="active" <?php } ?>><?php //echo $article->title;?></li>
    	<?php endforeach; ?>
    </ol>
    <noscript>
      <div class="carousel-indicators">
        <?php foreach ($this->important as $key => $article) : ?>
            <?php $link = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid, $article->language)); ?>
            <a href="<?php echo $link;?>" title="<?php echo $article->title;?>"><?php echo $article->title;?></a>
        <?php endforeach; ?>
      </div>
    </noscript>
  <?php endif; ?>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <?php  if (count(JModuleHelper::getModules('login-campus')) > 0) : ?>
      <?php 
        $iocwarning = JModuleHelper::getModules('avis_campus');
        if (count($iocwarning) > 0) {
          $warningmessage = trim(strip_tags(JModuleHelper::renderModule(array_shift($iocwarning))));
        } else {
          $warningmessage = '';
        }
      ?>
      <div class="container login-campus">
        <?php if (!empty($warningmessage)) : ?>
          <span class="ioc-warning" data-toggle="tooltip" data-original-title="<?php echo $warningmessage; ?>" data-placement="bottom" aria-label="<?php echo JText::_('TPL_IOC_WARNING_CAMPUS');?>" tabindex="0"></span>
        <?php endif; ?>
        <button id="login-campus-large" class="login-campus-body first hidden-xs hidden-sm" data-toggle="modal" data-target="#login-campus" tabindex="12">
            <span class="custom-icon" aria-hidden="true"></span>
            <span class="login-text"><?php echo JText::_('TPL_IOC_LOGIN_CAMPUS') . ' ';?></span>
        </button>
      </div>
    <?php  endif; ?>
    <?php foreach ($this->important as $key => $article) :
            $link = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid, $article->language));
            array_push($menuitems, array($article->title, $link));
            $welcome = $article->alias == 'ioc-welcome';
            $src = $imgpath . '/default-news.jpg';
            $fallbackclass = 'fallbackdefault';
            if ($welcome) {
              if ($welcome = preg_match('~<h2>(.*?)</h2>~', $article->introtext, $matches)) {
                $article->introtext = preg_replace('~<h2>(.*?)</h2>~', '', $article->introtext, 1);
                $article->title = $matches[1];
              }
            } else {
              $article->introtext = preg_replace('~</?p[^>]*>~', '', $article->introtext);
              if (preg_match('/class="imatge-noticia"/', $article->introtext)) {
                preg_match('/<img.+src="(([^"])+)"[^>]+>/', $article->introtext, $matches);
                if (isset($matches[1])) {
                    $src = $matches[1];
                    $article->introtext = preg_replace('/\s*<img[^>]*>\s*/', '', $article->introtext, 1);
                    if (preg_match('/slide(\d+)/', $src, $matches)) {
                      $fallbackclass = 'fallback' . $matches[1];
                    }
                }
              }
            }
    ?>
            <?php if ($welcome) : ?>
              <div class="item welcome <?php if (!$key) { ?> active <?php } ?>">
                <div class="carousel-transparency">
                  <img class="top-layer" src="<?php echo $imgpath; ?>/transparency.svg" alt="">
                </div>
                <?php echo $article->introtext; ?>
            <?php else : ?>
              <div class="item <?php if (!$key) { ?> active <?php } ?> <?php echo $fallbackclass; ?>" style="background-image: url('<?php echo $src;?>')">
            <?php endif; ?>
            <div class="container logo-ioc-large">
              <img src="<?php echo $imgpath; ?>/logo-ioc-gran.svg" alt="Institut Obert de Catalunya">
            </div>
              <div class="important-background"></div>
              <div class="container carousel-caption">
                <?php
                  $link = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid, $article->language));
                ?>
                <?php if ($welcome) : ?>
                  <h2><?php echo str_replace('{TPL_IOC_MAIN_WELCOME_IOC}', JText::_('TPL_IOC_MAIN_WELCOME_IOC'), $article->title);?></h2>
                <?php else : ?>
                  <h2><a href="<?php echo $link;?>"><?php echo $article->title;?></a></h2>
                    <?php
                      $cleanintrotext = strip_tags($article->introtext);
                      $cleanintrotext = str_replace('’', '\'', $cleanintrotext);
                      if (mb_strlen($article->introtext) > MAXFEATUREDLENGHT) {
                          $cleanintrotext = substr($cleanintrotext, 0, MAXFEATUREDLENGHT) . ' (&#8230;)';
                      }
                    ?>
                    <div class="carousel-introtext">
                      <?php echo $cleanintrotext;?>
                      <div class="link-button">
                        <a href="<?php echo $link;?>"><?php echo JText::_('TPL_IOC_JUMP_TO_NEW');?></a>
                      </div>
                    </div>
                <?php endif; ?>
        	    </div>
            </div>
    <?php endforeach; ?>
  </div>
  <?php if (!empty($this->intro_items) && !$multilang) : ?>
    <div class="avisos hidden-sm hidden-xs">
      <a class="prev" href="#myCarousel" role="button" data-slide="prev">
        <span class="control-prev" aria-hidden="true"></span>
      </a>
      <a class="next" href="#myCarousel" role="button" data-slide="next">
        <?php foreach ($menuitems as $k => $item) :?>
          <?php
            if ($k == 0) {
              $item = array(JText::_('JLIB_HTML_START'), '');
            }
          ?>
          <div><?php echo $item[0]; ?></div>
        <?php endforeach; ?>
        <span class="control-next" aria-hidden="true"></span>
      </a>
    </div>
  <?php endif; ?>
  <?php if(!empty($warningmessage)) : ?>
  <div class="warning-mobile visible-sm visible-xs">
    <h2><?php echo JText::_('TPL_IOC_WARNINGS');?></h2>
      <div class="warning-message"><?php echo $warningmessage; ?></div>
  </div>
  <?php endif; ?>
  <?php if (!empty($this->intro_items) && !$multilang) : ?>
    <div class="avisos-mobile visible-sm visible-xs">
      <h2><?php echo JText::_('TPL_IOC_FEATURED');?></h2>
        <?php foreach ($menuitems as $k => $item) :?>
          <?php
            if ($k == 0) {
              continue;
            }
          ?>
          <div><a href="<?php echo $item[1];?>"><?php echo $item[0]; ?></a></div>
        <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="icon-prev" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="icon-next" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
<?php endif; ?>

<!-- Print studies -->
<?php if ($modules = JModuleHelper::getModules('studies')) : ?>
  <div id="estudis"></div>
  <?php foreach ($modules as $module) : ?>
    <div class="container all-studies">
    <?php echo JModuleHelper::renderModule($module, array('style' => 'container studies')); ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php if ($modules = JModuleHelper::getModules('employment')) : ?>
  <?php foreach ($modules as $module) : ?>
    <div class="layout-employment">
    <?php echo JModuleHelper::renderModule($module, array('style' => 'container employment')); ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<div id="news" >
<div class="container blog-featured<?php echo $this->pageclass_sfx;?>" itemscope itemtype="https://schema.org/Blog">

<?php if ($this->params->get('show_page_heading') != 0) : ?>
  <div class="title">
    <h1>
      <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
  </div>
<?php endif; ?>

<?php $leadingcount = 0; ?>
<?php if (!empty($this->lead_items)) : ?>
<div class="items-leading clearfix">
	<?php foreach ($this->lead_items as &$item) : ?>
		<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?> clearfix"
			itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			<?php
				$this->item = &$item;
				echo $this->loadTemplate('item');
			?>
		</div>
		<?php
			$leadingcount++;
		?>
	<?php endforeach; ?>
</div>
<?php endif; ?>
<?php
	$counter = 0;
?>
<?php if (!empty($this->intro_items)) : ?>
	<?php foreach ($this->intro_items as $key => &$item) : ?>
		<?php
		if ($counter == 0) : ?>
  		<div class="items-row">
		<?php endif; ?>
			<div class="item <?php echo $item->state == 0 ? ' system-unpublished' : null; ?> "
				itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
			?>
			</div>
			<?php $counter++; ?>

			<?php if ($counter == 3) : ?>
        <?php if (!empty($this->link_items)) : ?>
          <div class="item link_items items-more">
            <h3><?php echo JText::_('TPL_IOC_READ_MORE_NEWS_TITLE'); ?></h3>
            <div class="link-elements">
              <?php echo $this->loadTemplate('links'); ?>
            </div>
          </div>
        <?php endif; ?>
        </div>
		  <?php endif; ?>

	<?php endforeach; ?>
  <?php else: ?>
    <div class="nonews"></div>
<?php endif; ?>

<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
  	<div class="content-pagination">
  		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
  			<p class="counter pull-right">
  				<?php echo $this->pagination->getPagesCounter(); ?>
  			</p>
  		<?php  endif; ?>
  				<?php echo $this->pagination->getPagesLinks(); ?>
  				<img src="templates/ioc/images/2022_febrer_LogosMRR.png" class="test" title="logos"  style="margin-bottom: -60px;" />
  	</div>
<?php endif; ?>

</div>
</div>
