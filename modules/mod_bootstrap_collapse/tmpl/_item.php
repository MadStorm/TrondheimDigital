<?php
/**
 * Bootstrap Collapse
 *
 * @author    TemplateMonster http://www.templatemonster.com
 * @copyright Copyright (C) 2012 - 2013 Jetimpex, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 
 * Parts of this software are based on Articles Newsflash standard module
 * 
*/
defined('_JEXEC') or die;
	$item_heading = $params->get('item_heading', 'h4');
	$item_images = json_decode($item->images);
?>
	<!-- Item title -->
	<div class="panel-heading" role="tab" id="heading_<?php echo $item->id; ?>">
		<a class="<?php if($i!=0) echo 'collapsed'; ?>" role="button" href="#collapse_<?php echo $module->id; ?>_<?php echo $item->id; ?>" data-toggle="collapse" data-parent="#accordion<?php echo $module->id; ?>" aria-expanded="false" aria-controls="heading_<?php echo $item->id; ?>">
			<span class="panel-heading_icon">
			<?php  if ((!isset($item_images->image_intro) || empty($item_images->image_intro)) && $item_images->image_intro_caption) : ?>
			<i class="<?php echo $item_images->image_intro_caption; ?>"></i>
			<?php endif; ?>
			</span>
			<?php echo $item->title;?>
		</a>
	</div>

	<div id="collapse_<?php echo $module->id; ?>_<?php echo $item->id; ?>" class="panel-collapse collapse <?php if($i==0) echo 'in'; ?>" role="tabpanel" aria-labelledby="#collapse_<?php echo $module->id; ?>_<?php echo $item->id; ?>">
	<div class="panel-body">

<!-- Intro Image -->
<?php if ($params->get('intro_image')): ?>
	<?php  if (isset($item_images->image_intro) and !empty($item_images->image_intro)) : ?>
	<?php $imgfloat = (empty($item_images->float_intro)) ? $params->get('float_intro') : $item_images->float_intro; ?>
	<div class="item_img img-intro img-intro__<?php echo htmlspecialchars($params->get('intro_image_align')); ?>">
		<img src="<?php echo htmlspecialchars($item_images->image_intro); ?>" alt="<?php echo htmlspecialchars($item_images->image_intro_alt); ?>"/></a>
		<?php if ($item_images->image_intro_caption): ?>
		<figcaption><?php echo htmlspecialchars($item_images->image_intro_caption); ?></figcaption>
		<?php endif; ?>
	</div>
	<?php endif; ?>
<?php endif; ?>


	<?php if (!$params->get('intro_only')) :
		echo $item->afterDisplayTitle;
	endif; ?>

	<?php if ($params->get('show_tags', 1) && !empty($item->tags)) : ?>
		<?php $item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>

		<?php echo $item->tagLayout->render($item->tags->itemTags); ?>
	<?php endif; ?>

	<?php if ($params->get('published')) : ?>
		<div class="item_published">
			<?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC1')); ?>
		</div>
	<?php endif; ?>

	<?php if ($params->get('createdby')) : ?>
		<div class="item_createdby">
			<?php $author = $item->author; ?>
			<?php $author = ($item->created_by_alias ? $item->created_by_alias : $author); ?>
				<?php echo JText::sprintf('MOD_BOOTSTRAP_COLLAPSE_BY', $author); ?>
		</div>
	<?php endif; ?>

	<?php echo $item->beforeDisplayContent; ?>

	<!-- Introtext -->
	<?php echo $item->introtext; ?>

	<!-- Read More link -->
	<?php if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) :
		$readMoreText = JText::_('MOD_BOOTSTRAP_COLLAPSE_READMORE');
			if ($item->alternative_readmore){
				$readMoreText = $item->alternative_readmore;
			}
		echo '<a class="btn btn-info readmore" href="'.$item->link.'"><span>'. $readMoreText .'</span></a>';
	endif; ?>

</div>
</div>