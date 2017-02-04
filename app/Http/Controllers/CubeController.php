<?php

namespace App\Http\Controllers;
/**
 * CubeController
 *
 * @author      Camilo Quimbayo
 */

use App\Helpers\Cubes;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;

class CubeController extends Controller
{

    /**
     * reset cube form and clean other values
     *
     * @param object $request of laravel
     * @return void
     */
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

    /**
     * init cube and send new command
     *
     * @param object $request of laravel
     * @return void
     */
    public function send(Request $request)
    {
        try {
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
            }

            // Creat matrix
            if (!$matrix && $request->matrix) {
                $matrix = $cube->createMatrix($request->matrix);
            }

            $type = $request->command_type;
            $values = !empty($request->command_value) ? $request->command_value : "";
            $values = preg_split("/[\s,]+/", trim($values));
            if (sizeof($values) > 0) {
                $cube->checkCommand($values);
            }

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
        } catch(\Exception $error) {
            $validator->errors()->add('command_value', $error->getMessage());

            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }
    }

    /**
     * update cube matrix
     *
     * @param array $values of command
     * @param object $cube instance
     * @param object $validator of laravel
     * @return void
     */
    private function update($values, $cube, $validator)
    {
        try {
            $cube->argCount(4, $values);
            $results = session('results', []);
            $setMatrix = $cube->operations();
            $message = "";

            if (!$setMatrix) {
                $message = $cube->step() == 0 ? "End cube test" : "Send matrix and operations again";

            } else {
                $x = $values[0]-1;
                $y = $values[1]-1;
                $z = $values[2]-1;
                $w = $values[3];

                $cube->updateMatrix($x, $y, $x, $w);
                array_push($results, ['value' => "OK", 'date' => date('Y-m-d H:i:s') ]);
                session(['results' => $results]);
            }

            return view('home', [
                'step' => $cube->step(),
                'message' => $message,
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

    /**
     * find coors in matrix and sum the result
     *
     * @param array $values of command
     * @param object $cube instance
     * @param object $validator of laravel
     * @return void
     */
    private function query($values, $cube, $validator)
    {
        try {
            $cube->argCount(6, $values);
            $results = session('results', []);
            $setMatrix = $cube->operations();
            $message = "";

            if (!$setMatrix) {
                $message = $cube->step() == 0 ? "End cube test" : "Send matrix and operations again";

            }
            $matrix = $cube->getMatrix();
            $coords = $cube->findBounds($values);
            $sum = 0;

            foreach ($coords as $c) {
                list($x,$y,$z) = $c;
                error_log(implode(",", $c));
                $sum += $matrix[$x][$y][$z];
            }
            array_push($results, ['value' => $sum, 'date' => date('Y-m-d H:i:s') ]);
            session(['results' => $results]);

            return view('home', [
                'step' => $cube->step(),
                'message' => $message,
                'results' => $results
            ]);

        } catch(\Exception $error) {
            $validator->errors()->add('command_value', $error->getMessage());

            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }
    }
}
