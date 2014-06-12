<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="element-data row">
	<ul>
		<li><?php echo $element->eye ? $element->eye->name : 'Eye no specified'?></li>
		<?php if ($element->city_road) {?>
			<li>At City Road</li>
		<?php }?>
		<?php if ($element->satellite) {?>
			<li>At satellite</li>
		<?php }?>
		<?php if ($element->fast_track) {?>
			<li>Suitable for fast-track</li>
		<?php }?>
		<li>
			Target post-op refractive correction is <?php echo $element->target_postop_refraction?> Dioptres
		</li>
		<?php if ($element->correction_discussed) {?>
			<li>Post-op refractive correction has been discussed with the patient</li>
		<?php } else {?>
			<li>Post-op refractive correction has not been discussed with the patient</li>
		<?php }?>
		<li>
			Suitable for <?php echo $element->suitable_for_surgeon->name?> surgeon (<?php echo $element->supervised ? 'supervised' : 'unsupervised'?>)
		</li>
		<li>
			<?php echo $element->vitrectomised_eye ? 'Vitrectomised eye' : 'Non-vitrectomised eye'?>
		</li>
	</ul>
</div>
