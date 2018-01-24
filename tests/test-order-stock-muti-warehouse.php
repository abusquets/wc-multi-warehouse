<?php
/**
 * Class OrderStockToWarehousesTest
 *
 * @package Wc_Multi_Warehouse
 */

/**
 * Tetst order stock.
 */
class OrderStockToWarehousesTest extends WP_UnitTestCase {

	function test_sample() {
		$rows = array();
		$rows[] = array('meta_id'=>1,'meta_key'=>'warehouse_AA','meta_value'=>0);
		$rows[] = array('meta_id'=>2,'meta_key'=>'warehouse_BB','meta_value'=>0);
		$rows[] = array('meta_id'=>3,'meta_key'=>'warehouse_CC','meta_value'=>3);
		$repartiment = order_stock_to_warehouses_process($rows, 1);
		$this->assertTrue(count($repartiment) == 1);
		$this->assertTrue($repartiment[0]['quantity'] == 1);
		$this->assertTrue($repartiment[0]['meta_key'] == 'warehouse_CC');
		$this->assertTrue($repartiment[0]['meta_value'] == 2);

		$rows = array();
		$rows[] = array('meta_id'=>1,'meta_key'=>'warehouse_AA','meta_value'=>1);
		$rows[] = array('meta_id'=>2,'meta_key'=>'warehouse_BB','meta_value'=>0);
		$rows[] = array('meta_id'=>3,'meta_key'=>'warehouse_CC','meta_value'=>3);
		$repartiment = order_stock_to_warehouses_process($rows, 1);
		$this->assertTrue(count($repartiment) == 1);
		$this->assertTrue($repartiment[0]['quantity'] == 1);
		$this->assertTrue($repartiment[0]['meta_key'] == 'warehouse_AA');
		$this->assertTrue($repartiment[0]['meta_value'] == 0);

		$rows = array();
		$rows[] = array('meta_id'=>1,'meta_key'=>'warehouse_AA','meta_value'=>1);
		$rows[] = array('meta_id'=>2,'meta_key'=>'warehouse_BB','meta_value'=>1);
		$rows[] = array('meta_id'=>3,'meta_key'=>'warehouse_CC','meta_value'=>3);
		$repartiment = order_stock_to_warehouses_process($rows, 3);

		$this->assertTrue(count($repartiment) == 3);

		$this->assertEquals($repartiment[0]['quantity'], 1);
		$this->assertEquals($repartiment[0]['meta_key'], 'warehouse_AA');
		$this->assertEquals($repartiment[0]['meta_value'], 0);

		$this->assertEquals($repartiment[1]['quantity'], 1);
		$this->assertEquals($repartiment[1]['meta_key'], 'warehouse_BB');
		$this->assertEquals($repartiment[1]['meta_value'], 0);

		$this->assertEquals($repartiment[2]['quantity'], 1);
		$this->assertEquals($repartiment[2]['meta_key'], 'warehouse_CC');
		$this->assertEquals($repartiment[2]['meta_value'], 2);


		$rows = array();
		$rows[] = array('meta_id'=>1,'meta_key'=>'warehouse_AA','meta_value'=>1);
		$rows[] = array('meta_id'=>2,'meta_key'=>'warehouse_BB','meta_value'=>1);
		$rows[] = array('meta_id'=>3,'meta_key'=>'warehouse_CC','meta_value'=>3);
		$repartiment = order_stock_to_warehouses_process($rows, 3);

		$this->assertTrue(count($repartiment) == 3);

		$this->assertEquals($repartiment[0]['quantity'], 1);
		$this->assertEquals($repartiment[0]['meta_key'], 'warehouse_AA');
		$this->assertEquals($repartiment[0]['meta_value'], 0);

		$this->assertEquals($repartiment[1]['quantity'], 1);
		$this->assertEquals($repartiment[1]['meta_key'], 'warehouse_BB');
		$this->assertEquals($repartiment[1]['meta_value'], 0);

		$this->assertEquals($repartiment[2]['quantity'], 1);
		$this->assertEquals($repartiment[2]['meta_key'], 'warehouse_CC');
		$this->assertEquals($repartiment[2]['meta_value'], 2);


		$rows = array();
		$rows[] = array('meta_id'=>1,'meta_key'=>'warehouse_AA','meta_value'=>2);
		$rows[] = array('meta_id'=>2,'meta_key'=>'warehouse_BB','meta_value'=>0);
		$rows[] = array('meta_id'=>3,'meta_key'=>'warehouse_CC','meta_value'=>1);
		$repartiment = order_stock_to_warehouses_process($rows, 3);

		$this->assertTrue(count($repartiment) == 2);

		$this->assertEquals($repartiment[0]['quantity'], 2);
		$this->assertEquals($repartiment[0]['meta_key'], 'warehouse_AA');
		$this->assertEquals($repartiment[0]['meta_value'], 0);

		$this->assertEquals($repartiment[1]['quantity'], 1);
		$this->assertEquals($repartiment[1]['meta_key'], 'warehouse_CC');
		$this->assertEquals($repartiment[1]['meta_value'], 0);

		$rows = array();
		$rows[] = array('meta_id'=>71806,'meta_key'=>'warehouse_DARO','meta_value'=>3);
		$rows[] = array('meta_id'=>71807,'meta_key'=>'warehouse_TWINS','meta_value'=>4);
		$repartiment = order_stock_to_warehouses_process($rows, 1);
		print_r($repartiment);
		$this->assertTrue(count($repartiment) == 1);

		$this->assertEquals($repartiment[0]['quantity'], 1);
		$this->assertEquals($repartiment[0]['meta_key'], 'warehouse_DARO');
		$this->assertEquals($repartiment[0]['meta_value'], 2);

	}
}
