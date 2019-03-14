<?php
namespace tests;

use Germania\Pagination\PaginationIterator;
use Germania\Pagination\PaginationInterface;

class PaginationIteratorTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @dataProvider providePaginationResults
	 */
	public function testSimple( $active )
	{

		$pi = $this->prophesize( PaginationInterface::class );
		$pi->isActive()->willReturn( $active );
		$pi->getPageSize()->willReturn( 25 );
		$pi->getCurrent()->willReturn( 10 );

		$sut = new PaginationIterator( new \ArrayObject, $pi->reveal() );	

		$this->assertInstanceOf( \Countable::class, $sut);
		$this->assertInstanceOf( \Traversable::class, $sut);
		$this->assertInstanceOf( \Traversable::class, $sut->getIterator() );

		$this->assertEquals( count($sut), iterator_count( $sut->getIterator() ));

	}

	public function providePaginationResults()
	{
		return array(
			[ true ],
			[ false ],
		);
	}
}