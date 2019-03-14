<?php
namespace tests;

use Germania\Pagination\Pagination;
use Germania\Pagination\PaginationFactory;
use Germania\Pagination\PaginationInterface;
use Germania\Pagination\PaginationExceptionInterface;
use Germania\Pagination\PaginationInvalidArgumentException;

class PaginationFactoryTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @dataProvider provideValidPaginationFactoryData
	 */
	public function testInstantiation( $default_page_size, $php_class, $items, $pagination_data )
	{
		$sut = new PaginationFactory($default_page_size, $php_class);	

		$this->assertTrue( is_callable($sut));

		return $sut;
	}


	/**
	 * @dataProvider provideValidPaginationFactoryData
	 */
	public function testFactory( $default_page_size, $php_class, $items, $pagination_data )
	{
		$sut = new PaginationFactory($default_page_size, $php_class);	

		$result = $sut( $items, $pagination_data, $php_class);
		$this->assertInstanceOf(PaginationInterface::class, $result);
	}



	/**
	 * @dataProvider provideInvalidPaginationFactoryData
	 */
	public function testInvalidArgumentOnFactory( $default_page_size, $php_class, $items, $pagination_data )
	{
		$sut = new PaginationFactory($default_page_size, $php_class);	

		$this->expectException( \InvalidArgumentException::class );
		$this->expectException( PaginationExceptionInterface::class );
		$this->expectException( PaginationInvalidArgumentException::class );

		$sut( $items, $pagination_data, $php_class);
	}



	public function provideValidPaginationFactoryData()
	{
		$num_of_items = 200;
		$items_array = range(0, $num_of_items );
		$items = new \ArrayObject( $items_array );
		$traversable = new \LimitIterator(new \IteratorIterator($items), 0, -1);
		$page = 2;

		return array(
			[ 25, Pagination::class, $items,           2 ],
			[ 25, Pagination::class, $items_array,     2 ],
			[ 25, Pagination::class, $traversable,     2 ],
			[ 25, Pagination::class, $num_of_items,    2 ],
			[ 25, null,              $items, array('number' => $page )],
			[ 25, null,              $items_array,     array('number' => $page )],
			[ 25, null,              $items, array('number' => 0 )],
			[ 25, null,              $items, array('number' => "0", "size"=>5 )],
			[ 25, null,              $items, array("size"=>5 )],
			[ 25, null,              $items, "0"]
		);
	}

	public function provideInvalidPaginationFactoryData()
	{
		$num_of_items = 200;
		$items_array = range(0, $num_of_items );
		$items = new \ArrayObject( $items_array );
		$traversable = new \LimitIterator(new \IteratorIterator($items), 0, -1);
		$invalid_page = "invalid_number";
		$valid_page   = 2;

		return array(
			[ 25, Pagination::class, $items, $invalid_page ],
			[ 25, Pagination::class, "foo",  $valid_page ],
		);
	}
}