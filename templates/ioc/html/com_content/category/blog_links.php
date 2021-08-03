<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php foreach ($this->link_items as &$item) : ?>
    <div class="extra-blog">
        <div class="more-blog-icon">
            <span class="custom-icon fletxa"></span>
        </div>
        <div class="more-blog-title">
            <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
                <?php echo $item->title; ?>
            </a>
        </div>
    </div>
<?php endforeach; ?>
