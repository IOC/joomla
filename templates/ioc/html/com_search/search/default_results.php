<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="search-results<?php echo $this->pageclass_sfx; ?>">
<?php foreach ($this->results as $result) : ?>
    <div class="result-data">
        <?php if ($result->href) : ?>
            <a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) : ?> target="_blank"<?php endif; ?>>
                <?php // $result->title should not be escaped in this case, as it may ?>
                <?php // contain span HTML tags wrapping the searched terms, if present ?>
                <?php // in the title. ?>
                <span class="result-title">
                    <?php echo $result->title; ?>
                </span>
        <?php else : ?>
            <?php // see above comment: do not escape $result->title ?>
            <?php echo $result->title; ?>
        <?php endif; ?>
    <div class="result-text">
        <?php echo $result->text; ?>
    </div>
    <?php if ($result->section) : ?>
        <div class="result-category">
            <span class="small<?php echo $this->pageclass_sfx; ?>">
                <?php echo $this->escape($result->section); ?>
            </span>
        </div>
    <?php endif; ?>
    <?php if ($result->href) : ?>
        </a>
    <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
<div class="content-pagination">
    <?php echo $this->pagination->getPagesLinks(); ?>
</div>
