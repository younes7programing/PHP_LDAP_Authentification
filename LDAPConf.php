<?php

class LDAPConf
{

    const LDAP_SERVER = 'ldap://0.0.0.0';
    const DOMAINE_CONTROLLER = "domain";
    const TLD = "local";
    const CN = 'Users';

    //ADMIN
    const LDAP_USER_ADMIN = 'UserAdmin';
    const LDAP_PASSWORD_ADMIN = 'PasswordAdmin';



    /**
     * Default var,do not modify :)
     */
    const LDAP_USER_ADMIN_DOMAIN = self::LDAP_USER_ADMIN . "@" . self::DOMAINE_CONTROLLER . "." . self::TLD;
    const LDAP_CONNECT_URI = self::DOMAINE_CONTROLLER . '.' . self::TLD;
    const LDAP_BASE_DN = 'DC=' . self::DOMAINE_CONTROLLER . ',DC=' . self::TLD;
    const LDAP_TREE = "CN=" . self::CN . ",DC=" . self::DOMAINE_CONTROLLER . ",DC=" . self::TLD;


}