<?php

namespace Azit\Ddd\Arch\Constant;

class SGUConstant {

    public const URL_SGU = 'http://201.163.59.107:7099/persona/v2/persona?llaveApp=%s';
    public const URL_SGU_LOGOUT = 'http://201.163.59.107:7099/logout';
    public const URL_SGU_BY_CTT = 'https://apis.sej.jalisco.gob.mx/ccts/v1/ccts/clavecct?llaveApp=%s&clave=%s';
    public const URL_SGU_BY_PERSON = 'http://201.163.59.107:7099/persona/v2/persona/trabajador?llaveApp=%s&filtro=%s&criterio=%s';

    public const ARG_CURP = 'curp';
    public const ARG_MAIL = 'usuario';
    public const ARG_CCT = 'cct';

}