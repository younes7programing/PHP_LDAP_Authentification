<?php
require 'vendor/autoload.php';
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


if (!(empty($_POST))) {

    /******/
    $ldapUserName = htmlspecialchars($_POST["username"]);
    $ldappass = htmlspecialchars($_POST["password"]);
// config
    $ldapserver = LDAPConf::LDAP_SERVER;
    $ldapuser = LDAPConf::DOMAINE_CONTROLLER . "\\" . $ldapUserName;

    $ldaptree = LDAPConf::LDAP_TREE;

    $ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");


    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

// connect


    if ($ldapconn) {
        $ldapbind = ldap_bind($ldapconn, $ldapuser, $ldappass);
        if ($ldapbind) {
            echo "Authenticated";
            $ldap_base_dn = LDAPConf::LDAP_BASE_DN;
            $search_filter = '(&(objectCategory=person)(objectClass=user)(sAMAccountName=' . $ldappass . '))';
            $result = ldap_search($ldapconn, $ldap_base_dn, $search_filter);

            $entries = ldap_get_entries($ldapconn, $result);
            // SHOW ALL DATA
            echo '<h1>Dump all data</h1><pre>';

            //
            //Retrieve values from Active Directory
            //
            //Windows Usernaame
            $LDAP_samaccountname = "";
            if (!empty($entries[0]['samaccountname'][0])) {
                $LDAP_samaccountname = $entries[0]['samaccountname'][0];
                if ($LDAP_samaccountname == "NULL") {
                    $LDAP_samaccountname = "";
                }
            } else {
                //#There is no samaccountname s0 assume this is an AD contact record so generate a unique username
                $LDAP_uSNCreated = $entries[0]['usncreated'][0];
                $LDAP_samaccountname = "CONTACT_" . $LDAP_uSNCreated;
            }

            //Last Name
            $LDAP_LastName = "";
            if (!empty($entries[0]['sn'][0])) {
                $LDAP_LastName = $entries[0]['sn'][0];
                $id = 1;
                $LDAP_LastName = $LDAP_LastName . "  " . $id;
                if ($LDAP_LastName == "NULL") {
                    $LDAP_LastName = "";
                }
            }
            // echo $_POST["username"];


            //First Name
            $LDAP_FirstName = "";
            if (!empty($entries[0]['givenname'][0])) {
                $LDAP_FirstName = $entries[0]['givenname'][0];
                if ($LDAP_FirstName == "NULL") {
                    $LDAP_FirstName = "";
                }
            }


            //Department
            $LDAP_Department = "";
            if (!empty($entries[0]['department'][0])) {
                $LDAP_Department = $entries[0]['department'][0];
                if ($LDAP_Department == "NULL") {
                    $LDAP_Department = "";
                }
            }


            //Email address
            $LDAP_InternetAddress = "";
            if (!empty($entries[0]['mail'][0])) {
                $LDAP_InternetAddress = $entries[0]['mail'][0];
                if ($LDAP_InternetAddress == "NULL") {
                    $LDAP_InternetAddress = "";
                }
            }


            echo $LDAP_samaccountname . "<br>";
            echo $LDAP_LastName . "<br>";
            echo $LDAP_FirstName . "<br>";
            echo $LDAP_Department . "<br>";
            echo $LDAP_InternetAddress . "<br>";

        }


    } else {
        echo "Invalid Credential";
        echo "LDAP bind failed...";
        die();
    }


}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LDAP</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="my-4">LDAP AUTHENTIFICATION</h2>

    <form action="details.php" method="post" class="my-4">

        <div class="form-group">
            <label for="exampleInputEmail1">LDAP username</label>
            <input name="username" type="text" class="form-control" id="exampleInputEmail1"
                   aria-describedby="emailHelp"
                   placeholder="x.username">
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>

        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input name="password" type="password" class="form-control" id="exampleInputPassword1"
                   placeholder="Password">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
