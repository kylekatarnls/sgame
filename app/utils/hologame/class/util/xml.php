<?php

namespace Hologame;

// DIA Maps Handling class - Simple XML Parser
// Copyright (C) 2007 Mateusz Golicz <mateusz.golicz@sileman.pl>
// Parts of code courtesy of <geoffers [at] gmail [dot] com>
// Released under conditions of GNU General Public License version 2 
// See http://www.gnu.org/licenses/gpl.txt for more information

class Util°Xml
{
	var $parser;
	var $error_code;
	var $error_string;
	var $current_line;
	var $current_column;
	var $data = array();
	var $datas = array();
   
	function parse($data, $encoding = 'UTF-8')
	{
		$this->parser = xml_parser_create($encoding);
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
		xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
		xml_set_character_data_handler($this->parser, 'cdata');
		if (!xml_parse($this->parser, $data))
		{
			$this->data = array();
			$this->error_code = xml_get_error_code($this->parser);
			$this->error_string = xml_error_string($this->error_code);
			$this->current_line = xml_get_current_line_number($this->parser);
			$this->current_column = xml_get_current_column_number($this->parser);
		}
		else
		{
			$this->data = $this->data['child'];
		}
		xml_parser_free($this->parser);
		return $this->data;
	}

	function tag_open($parser, $tag, $attribs)
	{
		$na = array();
		foreach($attribs as $k => $v)
			$na[strtoupper($k)] = $v; // Just in case
			
		$tag = strtoupper($tag); // Just in case
		$this->data['child'][$tag][] = array('data' => '', 'attribs' => $na, 'child' => array());
		$this->datas[] =& $this->data;
		$this->data =& $this->data['child'][$tag][count($this->data['child'][$tag])-1];
	}

	function cdata($parser, $cdata)
	{
		$this->data['data'] .= $cdata;
	}

	function tag_close($parser, $tag)
	{
		$this->data =& $this->datas[count($this->datas)-1];
		array_pop($this->datas);
	}
	
	function serialize_tag($name, $data)
	{
		$name = strtolower($name);
		$out = "<$name";
		foreach($data["attribs"] as $k => $v)
		{
			$k = strtolower($k);
			$v = strtr($v, array("&" => "&amp;", "\"" => "&quot;"));
			$out .= " $k=\"$v\"";
		}
		
		
		$dat = trim($data["data"]);
		
		if(count($data["child"]) > 0 || strlen($dat) > 0)
		{
			$out .= ">";

			if(strlen($dat) > 0)
				$out .= strtr($dat, array("<" => "&lt;", ">" => "&gt;"));
			else
				$out .= "\n";
			
			
			foreach($data["child"] as $type => $items)
				foreach($items as $item)
				{
					$out .= $this->serialize_tag($type, $item);
				}
			
			
			$out .= "</$name>\n";
		}
		else
			$out .= "/>\n";

		return $out;
	}
	
	function serialize()
	{
		$out = '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' . "\n";
		
		foreach($this->data as $type => $items)
			foreach($items as $item)
			{
				$out .= $this->serialize_tag($type, $item);
			}
		return $out;
	}
}


?>