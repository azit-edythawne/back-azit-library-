<?php

namespace Azit\Ddd\Arch\Constant;

class MessageConstant {

    // SGU Exception
    public const SGU_INVALID = 'SGU Invalido';
    public const SGU_EMPTY = 'SGU Vacio';
    public const SGU_VALID = 'SGU Valido';
    public const REQUIRE_TOKEN = 'Require token';
    
    public const SESSION_INVALID = 'Sesión invalida';

    // Route error
    public const EXCEPTION_ROUTE_NOT_FOUND = 'Ruta no encontrada';

    // MySQL error
    public const EXCEPTION_CREATE = 'Se ha producido un error al crear el registro';
    public const EXCEPTION_UPDATE = 'Se ha producido un error al modificar el registro';
    public const EXCEPTION_DELETE = 'Se ha producido un error al eliminar el registro';
    public const EXCEPTION_SELECT = 'Se ha producido un error, registro no encontrado';
    public const EXCEPTION_ATTACHMENTS = 'Se ha producido un error al guardar los archivos';
    public const EXCEPTION_MATCH = 'Se ha producido un error al realizar un filtro';
    public const EXCEPTION_DATA_REQUIRED = 'Algunos datos son requeridos';
    public const EXCEPTION_UUID_REQUIRED = 'Se require un identificador';

    // Mi Muro exception
    public const EXCEPTION_MI_MURO_ACCESS = 'Sin acceso al sistema por parte de Mi Muro';

    // Acciones
    public const SUCCESS_ACTION = 'El proceso se ha realizado con exito';
    public const SUCCESS_NOT_ACTION = 'El proceso no se pudo realizar';
    public const SUCCESS_HAS_BEEN_RESPONSE = 'Se ha respondido anteriormente';
    public const NO_RECORD_FOUND = 'No se encontró ningún registro';
    public const EXCEPTION_MOVE = 'No se pudo actualizar el estatus';
    public const ACTION_MAKE = 'Petición realizada';

}
