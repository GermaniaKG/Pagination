<?php
namespace Germania\Pagination;

class PaginationIterator implements \IteratorAggregate, \Countable
{

	/**
	 * @var Traversable
	 */
	public $items;

	/**
	 * @var PaginationInterface
	 */
	public $pagination;


	/**
	 * @param \Traversable        $items
	 * @param PaginationInterface $pagination
	 */
	public function __construct( \Traversable $items, PaginationInterface $pagination)
	{
		$this->pagination = $pagination;
		$this->items = $items;
	}


	public function getIterator()
	{
		if ($this->pagination->isActive()):

	    	$offset = $this->calculateOffset();
	    	$length = $this->calculateLength();
	    	return new \LimitIterator( new \IteratorIterator($this->items), $offset, $length);

	    endif;

	    return $this->items;
	}


	public function count()
	{
		$iterator = $this->getIterator();
		return iterator_count( $iterator );
	}



	protected function calculateOffset()
	{
		$current_page_size = $this->pagination->getPageSize();
		$page_number = $this->pagination->getCurrent();

		return $current_page_size * $page_number;
	}


	protected function calculateLength()
	{
		return $this->pagination->getPageSize();
	}
}