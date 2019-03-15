<?php
namespace Germania\Pagination;

class Pagination implements PaginationInterface
{


	/**
	 * The total number of items to paginate
	 * @var int
	 */
	public $items_count;

	/**
	 * The current page number
	 * @var int|null
	 */
	public $page_number;

	/**
	 * The number of page items in use
	 * @var int
	 */
	public $page_size;

	/**
	 * The default number of items on a page
	 * @var int
	 */
	protected $default_page_size = 25;

	/**
	 * The maximum page length
	 * @var int
	 */
	protected $max_page_size = 100;




	/**
	 * @param int  $items_count The total number of items to paginate
	 * @param int  $page_size   Optional: number of items on a single page, default: 25
	 * @param int  $page_size   Optional: Maximum number of items on a single page, default: 100
	 */
	public function __construct( int $items_count, int $page_size = null, int $max_page_size = null )
	{
		$this->items_count = $items_count;
		$this->page_size = $page_size ?: $this->default_page_size;
		$this->default_page_size = $this->page_size;
		$this->max_page_size = $max_page_size ?: $this->max_page_size;
	}


	/**
	 * @inheritDoc
	 */
	public function isActive() : bool
	{
		return ($this->getCurrent() !== null);
	}


	/**
	 * @inheritDoc
	 */
	public function isDefaultPageSize() : bool
	{
		return $this->getPageSize() === $this->default_page_size;
	}





	/**
	 * @inheritDoc
	 */
	public function getPagesCount() : int
	{
		return ceil( $this->items_count / $this->getPageSize( ));
	}


	/**
	 * @inheritDoc
	 */
	public function getPageSize()
	{
		return $this->page_size;
	}
	

	/**
	 * @inheritDoc
	 * @throws PaginationRangeException When page size not between 1 and $max_page_size
	 */
	public function setPageSize( $size )
	{
        $filter_options = array("options" => [
        	"min_range" => 1, 
        	"max_range" => $this->max_page_size
    	]);

		if (filter_var($size, FILTER_VALIDATE_INT, $filter_options) === false):
	    	$msg = sprintf("Invalid Page size (max. %s)", $this->max_page_size);
	    	throw new PaginationRangeException($msg, 400);
		endif;

		$this->page_size = $size;
		return $this;
	}





	/**
	 * @inheritDoc
	 */
	public function getCurrent()
	{
		return $this->page_number;
	}



	/**
	 * @inheritDoc
	 * @throws PaginationInvalidArgumentException when page number is not integer
	 * @throws PaginationRangeException           when page number does not exists
	 */
	public function setCurrent( $number )
	{
        $min_page_number = $this->getFirst();
        $max_page_number = $this->getLast();

		if (filter_var($number, FILTER_VALIDATE_INT) === false):
	    	$msg = sprintf("Integer (%s to %s) expected", $min_page_number, $max_page_number);
	    	throw new PaginationInvalidArgumentException($msg);
		endif;

        $filter_options = array("options" => [
        	"min_range" => $min_page_number, 
        	"max_range" => $max_page_number
    	]);

		if (filter_var($number, FILTER_VALIDATE_INT, $filter_options) === false):
	    	$msg = sprintf("Invalid Page number (allowed: %s to %s)", $min_page_number, $max_page_number);
	    	throw new PaginationRangeException($msg, 400);
		endif;

		$this->page_number = $number;		
		return $this;		

	}





	/**
	 * @inheritDoc
	 */
	public function getPrevious()
	{
		$page_number = $this->getCurrent();
		if (!$this->isActive()):
			return null;
		endif;

		return $page_number > $this->getFirst()
		? $page_number - 1
		: null;
	}



	/**
	 * @inheritDoc
	 */
	public function getNext()
	{
		$page_number = $this->getCurrent();
		if (!$this->isActive()):
			return null;
		endif;

		return $page_number < $this->getLast()
		? $page_number + 1
		: null;
	}




	/**
	 * @inheritDoc
	 */
	public function getFirst() : int
	{
		return 0;
	}

	/**
	 * @inheritDoc
	 */
	public function getLast() : int
	{
		return ceil( $this->items_count / $this->getPageSize( )) - 1;
	}

}