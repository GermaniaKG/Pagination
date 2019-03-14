<?php
namespace Germania\Pagination;

use Psr\Http\Message\UriInterface;

class JsonApiPaginationDecorator extends PaginationDecoratorAbstract implements  \JsonSerializable
{

	/**
	 * @var UriInterface
	 */
	public $uri;

	/**
	 * @var array
	 */
	public $query_params = array();

	/**
	 * @var array
	 */
	public $json_result = array();

	/**
	 * @var array
	 */
	public $filter_result = false;


	/**
	 * @param PaginationInterface $pagination    The pagination to decorate
	 * @param UriInterface        $uri           PSR-7 URI instance
	 * @param array               $query_params  Optional: default query parameters for URIs
	 * @param bool                $filter_result Optional: Filter out null member values in jsonSerialize's result. 
	 *                                           Default: FALSE
	 */
	public function __construct( PaginationInterface $pagination, UriInterface $uri, array $query_params = array(), bool $filter_result = false )
	{
		$this->uri = $uri;
		$this->query_params = $query_params;
		$this->filter_result = $filter_result;

   
		$this->json_result['first'] = null;
		$this->json_result['prev']  = null;
		$this->json_result['next']  = null;
		$this->json_result['last']  = null;		

		parent::__construct($pagination);
	}


	/**
	 * JsonSerializable alias for getLinks()
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->getLinks();
	}


	/**
	 * Returns an array with `first`, `last`, `next`, and `previous` elements
	 * according to the JsonAPI pagination specs.
	 * 
	 * @return array
	 * @see    https://jsonapi.org/format/#fetching-pagination
	 */
	public function getLinks() : array
	{
		if (!$this->pagination->isActive()):
			return $this->json_result;
		endif;

		$page_size = $this->isDefaultPageSize() ? null : $this->pagination->getPageSize( );

        $this->buildField('first', $this->pagination->getFirst(),    $page_size);
        $this->buildField('prev',  $this->pagination->getPrevious(), $page_size);
        $this->buildField('next',  $this->pagination->getNext(),     $page_size);
        $this->buildField('last',  $this->pagination->getLast(),     $page_size);

        return $this->filter_result 
        ? array_filter($this->json_result)
        : $this->json_result;		
	}

	/**
	 * Returns an array with non-standard meta information about the pagination
	 *
	 * - numberOfPages
	 * - currentPage
	 * - pageSize
	 * 
	 * @return array
	 */
	public function getMeta() : array
	{
		if (!$this->pagination->isActive()):
			return array();
		endif;

		return array(
        	'numberOfPages' => $this->pagination->getPagesCount(),
        	'currentPage'   => $this->pagination->getCurrent(),
        	'pageSize'      => $this->pagination->getPageSize()
		);
	}




	/**
	 * @param  string   $field     Link element field name
	 * @param  int      $page      Page number
	 * @param  int|null $page_size Page size
	 * @return void
	 */
	protected function buildField($field, $page, $page_size) {

		$notnullfilter = function($i) { return !is_null($i); };

		if (is_null($page)):
			$this->json_result[$field] = null;
		else:
			$pagination_query_params = array(
				'page' => array_filter(['size' => $page_size, 'number' => $page], $notnullfilter) 
			);
			$this->json_result[$field] = $this->buildUriString( $pagination_query_params );
		endif;
	}


    /**
     * @param  array $custom_params
     * @return string
     */
    protected function buildUriString( array $custom_params) : string
    {
    	$params = array_merge($this->query_params, $custom_params);
    	$query = http_build_query( $params );
        return (string) $this->uri->withQuery( $query );
    }

}