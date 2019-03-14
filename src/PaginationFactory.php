<?php
namespace Germania\Pagination;

class PaginationFactory
{


	/**
	 * @var int
	 */
	public $default_page_size = 25;


	/**
	 * Default class FQDN for creating the Pagination instance 
	 * @var string
	 */
	public $default_pagination_class;



	/**
	 * @param int|integer $default_page_size Default: 25
	 * @param string|null $php_class         Default: \Germania\Pagination\Pagination
	 */
	public function __construct( int $default_page_size = 25, string $php_class = null )
	{
		$this->default_page_size = $default_page_size;
		$this->default_pagination_class = $php_class ?: Pagination::class;
	}


	/**
	 * @param  mixed        $items            The items to paginate: int, array or Traversable (countable)
	 * @param  int|array    $pagination_data  The data to construct the pagination with
	 * @param  string       $php_class        Optional: Custom pagination class FQDN
	 * @return 
	 */
	public function __invoke( $items, $pagination_data, string $custom_php_class = null )
	{
		$items_count = $this->countItems( $items );

		$klasse = $custom_php_class ?: $this->default_pagination_class;
		$pagination = new $klasse( $items_count, $this->default_page_size );


		// Eval user data
        if (is_numeric($pagination_data)):
            $pagination->setCurrent( $pagination_data );

        elseif (is_array($pagination_data)):
            $pagination->setCurrent( $pagination_data['number'] ?? 0);

            if (!empty($pagination_data['size'])):
            	$pagination->setPageSize( $pagination_data['size'] );
            endif;

        else:
        	throw new PaginationInvalidArgumentException("Integer or Array expected");

        endif;
		return $pagination;
	}


	/**
	 * Counts the items to determine the $items_count parameter for Pagination class.
	 * 
	 * @param   mixed $items
	 * @return  int
	 * @throws  PaginationInvalidArgumentException
	 */
	protected function countItems( $items )
	{
		if (is_array($items)):
			return count($items);

		elseif ($items instanceOf \Countable):
			return count($items);

		elseif ($items instanceOf \Traversable):
			return iterator_count($items);

		elseif (is_int($items)):
			return $items;

		else:
			throw new PaginationInvalidArgumentException("Countable, Traversable or integer expected");
		endif;

	}
}