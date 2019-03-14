<?php
namespace tests;

use Germania\Pagination\Pagination;
use Germania\Pagination\PaginationInterface;
use Germania\Pagination\PaginationExceptionInterface;
use Germania\Pagination\PaginationRangeException;
use Germania\Pagination\PaginationInvalidArgumentException;

class PaginationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @dataProvider providePaginationData
	 */
	public function testSimple( $items_count, $page_size, $max_page_size )
	{

		$sut = new Pagination( $items_count, $page_size, $max_page_size );

		// A "fresh" Pagination MUST NOT be "active"
		$this->assertFalse( $sut->isActive() );
		$this->assertTrue( $sut->isDefaultPageSize() );

		$this->assertNull( $sut->getCurrent() );
		$this->assertNull( $sut->getNext() );
		$this->assertNull( $sut->getPrevious() );

		$this->assertInternalType( "int", $sut->getFirst() );
		$this->assertInternalType( "int", $sut->getLast() );



		// Provokate some method calls
		$this->assertInternalType( "int", $sut->getPagesCount() );

		$this->assertInstanceOf( PaginationInterface::class, $sut );		
	}

	/**
	 * @dataProvider providePaginationData
	 */
	public function testPageSetting( $items_count, $page_size, $max_page_size )
	{
		$page = 1;

		$sut = new Pagination( $items_count, $page_size, $max_page_size );
		$sut->setCurrent( $page );

		$this->assertTrue( $sut->isActive() );
		$this->assertEquals( $page, $sut->getCurrent() );
		$this->assertInternalType( "int", $sut->getNext() );
		$this->assertInternalType( "int", $sut->getPrevious() );

	}



	/**
	 * @dataProvider providePaginationData
	 */
	public function testSetters( $items_count, $page_size, $max_page_size )
	{

		$sut = new Pagination( $items_count, $page_size, $max_page_size );

		$fluid_interface = $sut->setPageSize(18);
		$this->assertInstanceOf( PaginationInterface::class, $fluid_interface);

		$fluid_interface = $sut->setCurrent(0);
		$this->assertInstanceOf( PaginationInterface::class, $fluid_interface);

	}

	/**
	 * @dataProvider provideInvalidPageNumbers
	 */
	public function testInvalidPageNumber( $invalid_page_number, $exception_class )
	{

		$sut = new Pagination( 100, 20, 100 );

		$this->expectException( $exception_class );
		$sut->setCurrent($invalid_page_number);

	}

	/**
	 * @dataProvider provideInvalidPageSizes
	 */
	public function testInvalidPageSize( $invalid_size, $exception_class )
	{

		$sut = new Pagination( 100, 20, 100 );

		$this->expectException( $exception_class );
		$sut->setPageSize($invalid_size);

	}


	public function providePaginationData()
	{
		$items_count = 100;

		return array(
			[ $items_count, null, null ],
			[ $items_count,   20, null ],
			[ $items_count,   20,  200 ]
		);
	}


	public function provideInvalidPageNumbers()
	{
		return array(
			[ "foobar", PaginationInvalidArgumentException::class ],
			[ -99, PaginationRangeException::class ]
		);
	}

	public function provideInvalidPageSizes()
	{
		return array(
			[ "foobar", PaginationRangeException::class ],
			[ -99, PaginationRangeException::class ]
		);
	}
}