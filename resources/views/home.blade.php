@extends('layouts.app')

@section('content')

  <div class="container">
    <div class="col-sm-offset-2 col-sm-8">

      <div class="panel panel-default">
        <div class="panel-heading">
          Cube Summation
        </div>

        <div class="panel-body">
          <!-- Validation Errors -->
          @include('errors.common')

          <!-- Cube Form -->
          @if (isset($message) && strlen($message) > 0)
            <div class="alert alert-info">
              <strong>{{$message}}</strong>
            </div>
          @endif

          <form action="{{ url('cube')}}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            <!-- Inputs -->

            <div class="form-group">
              <label for="testcase" class="col-sm-3 control-label">Test-cases</label>
              <div class="col-sm-6">
                <input type="number" name="testcase" id="testcase" class="form-control"
                       value="{{ old('testcase') }}" {{ $step > 0 ? "readonly":"" }}>
              </div>
            </div>

            @if ($step > 0)
              <div class="form-group">
                <label for="matrix" class="col-sm-3 control-label">Matrix</label>
                <div class="col-sm-6">
                  <input type="number" name="matrix" id="matrix" class="form-control"
                         value="{{ old('matrix') }}" {{ $step > 1 ? "readonly":"" }}>
                </div>
              </div>

              <div class="form-group">
                <label for="operations" class="col-sm-3 control-label">Operations</label>
                <div class="col-sm-6">
                  <input type="number" name="operations" id="operations" class="form-control"
                         value="{{ old('operations') }}" {{ $step > 1 ? "readonly":"" }}>
                </div>
              </div>
            @endif
            @if ($step > 1)
              <div class="form-group">
                <label for="command_type" class="col-sm-3 control-label">Command:</label>
                <div class="col-sm-6">
                  <select id="command_type" name="command_type" class="form-control" value="{{ old('command_type') }}">
                    <option>UPDATE</option>
                    <option>QUERY</option>
                  </select>
                  <input type="text" name="command_value" id="command_value" class="form-control" value="{{ old('command_value') }}">
                </div>
              </div>
            @endif

            <!-- Buttons -->
            <div class="form-group">
              <div class="col-sm-offset-3 col-sm-6">
                <button type="submit" class="btn btn-default">
                  <i class="fa fa-btn fa-arrow-circle-right"></i> Send
                </button>

                <button type="submit" class="btn btn-default" formaction="{{ url('reset') }}">
                  <i class="fa fa-btn fa-eraser"></i> Clear
                </button>
              </div>
            </div>

          </form>

        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">
          Results
        </div>

        <div class="panel-body">

          @if (isset($results) && sizeof($results) >0)
            @for ($i = 0; $i < sizeof($results); $i++)
              <div>{{$results[$i]['date']}} -> {{ $results[$i]['value'] }}</div>
            @endfor
          @endif

        </div>
      </div>

    </div>
  </div>

@endsection
