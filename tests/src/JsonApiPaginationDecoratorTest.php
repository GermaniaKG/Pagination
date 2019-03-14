<?php
namespace tests;

use Germania\Pagination\Pagination;
use Germania\Pagination\JsonApiPaginationDecorator;
use Germania\Pagination\PaginationInterface;
use Germania\Pagination\PaginationExceptionInterface;
use Germania\Pagination\PaginationRangeException;
use Germania\Pagination\PaginationInvalidArgumentException;

class JsonApiPaginationDecoratorTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @dataProvider providePaginationData
	 */
	public function testSimple( $items_count, $page_size, $max_page_size, $filter_result )
	{

		$pagination = new Pagination( $items_count, $page_size, $max_page_size );

		$uri = \GuzzleHttp\Psr7\uri_for('http://example.com');

		$sut = new JsonApiPaginationDecorator( $pagination, $uri, [], $filter_result );

		$this->assertInstanceOf( PaginationInterface::class, $sut );		
		$this->assertInstanceOf( \JsonSerializable::class, $sut );		

		$this->assertInternalType("array", $sut->getLinks());
		$this->assertInternalType("array", $sut->getMeta());
	}

	/**
	 * @dataProvider providePaginationData
	 */
	public function testWithActivePagination( $items_count, $page_size, $max_page_size, $filter_result  )
	{

		$pagination = new Pagination( $items_count, $page_size, $max_page_size );
		$pagination->setCurrent(0);

		$uri = \GuzzleHttp\Psr7\uri_for('http://example.com');

		$sut = new JsonApiPaginationDecorator( $pagination, $uri, [], $filter_result );

		$links = $sut->getLinks();
		$this->assertEquals( $links, $sut->JsonSerialize() );

		$meta = $sut->getMeta();

		
	}



	public function providePaginationData()
	{
		$items_count = 100;

		return array(
			[ $items_count, null, null,   true ],
			[ $items_count,   20, null,   true ],
			[ $items_count,   20,  200,   true ],
			[ $items_count, null, null,   false ],
			[ $items_count,   20, null,   false ],
			[ $items_count,   20,  200,   false ],
		);
	}

}