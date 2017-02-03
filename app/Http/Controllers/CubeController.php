<?php

namespace App\Http\Controllers;

use App\Helpers\Cubes;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;

class CubeController extends Controller
{
    public function reset(Request $request)
    {
        // reset steps
        $cube = new Cubes();
        $cube->reset();

        // Clean form
        $request->replace(['testcase' => '']);
        $request->replace(['matrix' => '']);
        $request->replace(['operations' => '']);

        return redirect('/')->withInput();
    }

    public function command(Request $request)
    {
        $request->flash();
        $rules = [
            'testcase' => 'required|numeric|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);
        $cube = new Cubes($request->testcase, $request->operations);
        $matrix = $cube->getMatrix();

        // Validation by step
        if ($cube->step() > 0) {
            $rules['matrix'] = 'required|numeric|max:255';
            $rules['operations'] = 'required|numeric|max:255';
            $validator = Validator::make($request->all(), $rules);
        }

        if ($cube->step() > 1) {
            $rules['command_type'] = 'required|max:255';
            $rules['command_value'] = 'required|max:255';
            $validator = Validator::make($request->all(), $rules);

            if (!$cube->operations()) {
                $cube->testCase();
                $cube->step(1);
            }
        }

        // Creat matrix
        if (!$matrix && $request->matrix) {
            $matrix = $cube->createMatrix($request->matrix);
        }

        $type = $request->command_type;
        $values = !empty($request->command_value) ? $request->command_value : "";
        $values = preg_split("/[\s,]+/", $values);

        // Display step dynamic
        $cube->step($request->testcase ? ($request->matrix && $request->operations ? 2 : 1) : 0);

        if ($validator->fails()) {
            return redirect('/')
                ->withInput()
                ->withErrors($validator);

        } else if (strtoupper($type) === 'UPDATE') {
            return $this->update($values, $cube, $validator);

        } else if (strtoupper($type) == 'QUERY') {
            return $this->query($values, $cube, $validator);

        } else {
            return view('home', [
                'step' => $cube->step()
            ]);
        }
    }

    private function update($values, $cube, $validator)
    {
        try {
            $results = session('results', []);
            $cube->argCount(6, $values);
            $cube->operations();

            $x = $values[0]-1;
            $y = $values[1]-1;
            $z = $values[2]-1;
            $w = $values[3];

            #$cube->updateMatrix($x, $y, $x, $w);
            array_push($results, ['value' => "OK", 'date' => date('Y-m-d H:i:s') ]);
            session(['results' => $results]);

            return view('home', [
                'step' => $cube->step(),
                'success' => TRUE,
                'results' => $results
            ]);
        } catch(\Exception $error) {
            $validator->errors()->add('command_value', $error->getMessage());
            $cube->results = [];
            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }
    }

    private function query($values, $cube, $validator)
    {
        $cube->operations();
        return view('home', [
            'step' => $cube->step("next")
        ]);
    }
}
