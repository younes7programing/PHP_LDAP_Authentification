<?php
require 'vendor/autoload.php';
require 'LDAPConf.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


if (!(empty($_POST))) :
    // dd($_POST);
    /******/
// config
    $ldapserver = LDAPConf::LDAP_SERVER;
    $ldapuser = LDAPConf::DOMAINE_CONTROLLER."\\" . $_POST["username"];
    $ldappass = $_POST["password"];
    $ldaptree = LDAPConf::LDAP_TREE;

    $ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");


    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

// connect


    if ($ldapconn) {
        $ldapbind = ldap_bind($ldapconn, $ldapuser, $ldappass);
        if ($ldapbind) {
            echo "<h3 class='text-center mt-3'> Authenticated
                    <a href='info/index.php' class='badge badge-info text-center p-2' style='color: #fff;vertical-align: bottom;font-size: 11px'>Show All data</a>
                  </h3> ";
            $ldap_base_dn = LDAPConf::LDAP_BASE_DN;
            $search_filter = '(&(objectCategory=person)(objectClass=user)(sAMAccountName=' . $_POST["username"] . '))';
            $result = ldap_search($ldapconn, $ldap_base_dn, $search_filter);

            $entries = ldap_get_entries($ldapconn, $result);
            // SHOW ALL DATA


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

                $LDAP_LastName = $LDAP_LastName;
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

        }


    } else {
        echo "Invalid Credential";
        echo "LDAP bind failed...";
        die();
    }

    ldap_unbind($ldapconn);// Clean up after ourselves.
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

    <div class="container my-4">
        <table class="table  table-bordered table-striped">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Account Name</th>
                <th scope="col">Username</th>
                <th scope="col">Last Name</th>
                <th scope="col">Department</th>
                <th scope="col">E-Mail Address</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row"><?= $LDAP_samaccountname ?></th>
                <td><?= $LDAP_LastName ?></td>
                <td><?= $LDAP_FirstName ?></td>
                <td><?= $LDAP_Department ?></td>
                <td><?= $LDAP_InternetAddress ?></td>
            </tr>

            </tbody>
        </table>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>

<?php
else:
    header('location: /');
    die();
endif; ?>
