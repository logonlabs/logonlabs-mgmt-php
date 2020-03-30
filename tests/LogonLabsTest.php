<?php

use PHPUnit\Framework\TestCase;

class LogonLabsTest extends TestCase {

    protected function paginations($response) {
        $this->assertArrayNotHasKey('error', $response);
        $this->assertIsIterable($response);
        $this->assertIsIterable($response['results']);
        $this->assertIsNumeric($response['page_size']);
        $this->assertIsNumeric($response['current_page']);
        $this->assertIsNumeric($response['total_pages']);
        $this->assertIsNumeric($response['total_items']);
    }
}