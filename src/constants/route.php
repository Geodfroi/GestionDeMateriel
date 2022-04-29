<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.29 ###
##############################

namespace app\constants;

class Page
{
    const ADMIN = 'admin';
    const ART_TABLE = 'articletable';
    const ART_EDIT =  'articleedit';
    const DEBUG_EMAILS =  'debugmails';
    const DEBUG_PAGE =  'debugpage';
    const CONTACT =  'contact';
    const LOCAL_PRESETS =   'locationpresets';
    const LOGIN =  'login';
    const LOGOUT =  'logout';
    const PROFILE =  'profile';
    const USER_EDIT =  'useredit';
    const USERS_TABLE =  'usertable';
}

class Route
{
    // routes
    const ADMIN = APP_URL . '/' . Page::ADMIN;
    const ART_TABLE = APP_URL  . '/' .  Page::ART_TABLE;
    const ART_EDIT = APP_URL . '/' .  Page::ART_EDIT;
    const DEBUG_EMAILS = APP_URL . '/' .  Page::DEBUG_EMAILS;
    const DEBUG_PAGE = APP_URL . '/' .  Page::DEBUG_PAGE;
    const CONTACT = APP_URL . '/' .  Page::CONTACT;
    const LOCAL_PRESETS =  APP_URL . '/' .  Page::LOCAL_PRESETS;
    const LOGIN = APP_URL . '/' .  Page::LOGIN;
    const LOGOUT = APP_URL . '/' .  Page::LOGOUT;
    const HOME = APP_URL;
    const PROFILE = APP_URL . '/' .  Page::PROFILE;
    const SERVER = APP_URL . '/aikEBljqDIAzeMBgMPoS';
    const USER_EDIT = APP_URL . '/' .  Page::USER_EDIT;
    const USERS_TABLE = APP_URL . '/' .  Page::USERS_TABLE;
}
