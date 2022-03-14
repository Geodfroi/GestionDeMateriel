<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.14 ###
##############################

namespace app\constants;

class Route
{
    // routes
    const ADMIN = LOCAL_SERVER ? '/admin' : "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/admin";
    const ART_TABLE = LOCAL_SERVER ? '/articletable' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/articletable";
    const ART_EDIT = LOCAL_SERVER ? '/articleedit' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/articleedit";

    const DEBUG_EMAILS = LOCAL_SERVER ? '/debugmails' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/debugmails";

    const DEBUG_PAGE = LOCAL_SERVER ? '/debugpage' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/debugpage";

    const CONTACT = LOCAL_SERVER ? '/contact' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/CONTACT";

    const LOCAL_PRESETS =  LOCAL_SERVER ? '/locationpresets' : "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/locationpresets";

    const LOGIN = LOCAL_SERVER ? '/login' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/login";

    const HOME = LOCAL_SERVER ? '/' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/login";

    const PROFILE = LOCAL_SERVER ? '/profile' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/profile";

    const USER_EDIT = LOCAL_SERVER ? '/useredit'
        : "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/useredit";

    const USERS_TABLE = LOCAL_SERVER ? '/usertable' :
        "http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/usertable";
}
