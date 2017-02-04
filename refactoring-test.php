<?php

/*
 * 1) El mayor problema encontrado fue sobre complejidad ciclomática que era bastante elevada dificultando la
 * lectura del codigo asi como posible errore futuros por dicha razón, además variables mal declaradas y llamados a la
 * base de datos redundantes y problemas de estilo como mezclar comillas dobles con simple.
 *
 * 2) Para solucionar la complejidad ciclomática simplifique el codigo eliminando condicionales reduntantes, para reducir las llamadas a la base de datos
 * elimine las llamadas innecesarias y unifique otras, asi como validar desde el inicio el 'Driver', corrigi los estilos usando solo comilla simple, como
 * resultado quedo un codigo con mejor legibilidad y con menor lineas
 */
class testClass
{
    public function post_confirm() {
        $id = Input::get('service_id');
        $driverId = Input::get('driver_id');
        $servicio = Service::find($id);
        $driverTmp = Driver::find($driverId);

        //dd($servicio);
        if ($servicio == NULL || $driverTmp == NULL) {
            return Response::json(Array('error' => '3'));
        }
        if($servicio->status_id == '6') {
            return Response::json(Array('error' => '2'));
        }
        if ($servicio->driver_id != NULL || $servicio->status_id != '1') {
            return Response::json(Array('error' => '1'));
        }

        $servicio->update([
            'driver_id' => $driverId,
            'status_id' => '2',
            'car_id' => $driverTmp->car_id
        ]);
        $driverTmp->update([
            'available' => '0'
        ]);

        //Notificar a usuario!!
        $pushMessage = 'Tu servicio ha sido confirmado!';
        $push = Push::make();

        if($servicio->user->uuid == '') {
            return Response::json(['error' => '0']);
        }
        if($servicio->user->type == '1') {//iPhone
            $result = $push->ios($servicio->user->uuid, $pushMessage,1,'honk.wav','Open', ['serviceId' => $servicio->id]);
        }else{
            $result = $push->android2($servicio->user->uuid,$pushMessage,1,'default','Open', ['serviceId' => $servicio->id]);
        }
        return Response::json(['error' => '0']);
    }
}