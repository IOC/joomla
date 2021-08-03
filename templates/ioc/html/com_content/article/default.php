<?php
/**
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);
$imgpath = JURI::base() . 'templates/' . $template . '/images/';
$articlenews = array('latest-news', 'featured');
$newsclass = (in_array($this->item->category_alias , $articlenews) ? 'iocnewsarticle' : '');
$pageclass = (!empty($this->pageclass_sfx) ? $this->pageclass_sfx : 'default');
$container = (!empty($this->pageclass_sfx) && strpos($this->pageclass_sfx, 'default') === false ? '' : 'container');
$studylogo = strpos($pageclass, 'logo_') !== false;
$subpage = (strpos($pageclass, 'subpage_') !== false ? ' subpage':'');
$currentstudy = ' ';
$suffixes = array(
    'logo_',
    'subpage_'
);
$blog = $params['layout_type'] == 'blog' ? 'blog blog-individual' : '';
$pageclass = str_replace($suffixes, '', $pageclass);
if ($newsclass) {
    $pageclass = 'news';
    $blog = '';
}

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (JLanguageAssociations::isEnabled() && $params->get('show_associations'));
JHtml::_('behavior.caption');

?>
<?php if ($this->params->get('show_page_heading') || !empty($newsclass)) : ?>
    <div class="fluid-container page-header <?php echo $pageclass; ?> <?php echo $newsclass;  echo $subpage; ?>">
        <div class="container">
            <?php if ($studylogo) : ?>
                <div class="logo_estudi">
                    <img src="<?php echo $imgpath . $pageclass;?>-hexagon-img.png" alt="Estudi <?php echo $pageclass; ?>"/>
                </div>
                <?php
                    $currentstudy .= $pageclass;
                    $pageclass = 'study';
                ?>
            <?php endif; ?>
            <h1 class="<?php echo !empty(trim($currentstudy))?'study':''; ?>"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
    </div>
<?php endif;
    if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative)
    {
        echo $this->item->pagination;
    }
    ?>

<div class="<?php echo $container; ?> item-page<?php echo '-' . $pageclass; echo $currentstudy; echo $blog; ?>" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
    <?php // Todo Not that elegant would be nice to group the params ?>
    <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
    || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') ); ?>

    <div class="<?php echo !empty($newsclass) || $pageclass != 'default' ? 'container':''; ?>">
    <?php if (!$useDefList && $this->print) : ?>
        <div id="pop-print" class="btn hidden-print">
            <?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
        </div>
        <div class="clearfix"> </div>
    <?php endif; ?>
    <?php if ($params->get('show_title') || $params->get('show_author')) : ?>
    <div class="page-header">
        <?php if ($params->get('show_title')) : ?>
            <h2 itemprop="name">
                <?php echo $this->escape($this->item->title); ?>
            </h2>
        <?php endif; ?>
        <?php if ($this->item->state == 0) : ?>
            <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
        <?php endif; ?>
        <?php if (strtotime($this->item->publish_up) > strtotime(JFactory::getDate())) : ?>
            <span class="label label-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></span>
        <?php endif; ?>
        <?php if ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate()) : ?>
            <span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
        <?php endif; ?>
    </div>
    <?php if (!empty($blog)) :
        echo JLayoutHelper::render('joomla.content.info_block.modify_date', array('item' => $this->item, 'params' => $this->params, 'position' => 'above'));
    endif; ?>
    <?php endif; ?>
    <?php if (!$this->print) : ?>
        <?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
            <?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
        <?php endif; ?>
    <?php else : ?>
        <?php if ($useDefList) : ?>
            <div id="pop-print" class="btn hidden-print">
                <?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php // Content is generated by content plugin event "onContentAfterTitle" ?>
    <?php echo $this->item->event->afterDisplayTitle; ?>

    <?php if ( !empty($newsclass)) : ?>
        <?php echo JLayoutHelper::render('joomla.content.info_block.modify_date', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
    <?php endif; ?>

    <?php if ($info == 0 && $params->get('show_tags', 1) && (!empty($this->item->tags->itemTags))  || $pageclass == 'news') : ?>
        <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>

        <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
    <?php endif; ?>
    </div>

    <?php // Content is generated by content plugin event "onContentBeforeDisplay" ?>
    <?php echo $this->item->event->beforeDisplayContent; ?>

    <?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position)))
        || (empty($urls->urls_position) && (!$params->get('urls_position')))) : ?>
    <?php echo $this->loadTemplate('links'); ?>
    <?php endif; ?>
    <?php if ($params->get('access-view')):?>
    <?php if (isset($images->image_fulltext) && !empty($images->image_fulltext)) : ?>
    <?php $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
    <div class="pull-<?php echo htmlspecialchars($imgfloat); ?> item-image"> <img
    <?php if ($images->image_fulltext_caption):
        echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
    endif; ?>
    src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>" itemprop="image"/> </div>
    <?php endif; ?>
    <?php
    if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative):
        echo $this->item->pagination;
    endif;
    ?>
    <?php if (isset ($this->item->toc)) :
        echo $this->item->toc;
    endif; ?>
    <?php
        $customclass = '';
        if (!empty($newsclass)) {
            $customclass = 'container ioc-news-container';
        } else if (!empty($subpage)) {
            $customclass = 'subpagebody';
        } else if (!empty($blog)) {
            $customclass = 'blog-post';
        } else if ($pageclass == 'default') {
            $customclass = 'article-default';
        } else if (strpos($pageclass, 'resource') !== false) {
            $customclass = 'resource';
        }
    ?>
    <div itemprop="articleBody" class="<?php echo $customclass;?>">
        <?php echo $this->item->text; ?>

    <?php if (!empty($blog)) :
        $items = array();
        if (!empty($this->item->tags->itemTags)) {
            $model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
            $model->setState('params', JFactory::getApplication()->getParams());
            $model->setState('filter.category_id', $this->item->catid);
            $model->setState('filter.published', 1);
            $model->setState('list.ordering', 'a.created');
            $model->setState('list.direction', 'DESC');
            $model->setState('list.limit', 4);
            $model->setState('filter.article_id', $this->item->id);
            $model->setState('filter.article_id.include', false);
            $tagids = array_map(
                                function($tag) {
                                    return $tag->tag_id;
                                }, $this->item->tags->itemTags);
            $model->setState('filter.tag', $tagids);
            $items = $model->getItems();
        }
    ?>
        <?php if (!empty($items)) : ?>
            <div class="blog-related-items">
                <div class="blog-related-header">
                    <?php echo JText::_('TPL_IOC_RELATED_BLOG_ENTRIES'); ?>
                </div>
                <?php foreach ($items as $item) : ?>
                    <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language)); ?>" class="blog-related-item">
                        <?php echo $item->title; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
    <?php if ($info == 1 || $info == 2) : ?>
        <?php if ($useDefList) : ?>
            <?php echo JLayoutHelper::render('joomla.content.info_block.modify_date', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
        <?php endif; ?>
        <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
            <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative):
        echo $this->item->pagination;
    ?>
    <?php endif; ?>
    <?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) : ?>
    <?php echo $this->loadTemplate('links'); ?>
    <?php endif; ?>
    <?php // Optional teaser intro text for guests ?>
    <?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
    <?php echo $this->item->introtext; ?>
    <?php // Optional link to let them register to see the whole article. ?>
    <?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
    <?php $menu = JFactory::getApplication()->getMenu(); ?>
    <?php $active = $menu->getActive(); ?>
    <?php $itemId = $active->id; ?>
    <?php $link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
    <?php $link->setVar('return', base64_encode(JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language), false))); ?>
    <p class="readmore">
        <a href="<?php echo $link; ?>" class="register">
        <?php $attribs = json_decode($this->item->attribs); ?>
        <?php
        if ($attribs->alternative_readmore == null) :
            echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
        elseif ($readmore = $this->item->alternative_readmore) :
            echo $readmore;
            if ($params->get('show_readmore_title', 0) != 0) :
                echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
            endif;
        elseif ($params->get('show_readmore_title', 0) == 0) :
            echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
        else :
            echo JText::_('COM_CONTENT_READ_MORE');
            echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
        endif; ?>
        </a>
    </p>
    <?php endif; ?>
    <?php endif; ?>
    <?php
    if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) :
        echo $this->item->pagination;
    ?>
    <?php endif; ?>
    <?php // Content is generated by content plugin event "onContentAfterDisplay" ?>
    <?php echo $this->item->event->afterDisplayContent; ?>
</div>
