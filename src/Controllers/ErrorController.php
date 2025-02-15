<?php 
    namespace Controllers;

    use Lib\Pages;

    class ErrorController{
       
        public static function show_Error404(): void{
            $pages = new Pages();
            $pages->render('Error/error404', ['titulo'=> 'Pagina no encontrada']);
        }

        public static function show_Error403(): void {
            $pages = new Pages();
            $pages->render('Error/error403' , ['titulo'=> 'Acceso Denegado']);
        }
    }
