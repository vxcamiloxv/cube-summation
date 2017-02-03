<?php

namespace App\Helpers;

use \Exception;

class Cubes
{
    private $_matrix, $_updated, $_testcase, $_operations;
    private $_testnum = 0;
    private $_opnum = 0;

    function __construct($t = null, $o = null)
    {
        if (!$this->_testcase) {
            $this->_testcase = $t;
        }
        if (!$this->_operations) {
            $this->_operations = $o;
        }
    }

    public function reset() {
        $this->_testnum = 0;
        $this->_opnum = 0;
        $this->_testcase = null;
        $this->_operations = null;
        $this->_matrix = null;
        $this->step(0);
        session(['results' => []]);
    }

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

    public function argCount($argc, $args)
    {
        $size_args = sizeof($args);

        if ($size_args !== $argc) {
            throw new \Exception(sprintf("args needs to be exactly %d was %d", $argc, $size_args));
        };
    }

    public function createMatrix($n)
    {
        for ($i = 0; $i <= $n; $i++) {
            for ($j = 0; $j <= $n; $j++) {
                for ($k = 0; $k <= $n; $k++) {
                    return $this->_matrix[$i][$j][$k] = 0;
                }
            }
        }

    }

    public function updateMatrix($x, $y, $z, $n)
    {
        $this->$_matrix[$x][$y][$z] = $n;
        return $this->_matrix;
    }

    public function getMatrix()
    {
        return $this->_matrix;
    }

    public function setMatrix($m)
    {
        $this->_matrix = $m;
    }

    public function testCase() {
        if ($this->_testnum >= $this->_testcase) {
            $this->setMatrix(null);
            $this->_testnum = 0;
            return false;
        }
        $this->_testnum = $this->_testnum + 1;
        return true;
    }

    public function operations() {
        if ($this->_opnum >= $this->_operations) {
            return false;
        }
        $this->_opnum = $this->_opnum + 1;
        return true;
    }
}