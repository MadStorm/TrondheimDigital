<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->addStylesheet('modules/mod_counters/assets/css/counters.css');
$document->addScript('modules/mod_counters/assets/js/jquery.countdown.min.js');
$count = $params->get('max_dim') - $params->get('min_dim');
?>

<div id="counters_<?php echo $module->id; ?>" class="counters counters__<?php echo $moduleclass_sfx ?> cols-<?php echo $count+1 ?>">
	<div class="row-fluid">
		<?php $html = "";
		$script = "";
		for($i = $count; $i >=0; $i--){
			switch($i+$params->get('min_dim')){
				case 5:
					$title = JText::_('MOD_COUNTERS_YEARS');
					$class = "years";
					$number = "event.strftime('%-Y')";
			   		$progress = "Math.floor(event.strftime('%-Y')*180/10)";
					break;
				case 4:
					$title = JText::_('MOD_COUNTERS_MONTHS');
					$class = "months";
					$number = "event.strftime('%-m')%12";
			   		$progress = "Math.floor((event.strftime('%-m')%12)*180/12)";
					break;
				case 3:
					$title = JText::_('MOD_COUNTERS_DAYS');
					$class = "days";
					$number = "event.strftime('%-D')";
			   		$progress = "Math.floor((event.strftime('%-D')%30)*180/30)";
					break;
				case 2:
					$title = JText::_('MOD_COUNTERS_HOURS');
					$class = "hours";
					$number = "event.strftime('%-H')";
			   		$progress = "Math.floor(event.strftime('%-H')*180/24)";
					break;
				case 1:
					$title = JText::_('MOD_COUNTERS_MINUTES');
					$class = "minutes";
					$number = "event.strftime('%-M')";
			   		$progress = "Math.floor(event.strftime('%-M')*180/60)";
					break;
				case 0:
					$title = JText::_('MOD_COUNTERS_SECONDS');
					$class = "seconds";
					$number = "event.strftime('%-S')";
			   		$progress = "Math.floor(event.strftime('%-S')*180/60)";
					break;
				default:
	    			$title = "";
					$class = "";
					$number = "";
			   		$progress = "";
			}
			$html .= "<div class=\"span\">
			<div class=\"radial-progress radial-progress__".$class."\">
				<div class=\"circle\">
					<div class=\"mask full\">
						<div class=\"fill\"></div>
					</div>
					<div class=\"mask half\">
						<div class=\"fill\"></div>
						<div class=\"fill fix\"></div>
					</div>
				</div>
				<div class=\"inset\">
					<div class=\"percentage\">
						<div class=\"numbers\">
							<span>-</span>";
							for($z = 0;$z<100;$z++){
								$html .= '<span>'.$z.'</span>';
							}
						$html .= "</div>
					</div>
					<div class=\"title\">".$title."</div>
				</div>
				</div>
			</div>\n";
			$script .= "$('#counters_".$module->id." div.radial-progress__".$class."').countdown('".JHtml::_('date', $params->get('date'), 'Y/m/d H:i:s')."', function(event) {
			   $(this).attr({
			   	'data-number': ".$number.",
			   	'data-progress': ".$progress."
			   });
			});
			";
		}
		echo $html; ?>
	</div>
</div>
<script>
	jQuery(function($){
		<?php echo $script; ?>
	})
</script>