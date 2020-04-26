<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Webservice_test extends TestCase
{
    public function test_index()
    {
        $output = $this->request('GET', 'webservice/index');
        $this->assertContains('{"message":"welcome to key\/value store webservice"}', $output);
    }

    public function test_method_404()
    {
        $this->request('GET', 'webservice/method_not_exist');
        $this->assertResponseCode(404);
    }

    public function test_empty_body()
    {
        $output = $this->request('POST', 'webservice/object');
        $this->assertContains('{"message":"Request body is empty."}', $output);
    }

    public function test_empty_get()
    {
        $output = $this->request('GET', 'webservice/object/123123');
        $this->assertContains('{"message":"No result found."}', $output);
    }

    public function test_not_supported_request()
    {
        $output = $this->request('PUT', 'webservice/object/123123');
        $this->assertContains('{"message":"Request method not supported."}', $output);
    }

    public function test_APPPATH()
    {
        $actual = realpath(APPPATH);
        $expected = realpath(__DIR__ . '/../..');
        $this->assertEquals(
            $expected,
            $actual,
            'Your APPPATH seems to be wrong. Check your $application_folder in tests/Bootstrap.php'
        );
    }
}
