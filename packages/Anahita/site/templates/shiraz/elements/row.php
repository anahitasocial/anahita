<?php
defined('KOOWA') or die;

class JElementRow extends JElement {

	function fetchElement($name, $value, &$node, $control_name)
	{
		$optionGroups = array(	
			array(
				'name' 		=> '1 Column',
				'options'	=> array(
					array( 'key'=>'1', 'value'=>'12')
				)
			),
			array(
				'name' 		=> '2 Column Ratios',
				'options'	=> array(
					array( 'key'=>'1-5', 'value'=>'2,10'),
					array( 'key'=>'1-3', 'value'=>'3,9'),
					array( 'key'=>'1-2', 'value'=>'4,8'),
					array( 'key'=>'1-1', 'value'=>'6,6'),
					array( 'key'=>'2-1', 'value'=>'8,4'),
					array( 'key'=>'3-1', 'value'=>'9,3'),
					array( 'key'=>'5-1', 'value'=>'10,2')
				)
			),
			array(
				'name' 		=> '3 Column Ratios',
				'options'	=> array(
					array( 'key'=>'1-1-4', 'value'=>'2,2,8'),
					array( 'key'=>'1-2-3', 'value'=>'2,4,6'),
					array( 'key'=>'1-3-2', 'value'=>'2,6,4'),
					array( 'key'=>'1-4-1', 'value'=>'2,8,2'),
					array( 'key'=>'1-1-2', 'value'=>'3,3,6'),
					array( 'key'=>'1-2-1', 'value'=>'3,6,3'),
					array( 'key'=>'2-1-3', 'value'=>'4,2,6'),
					array( 'key'=>'1-1-1', 'value'=>'4,4,4'),
					array( 'key'=>'2-3-1', 'value'=>'4,6,2'),
					array( 'key'=>'3-1-2', 'value'=>'6,2,4'),
					array( 'key'=>'2-1-1', 'value'=>'6,3,3'),
					array( 'key'=>'3-2-1', 'value'=>'6,4,2')
				)
			),
			array(
				'name' 		=> '4 Column Ratios',
				'options'	=> array(
					array( 'key'=>'1-1-1-3', 'value'=>'2,2,2,6'),
					array( 'key'=>'1-1-2-2', 'value'=>'2,2,4,4'),
					array( 'key'=>'1-1-3-1', 'value'=>'2,2,6,2'),
					array( 'key'=>'1-2-1-2', 'value'=>'2,4,2,4'),
					array( 'key'=>'1-2-2-1', 'value'=>'2,4,4,2'),
					array( 'key'=>'1-3-1-1', 'value'=>'2,6,2,2'),
					array( 'key'=>'1-1-1-1', 'value'=>'3,3,3,3'),
					array( 'key'=>'2-1-1-2', 'value'=>'4,2,2,4'),
					array( 'key'=>'2-1-2-1', 'value'=>'4,2,4,2'),
					array( 'key'=>'2-2-1-1', 'value'=>'4,4,2,2')
				)
			)
		);
		
		$html = '<select name="params['.$name.']" id="params'.$name.'" class="inputbox">';
		
		foreach($optionGroups as $group)
		{
			$html .= '<optgroup label="'.$group['name'].'">';
			
			foreach($group['options'] as $option)
			{
				$selected = ($option['value'] == $value ) ? 'selected' : '';
				$html .= '<option '.$selected.' value="'.$option['value'].'">'.$option['key'].'</option>';
			}
					
			$html .= '</optgroup>';
		}
			
		$html .= '</select>';
		
		return $html;
	}
}
