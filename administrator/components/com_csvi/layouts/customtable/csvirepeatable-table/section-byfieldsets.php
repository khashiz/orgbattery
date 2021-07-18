<?php
/**
 * @package     CSVI
 * @subpackage  Plugin.Multireplace
 *
 * @author      RolandD Cyber Produksi <contact@rolandd.com>
 * @copyright   Copyright (C) 2006 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Make thing clear
 *
 * @var JForm   $form       The form instance for render the section
 * @var string  $basegroup  The base group name
 * @var string  $group      Current group name
 * @var array   $buttons    Array of the buttons that will be rendered
 * @var int     $row        The row number being rendered
 */
extract($displayData);

if (!array_key_exists('row', $displayData))
{
	$row = 1;
}

$fieldsets         = $form->getFieldsets();
$temp              = array();
$temp['alias']     = $fieldsets['alias'];
$temp['joinalias'] = $fieldsets['joinalias'];

unset($fieldsets['alias']);
unset($fieldsets['joinalias']);
?>

<tr class="subform-repeatable-group-1" data-base-name="<?php echo $basegroup; ?>" data-group="<?php echo $group; ?>">
	<?php foreach($fieldsets as $fieldset): ?>
		<td class="<?php if (!empty($fieldset->class)){ echo $fieldset->class; } ?>" data-column="<?php echo $fieldset->name; ?>">
			<?php foreach($form->getFieldset($fieldset->name) as $field): ?>
			<div class="<?php echo $field->labelclass; ?>">
				<?php
					if ($row > 0 || ($row === 0 && $fieldset->name === 'custom'))
					{
						echo '<div class="inputfield">' . $field->input . '</div>';

						if ($fieldset->name === 'custom')
						{
							$alias = $form->getFieldset('alias');
							$field = reset($alias);

							echo '<br/>' . JText::_($field->label);
							echo '<div class="inputfield">' . $field->input . '</div>';
						}
						elseif ($fieldset->name === 'jointable')
						{
							$alias = $form->getFieldset('joinalias');
							$field = reset($alias);

							echo '<br/>' . JText::_($field->label);
							echo '<div class="inputfield">' . $field->input . '</div>';
						}
					}
				?>
				</div>
			<?php endforeach; ?>
		</td>
	<?php endforeach; ?>
	<?php if (!empty($buttons)):?>
		<td>
			<div class="btn-group">
				<?php if (!empty($buttons['add'])):?><a class="group-add-1 btn btn-mini button"><span class="icon-plus"></span> </a><?php endif;?>
				<?php if (!empty($buttons['remove'])):?><a class="group-remove-1 btn btn-mini button"><span class="icon-minus"></span> </a><?php endif;?>
				<?php if (!empty($buttons['move'])):?><a class="group-move-1 btn btn-mini button"><span class="icon-menu"></span> </a><?php endif;?>
			</div>
		</td>
	<?php endif; ?>
</tr>
