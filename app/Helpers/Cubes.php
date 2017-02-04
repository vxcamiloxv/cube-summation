<?php

namespace App\Helpers;
/**
 * Cube Class
 *
 * @author      Camilo Quimbayo
 */

use \Exception;

class Cubes
{
    private $_testcase, $_operations;
    private $_matrix = [];
    private $_updated = [];

    function __construct($t = null, $o = null)
    {
        $this->_updated = session('_updated', []);
        $this->_matrix = session('_matrix', []);

        if (!$this->_testcase) {
            $this->_testcase = $t;
        }
        if (!$this->_operations) {
            $this->_operations = $o;
        }
    }

    /**
     * Reset all values and set step 0
     *
     * @return void
     */
    public function reset() {
        $this->_testcase = null;
        $this->_operations = null;
        $this->_matrix = [];
        $this->_updated = [];
        $this->step(0);

        session(['_updated' => []]);
        session(['_matrix' => []]);
        session(['results' => []]);
        session(['_testnum' => 0]);
        session(['_opnum' => 0]);
    }

    /**
     * set or get step of cube summation
     *
     * @param number/string $s step number or string "next"
     * @return number
     */
    public function step($s = FALSE) {
        $step = session('step', 0);
        if ($s === "next" && $step < 2) {
            $step++;
        }
        if (is_int($s) && $s >= 0) {
            $step = $s;
        }
        session(['step' => $step]);
        return $step;
    }

    /**
     * set or get step of cube summation
     *
     * @param number $argc number of arguments allowed
     * @param array $args of command values
     * @return throw
     */
    public function argCount($argc, $args)
    {
        $size_args = sizeof($args);

        if ($size_args !== $argc) {
            throw new \Exception(sprintf("Command values needs to be exactly %d was %d", $argc, $size_args));
        };
    }

    /**
     * Create base matrix
     *
     * @param number $n max matrix length
     * @return array
     */
    public function createMatrix($n)
    {
        for ($i = 0; $i <= $n; $i++) {
            for ($j = 0; $j <= $n; $j++) {
                for ($k = 0; $k <= $n; $k++) {
                    $this->_matrix[$i][$j][$k] = 0;
                    session(['_matrix' => $this->_matrix]);
                    return $this->_matrix;
                }
            }
        }

    }

    /**
     * update matrix and save coords
     *
     * @param number $x
     * @param number $y
     * @param number $z
     * @param number $n new value of matrix
     * @return array
     */
    public function updateMatrix($x, $y, $z, $n)
    {
        $this->_matrix[$x][$y][$z] = $n;
        array_push($this->_updated, [$x, $y, $z]);

        session(['_updated' => $this->_updated]);
        session(['_matrix' => $this->_matrix]);

        return $this->_matrix;
    }

    /**
     * get current matrix
     *
     * @return array
     */
    public function getMatrix()
    {
        return $this->_matrix;
    }

    /**
     * set custom matrix
     *
     * @param array $m new matrix
     * @return void
     */
    public function setMatrix($m)
    {
        $this->_matrix = $m;
    }

    /**
     * prevent exploit
     *
     * @param array $values of command
     * @return boolean
     */
    public function checkCommand($values) {
        if (sizeof($values) > 10) {
            throw new \Exception("What's up? You have put too many values");
        }
        return true;
    }

    /**
     * evaluate if allow new test case
     *
     * @return boolean
     */
    public function testCase() {
        if (session('_testnum') + 1 >= $this->_testcase) {
            $this->reset();
            return false;
        }
        session(['_testnum' => session('_testnum', 0) + 1]);
        return true;
    }

    /**
     * evaluate if allow new operations command
     *
     * @return boolean
     */
    public function operations() {
        if (session('_opnum') + 1 >= $this->_operations) {
            session(['_opnum' => 0]);
            $this->step(1);
            $this->testCase();

            return false;
        }
        session(['_opnum' => session('_opnum', 0) + 1]);
        return true;
    }


    /**
     * find coords previously saved
     *
     * @param array $values matrix
     * @return array
     */
    public function findBounds($values) {
        list($x1, $y1, $z1, $x2, $y2, $z2) = $values;

        $coords = [];

        foreach ($this->_updated as $update) {
            list($x, $y, $z) = $update;

            if ($x >= ($x1-1) && $x < $x2 && $y >= ($y1-1) && $y < $y2 && $z >= ($z1-1) && $z < $z2) {
                array_push($coords, [$x, $y, $z]);
            }
        }
        return $coords;
    }
}