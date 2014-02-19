<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OphCiExamination_Episode_IOPHistory extends EpisodeSummaryWidget
{
	public function run()
	{
		$chart = $this->createWidget('FlotChart', array('chart_id' => 'iop-history-chart', 'legend_id' => 'iop-history-legend'))
			->configureXAxis(array('mode' => 'time'))
			->configureYAxis('mmHg', array('min' => 0, 'max' => 80))
			->configureSeries('Intraocular Pressure (right)', array('points' => array('show' => true), 'lines' => array('show' => true)))
			->configureSeries('Intraocular Pressure (left)', array('points' => array('show' => true), 'lines' => array('show' => true)));

		$events = $this->event_type->api->getEventsInEpisode($this->episode->patient, $this->episode);

		foreach ($events as $event) {
			if (($iop = $event->getElementByClass('Element_OphCiExamination_IntraocularPressure'))) {
				$timestamp = Helper::mysqlDate2JsTimestamp($iop->last_modified_date);
				$this->addIop($chart, $iop, $timestamp, 'right');
				$this->addIop($chart, $iop, $timestamp, 'left');
			}
		}

		$this->render('OphCiExamination_Episode_IOPHistory', array('chart' => $chart));
	}

	protected function addIop(FlotChart $chart, Element_OphCiExamination_IntraocularPressure $iop, $timestamp, $side)
	{
		$reading = $iop->{"{$side}_reading"};
		$instrument = $iop->{"{$side}_instrument"};

		if ($reading && $reading->value) {
			$chart->addPoint(
				"Intraocular Pressure ({$side})", $timestamp, $reading->value,
				"{$reading->value} mmHg ({$instrument->name})"
			);
		}
	}
}