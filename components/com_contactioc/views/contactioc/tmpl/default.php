<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contactioc
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.core');
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.framework');
JHtml::_('formbehavior.chosen', 'select');

JHTML::script('components/com_contactioc/js/contactioc.js', array('version' => 'auto'));

$data  = $this->get('data');
JText::script('COM_CONTACTIOC_ERROR');
JText::script('COM_CONTACTIOC_INVALID_SELECT');

?>
<div class="fluid-container page-header default resource ">
    <div class="container">
        <h1 class="">
        <?php echo JText::_('COM_CONTACTIOC_INBOX'); ?>
        </h1>
    </div>
</div>
<div id="contactioc-page" class="item-page-default contact-ioc">
    <div class="inbox_message">
        <?php echo JText::_('COM_CONTACTIOC_INBOX_MESSAGE'); ?>
    </div>
	<form action="<?php echo JRoute::_('index.php?option=com_contactioc&task=send'); ?>" id="contactiocForm" method="post">
        <fieldset>
            <legend><?php echo JText::_('COM_CONTACTIOC_INBOX_HEADER'); ?></legend>
            <div>
                <label for="contactStudy">
                    <?php echo JText::_('COM_CONTACTIOC_STUDY'); ?>
                    <span class="required">*</span>
                </label>
                <select name="contactStudy" id="contactStudy" class="form-control custom_select" oninvalid="setCustomValidity('<?php echo JText::_('COM_CONTACTIOC_INVALID_SELECT');?>')" required>
                    <?php for ($i = 0; $i <= $this->emails; $i++) : ?>
                        <option value="<?php echo ($i ? $i : ''); ?>"<?php echo ($this->escape($data->contactStudy) == $i ? 'selected' : '')?>><?php echo JText::_('COM_CONTACTIOC_STUDY' . $i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="contactSubject">
                    <?php echo JText::_('COM_CONTACTIOC_SUBJECT'); ?>
                    <span class="required">*</span>
                </label>
                <div>
                    <input name="contactSubject" value="<?php echo $this->escape($data->contactSubject); ?>" id="contactSubject" class="form-control" type="text" pattern="^\S+.*\S+$" oninvalid="setCustomValidity('<?php echo JText::_('COM_CONTACTIOC_INVALID_TEXT');?>')" required >
                </div>
            </div>
            <div>
                <label for="textarea" class="">
                    <?php echo JText::_('COM_CONTACTIOC_CONTENT'); ?>
                    <span class="required">*</span>
                </label>
                <div>
                    <textarea name="contactContent" id="textarea" class="form-control" maxlength="2048" required ><?php echo $this->escape($data->contactContent); ?></textarea>
                    <div class="clarification">
                        <?php echo JText::_('COM_CONTACTIOC_CONTENT_CLARIFICATION'); ?>
                    </div>
                </div>
            </div>
            <fieldset class="contact-files">
                <legend>
                <?php echo JText::_('COM_CONTACTIOC_FILES'); ?>
                </legend>
                <div>
                    <label for="contactFile1"><?php echo JText::_('COM_CONTACTIOC_FILE');?></label>
                    <input type="file"
                           id="contactFile1" name="contactFile1"
                           accept="image/png, image/jpeg, application/pdf, application/vnd.oasis.opendocument.text, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                </div>
                <div>
                    <label for="contactFile2"><?php echo JText::_('COM_CONTACTIOC_FILE');?></label>
                    <input type="file"
                           id="contactFile2" name="contactFile2"
                           accept="image/png, image/jpeg, application/pdf, application/vnd.oasis.opendocument.text, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                </div>
                <div>
                    <label for="contactFile3"><?php echo JText::_('COM_CONTACTIOC_FILE');?></label>
                    <input type="file"
                           id="contactFile3" name="contactFile3"
                           accept="image/png, image/jpeg, application/pdf, application/vnd.oasis.opendocument.text, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                </div>
            </fieldset>
        </fieldset>
        <fieldset>
            <legend>
            <?php echo JText::_('COM_CONTACTIOC_CONTACT_DETAILS'); ?>
            </legend>
            <div>
                <label for="nom">
                    <?php echo JText::_('COM_CONTACTIOC_FIRSTNAME'); ?>
                    <span class="required">*</span>
                    <input name="contactFirstname" maxlength="100" id="nom" class="form-control" type="text" value="<?php echo $this->escape($data->contactFirstname); ?>" pattern="^\S+.*\S+$" oninvalid="setCustomValidity('<?php echo JText::_('COM_CONTACTIOC_INVALID_TEXT');?>')" required >
                </label>
            </div>
            <div>
                <label for="cognom">
                    <?php echo JText::_('COM_CONTACTIOC_LASTNAME'); ?>
                    <span class="required">*</span>
                    <input name="contactLastname" maxlength="100" id="cognom" class="form-control" type="text" value="<?php echo $this->escape($data->contactLastname); ?>" pattern="^\S+.*\S+$" oninvalid="setCustomValidity('<?php echo JText::_('COM_CONTACTIOC_INVALID_TEXT');?>')" required >
                </label>
            </div>
            <div>
                <label for="correu">
                    <?php echo JText::_('COM_CONTACTIOC_EMAIL'); ?>
                    <span class="required">*</span>
                    <input name="contactEmail" maxlength="100" id="correu" class="form-control" type="email" value="<?php echo $this->escape($data->contactEmail); ?>" required >
                </label>
            </div>
            <div class="iocagree-text">
                <h3><?php echo JText::_('COM_CONTACTIOC_GDPR_HEADER'); ?></h3>
                <div class="text-row">
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT1'); ?></span>
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT2'); ?> <a href="/educacio/proteccio-dades">+info</a></span>
                </div>
                <div class="text-row">
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT3'); ?></span>
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT4'); ?> <a href="/educacio/proteccio-dades">+info</a></span>
                </div>
                <div class="text-row">
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT5'); ?></span>
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT6'); ?> <a href="/educacio/proteccio-dades">+info</a></span>
                </div>
                <div class="text-row">
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT7'); ?></span>
                    <span><?php echo JText::_('COM_CONTACTIOC_GDPR_TEXT8'); ?>
                        <br /><a href="https://ja.cat/IOCprotecciodedades">https://ja.cat/IOCprotecciodedades</a>
                    </span>
                </div>
            </div>
            <div>
                <label for="agree" class="iocagree">
                    <?php echo JText::_('COM_CONTACTIOC_AGREE'); ?>
                    <span class="required">*</span>
                    <input name="contactAgree" id="agree" class="form-control" type="checkbox" value="true" required >
                </label>
            </div>
        </fieldset>

        <!-- Removed captcha in contact form -->
        <!--
        <?php
            $captcha_plugin = JFactory::getConfig()->get('captcha');
            if ($captcha_plugin != '0') {
              $captcha = JCaptcha::getInstance($captcha_plugin);
              $field_id = 'contactioc-captcha';
              print $captcha->display($field_id, $field_id, 'g-recaptcha');
            }
        ?>
        -->

		<div>
			<button type="submit" class="contact-submit">
				<?php echo JText::_('COM_CONTACTIOC_SEND'); ?>
			</button>
		</div>
		<input type="hidden" name="layout" value="<?php echo htmlspecialchars($this->getLayout(), ENT_COMPAT, 'UTF-8'); ?>" >
		<input type="hidden" name="option" value="com_contactioc" >
		<input type="hidden" name="task" value="send" >
		<input type="hidden" name="tmpl" value="component" >
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
