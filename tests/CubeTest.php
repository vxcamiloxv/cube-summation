<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\Cubes;

class CubeTest extends TestCase
{
    /**
     * Test cube summation class.
     *
     * @return void
     */
    public function test_cube_class()
    {
        $cube = new Cubes(2, 3);

        // check step
        $this->assertTrue(is_int($cube->step()));
        $this->assertTrue($cube->step(1) == 1);
        $this->assertTrue($cube->step('next') == 2);
        $this->assertTrue($cube->step(0) == 0);

        // Check matrix
        $this->assertInternalType("array", $cube->createMatrix(5));
    }

    /**
     * Test send function with error
     *
     * @return void
     */
    public function controller_should_show_errors()
    {
        $this->withoutMiddleware();

        $data = [];

        $this->route('POST', 'cube', $data);

        $this->assertRedirectedToRoute('/');

        $this->visit('/')
            ->press('Send')
            ->see('Something went wrong :(');
    }

    /**
     * Test send function and show next inputs
     *
     * @return void
     */
    public function controller_should_show_step1()
    {
        $this->withoutMiddleware();

        $this->visit('/')
            ->type(2, 'testcase')
            ->press('Send')
            ->see('Matrix')
            ->see('Operations')
            ->seePageIs('/cube');

        $this->assertTrue(session('_testnum') == 1);
    }
}
