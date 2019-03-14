<?php
namespace Germania\Pagination;

abstract class PaginationDecoratorAbstract implements PaginationInterface
{

	/**
	 * @var PaginationInterface
	 */
	public $pagination;



	/**
	 * @param PaginationInterface $pagination 
	 */
	public function __construct( PaginationInterface $pagination )
	{
		$this->pagination = $pagination;
	}


	/**
	 * @inheritDoc
	 */
	public function isActive() : bool
	{
		return $this->pagination->isActive();
	}



	/**
	 * @inheritDoc
	 */
	public function isDefaultPageSize() : bool
	{
		return $this->pagination->isDefaultPageSize();
	}



	/**
	 * @inheritDoc
	 */
	public function getPageSize( $use_default = true )
	{
		return $this->pagination->getPageSize( $use_default);
	}


	/**
	 * @inheritDoc
	 */
	public function setPageSize( $size )
	{
		$this->pagination->setPageSize( $size );
		return $this;
	}





	/**
	 * @inheritDoc
	 */
	public function getCurrent()
	{
		return $this->pagination->getCurrent();
	}

	/**
	 * @inheritDoc
	 */
	public function setCurrent( $number )
	{
		$this->pagination->setCurrent( $number );
		return $this;		
	}





	/**
	 * @inheritDoc
	 */
	public function getPrevious()
	{
		return $this->pagination->getPrevious();
	}

	/**
	 * @inheritDoc
	 */
	public function getNext()
	{
		return $this->pagination->getNext();
	}




	/**
	 * @inheritDoc
	 */
	public function getFirst() : int
	{
		return $this->pagination->getFirst();
	}

	/**
	 * @inheritDoc
	 */
	public function getLast() : int
	{
		return $this->pagination->getLast();
	}

	/**
	 * @inheritDoc
	 */
	public function getPagesCount() : int
	{
		$this->pagination->getPagesCount();
	}








}