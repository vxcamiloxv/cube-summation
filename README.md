# cube-summation
Hackerrank cube summation challenge

## Requirements:

- PHP 5.4 or higher
- MySQL or SQlite


## Installation:

Need installed [Composer](https://getcomposer.org/) in your machine

```bash
   composer install
```

## Usage:

```bash
 php artisan serve
```

Open your browser on *http://localhost:8000* and see the Cube Summation form.

## Layers: [Spanish]

**Vista:** para la vista se utilizo la integrada en laravel sin ningún framework frontEnd por ahora simplemente para mostrar
el manejo del backend y laravel, se uso por supuesto blade con sus plantilla maestras para no repetir código, tambien se podria usar Ember/Angular
para una vista mas dinamica, auqnue se trato desde el backend de hacerlo lo más dinamica posible.

**Controlador:**
La parte de lógica y de interacción con el usuario, para reducir el código por archivo y para abstraer la funcionalidad haciéndola reusable se dividió en dos.

1. **CubeController.php:** Encargado de recibir y procesar las petición del usuario así como de enviar la respuesta a la vista de procesar las peticiones del usuario.
2. **Helpers\Cubes.php:** En esta clase se almacena la mayoría de la lógica del proseo de busqueda,creación/actualización de la Matrix.

**Persistencia:**
Para la persistencia de los datos se uso la sesión que bien se puede configurar con base de datos, con redis o en este caso
como viene por defecto con archivo, tambien algunos datos se almacenan en memoria.

License
-------

Licensed under the GPL3 License - see the [LICENSE](LICENSE) file for details
