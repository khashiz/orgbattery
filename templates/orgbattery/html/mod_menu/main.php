<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}

// The menu class is deprecated. Use nav instead
?>
<div class="uk-visible@m">
    <nav class="uk-height-1-1 uk-flex uk-flex uk-flex-middle">
        <ul class="uk-padding-remove uk-margin-remove uk-flex uk-flex-center nav menu<?php echo $class_sfx; ?> mod-list"<?php echo $id; ?>>
            <?php foreach ($list as $i => &$item)
            {
                $class = 'item-' . $item->id;

                if ($item->id == $default_id)
                {
                    $class .= ' default';
                }

                if ($item->id == $active_id || ($item->type === 'alias' && $item->params->get('aliasoptions') == $active_id))
                {
                    $class .= ' current';
                }

                if (in_array($item->id, $path))
                {
                    $class .= ' active';
                }
                elseif ($item->type === 'alias')
                {
                    $aliasToId = $item->params->get('aliasoptions');

                    if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
                    {
                        $class .= ' active';
                    }
                    elseif (in_array($aliasToId, $path))
                    {
                        $class .= ' alias-parent-active';
                    }
                }

                if ($item->type === 'separator')
                {
                    $class .= ' divider';
                }

                if ($item->deeper)
                {
                    $class .= ' deeper';
                }

                if ($item->parent)
                {
                    $class .= ' parent';
                }

                echo '<li class="' . $class . '">';

                switch ($item->type) :
                    case 'separator':
                    case 'component':
                    case 'heading':
                    case 'url':
                        require JModuleHelper::getLayoutPath('mod_menu', 'main_' . $item->type);
                        break;

                    default:
                        require JModuleHelper::getLayoutPath('mod_menu', 'main_url');
                        break;
                endswitch;

                // The next item is deeper.
                if ($item->deeper)
                {
                    echo '<div data-uk-drop="offset:0;delay-hide:200;animation:uk-animation-slide-bottom-small;duration:200;pos:bottom-right;" class="headerDrop"><div class="uk-card uk-card-body uk-card-default uk-padding-remove uk-box-shadow-small"><ul class="uk-padding-remove uk-margin-remove nav-child unstyled small">';
                }
                // The next item is shallower.
                elseif ($item->shallower)
                {
                    echo '</li>';
                    echo str_repeat('</ul></div></div></li>', $item->level_diff);
                }
                // The next item is on the same level.
                else
                {
                    echo '</li>';
                }
            }
            ?>
        </ul>
    </nav>
</div>