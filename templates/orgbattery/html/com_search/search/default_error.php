<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="uk-text-center">
    <img src="<?php echo JURI::base().'images/sprite.svg#file-search'; ?>" width="128" height="128" alt="<?php echo $sitename; ?>" data-uk-svg>
</div>
<?php if ($this->error) : ?>
	<div class="error">
		<?php echo $this->escape($this->error); ?>
	</div>
<?php endif; ?>