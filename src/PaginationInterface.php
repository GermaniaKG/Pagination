<?php
namespace Germania\Pagination;

interface PaginationInterface
{


    /**
     * Checks if user picked a page.
     * TRUE if so, FALSE if no page number set.
     * 
     * @return boolean
     */
    public function isActive() : bool;	


    /**
     * Checks if custom page size (other than set with constructor) is used.
     * 
     * @return boolean
     */
    public function isDefaultPageSize() : bool;

    
    /**
     * Returns the current page number or NULL.
     * 
     * @return int|null
     */
    public function getCurrent();	


    /**
     * Sets the current page number.
     * @param int $number
     * @return PaginationInterface
     */
    public function setCurrent( $number );


    /**
     * Returns the previous page number or NULL
     * 
     * @return int|null
     */
    public function getPrevious();


    /**
     * Returns the next page number or NULL.
     * 
     * @return int|null
     */
    public function getNext();


    /**
     * Returns the first page number.
     * 
     * @return int
     */
    public function getFirst() : int;


    /**
     * Returns the last page number.
     * 
     * @return int
     */
    public function getLast() : int;	


    /**
     * Returns the number of items on a page
     * @return int|null
     */
    public function getPageSize();	


    /**
     * Sets the number of items on a page
     * @param int $site
     * @return  PaginationInterface
     */
	public function setPageSize( $size );


    /**
     * Returns the total number of pages
     * @return int
     */
    public function getPagesCount() : int;	

}	