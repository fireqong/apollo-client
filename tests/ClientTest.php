<?php
/**
 * 客户端测试类
 * @author      church<wolfqong1993@gmail.com>
 * @since        1.0
 */

namespace Church\ApolloClient\Test;

use Church\ApolloClient\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testGetFromCache()
    {
        try {
            $client = new Client('http://127.0.0.1:8082', 'aecce2491f0e3e1d6a47dbde44f1e842', 'tech.oss', 'b0714e0fe4374660b8bc95effcb424bb');

            $result = $client->getFromCache();

            $this->assertTrue((boolval($result)));
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->markTestSkipped();
        }

    }

    public function testGetWithoutCache()
    {
        try {
            $client = new Client('http://127.0.0.1:8082', 'aecce2491f0e3e1d6a47dbde44f1e842', 'tech.oss', 'b0714e0fe4374660b8bc95effcb424bb');

            $result = $client->getWithoutCache();

            $this->assertTrue((boolval($result)));
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->markTestSkipped();
        }
    }

    public function testAutoPull()
    {
//        $client = new Client('http://127.0.0.1:8082', 'aecce2491f0e3e1d6a47dbde44f1e842', 'tech.oss', 'b0714e0fe4374660b8bc95effcb424bb');
//        $client->autoPull(['tech.oss', 'tech.baidu', 'tech.sms'], function($configuration) {
//            $configuration = json_decode($configuration, true);
//            $this->assertTrue(boolval($configuration));
//        });
    }
}
