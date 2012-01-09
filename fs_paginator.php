<?php

/**
 * Flexible pagination class
 * 
 * @author Kevin Bradwick <kbradwick@gmail.com>
 *
 */

class FS_Paginator
{
	/**
	 * Page variable in $_GET
	 * @var string
	 */
	public $pageVar = 'page';
	
	
	/**
	 * Page limit - default to 10 items per page
	 * @var integer
	 */
	public $pageLimit = 10;
	
	
	/**
	 * Links base url
	 * @var string
	 */
	public $baseUrl = '';
	
	
	/**
	 * The class for the outer div element
	 * @var string
	 */
	public $containerClass = 'pagination';
	
	
	/**
	 * The ID of the container tag
	 * @var string
	 */
	public $containerID = 'fs_paginator';
	
	
	/**
	 * Next Link
	 * @var string
	 */
	public $nextLink = 'Next &#187;';
	
	
	/**
	 * Previous link
	 * @var string
	 */
	public $prevLink = 'Previous &#171;';
	
	
	/**
	 * Total numbe of items
	 * @var integer
	 */
	public $totalItems = 0;
	
	
	/**
	 * Additional page parameters
	 * @var array
	 */
	public $pageParams = array();
	
	
	/**
	 * The link range i.e. how many links are
	 * displayed at any one time
	 * @var integer
	 */
	public $range = 5;
	
	
	/**
	 * Optionally set config in the class construct
	 * 
	 * @access	public
	 * @param 	array $config
	 * @return	null
	 */
	public function __construct($config = array())
	{
		// set class vars if passed in config array
		if(count($config) > 0) {
			foreach($config as $key => $val) {
				$keys = array_keys(get_class_vars(__CLASS__));
				if(in_array($key, $keys)) {
					$this->$key = $val;
				}
			}
		}
		
		if(empty($this->baseUrl))
			$this->baseUrl = $_SERVER['PHP_SELF'];
	}
	
	
	
	/**
	 * Get's the current page number
	 * 
	 * @access	public
	 * @return	integer
	 */
	public function getCurrentPage()
	{
		$page = isset($_GET[$this->pageVar]) ? (int)$_GET[$this->pageVar] : 1;
		if($page > $this->totalPages())
			return $this->totalPages();
		else
			return $page;
	}
	
	
	
	/**
	 * The total number of possible pages
	 * 
	 * @access	public
	 * @return	integer
	 */
	public function totalPages()
	{
		return (int)ceil($this->totalItems / $this->pageLimit);
	}
	
	
	
	/**
	 * Render the pagination HTML
	 * 
	 * @access	public
	 * @param	boolean $echo the results or return them
	 * @return	null
	 */
	public function render($echo = true)
	{
		if($this->totalPages()===1)
			return '';
			
		$html = '<div class="'.$this->containerClass.'" id="'.$this->containerID.'">';
		
		$nextPage = $this->getCurrentPage() + 1;
		$prevPage = $this->getCurrentPage() - 1;
		
		// show previous link if not on page one
		if($this->getCurrentPage()>1) {
			$html .= '<a href="'.$this->createLink(array( $this->pageVar =>1)).'">Beginning</a>';
			$html .= '<a href="'.$this->createLink(array( $this->pageVar => $prevPage)).'">'.$this->prevLink.'</a>';
		}
		
		// loop through page links
		$pages = array();
		for($i=1;$i<=$this->totalPages();$i++) {
			if($this->getCurrentPage()===$i)
				$pages[] = '<span class="current">'.$i.'</span>';
			else
				$pages[] = '<a href="'.$this->createLink(array( $this->pageVar => $i)).'">'.$i.'</a>';	
		}
		
		if($this->totalPages() <= $this->range) {
			$html .= implode('', $pages);
		} else {
			if($this->getCurrentPage() <= (intval(floor($this->getPageRange() / 2)) + 1)) {
				$html .= implode('',array_slice($pages, 0, $this->getPageRange()));
			} elseif($this->getCurrentPage() >= ($this->totalPages() - floor($this->getPageRange()/2)+1)) {
				$html .= implode('',array_slice($pages, ($this->totalPages() - $this->getPageRange()), $this->getPageRange()));
			} else {
				$left = intval(floor($this->getPageRange()/2));
				$html .= implode('',array_slice($pages, ($this->getCurrentPage()-$left)-1,$this->getPageRange()));
			}
		}
		
		// show next link if page num is less than total
		if($this->getCurrentPage()<$this->totalPages()) {
			$html .= '<a href="'.$this->createLink(array( $this->pageVar => $nextPage)).'">'.$this->nextLink.'</a>';
			$html .= '<a href="'.$this->createLink(array( $this->pageVar => $this->totalPages())).'">Last</a>';
		}
			
		$html .= '</div>';
		
		
		// echo/return the HTML
		if($echo===true)
			echo $html;
		elseif($echo===false)
			return $html;
	}
	
	
	
	/**
	 * Returns the page range. This will always return an odd number as the link
	 * that are rendered centre around the middle.
	 * 
	 * @access	public
	 * @return	integer
	 */
	public function getPageRange()
	{
		return ($this->range & 1) ? $this->range : $this->range + 1;
	}
	
	
	
	/**
	 * Additional page parameters to build the GET query
	 * 
	 * @access	public
	 * @param 	array $params
	 * @return	null
	 */
	public function pageParams($params = array())
	{
		$this->pageParams = array_merge($this->pageParams,$params);
	}
	
	
	
	/**
	 * Creates a link.
	 * 
	 * This will use the $pageParams by default. Additional parameters
	 * set in $params will get merged together.
	 * 
	 * @access	public
	 * @param 	array $params
	 * @return	string
	 */
	private function createLink($params = array())
	{
		$parameters = array_merge($this->pageParams,$params);
		return $this->baseUrl.'?'.http_build_query($parameters);
	}
}