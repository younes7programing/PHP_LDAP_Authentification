<?php
require '../vendor/autoload.php';
require '../LDAPConf.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>


<div class="container-fluid  my-4">

    <h2>AD User Results</h2></br>
    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Account Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">First Name</th>
            <th scope="col">Company</th>
            <th scope="col">Department</th>
            <!-- <th scope="col">Office Phone</th>
             <th scope="col">Office Fax</th>
             <th scope="col">Mobile</th>-->
            <th scope="col">DDI</th>
            <th scope="col">E-Mail-Address</th>
            <th scope="col">Home Phone</th>


        </tr>
        </thead>
        <tbody>

        <?php
        /***
         * https://www.geekshangout.com/php-example-get-data-active-directory-via-ldap/
         */


        //LDAP Bind paramters, need to be a normal AD User account.
        $ldap_password = LDAPConf::LDAP_PASSWORD_ADMIN;
        $ldap_username = LDAPConf::LDAP_USER_ADMIN_DOMAIN;
        $ldap_connection = ldap_connect(LDAPConf::LDAP_CONNECT_URI);

        if (FALSE === $ldap_connection) {
            // Uh-oh, something is wrong...
            echo 'Unable to connect to the ldap server';
        }

        // We have to set this option for the version of Active Directory we are using.
        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
        ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

        if (TRUE === ldap_bind($ldap_connection, $ldap_username, $ldap_password)) :

        //Your domains DN to query
        $ldap_base_dn = LDAPConf::LDAP_BASE_DN;

        //Get standard users and contacts
        $search_filter = '(|(objectCategory=person)(objectCategory=Cel))';


        //Connect to LDAP
        $result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter);

        if (FALSE !== $result) :
        $entries = ldap_get_entries($ldap_connection, $result);

        // Uncomment the below if you want to write all entries to debug somethingthing
        //var_dump($entries);

        //Create a table to display the output
        echo '';
        // echo '<table border = "1"><tr bgcolor="#cccccc"><td>Username</td><td>Last Name</td><td>First Name</td><td>Company</td><td>Department</td><td>Office Phone</td><td>Fax</td><td>Mobile</td><td>DDI</td><td>E-Mail Address</td><td>Home Phone</td></tr>';
        ?>

        <?php
        //For each account returned by the search
        for ($x = 0;
        $x < $entries['count'];
        $x++) :

        //
        //Retrieve values from Active Directory
        //

        //Windows Usernaame
        $LDAP_samaccountname = "";
        if (!empty($entries[$x]['samaccountname'][0])) {
            $LDAP_samaccountname = $entries[$x]['samaccountname'][0];
            if ($LDAP_samaccountname == "NULL") {
                $LDAP_samaccountname = "";
            }
        } else {
            //#There is no samaccountname s0 assume this is an AD contact record so generate a unique username
            $LDAP_uSNCreated = $entries[$x]['usncreated'][0];
            $LDAP_samaccountname = "CONTACT_" . $LDAP_uSNCreated;
        }

        //Last Name
        $LDAP_LastName = "";
        if (!empty($entries[$x]['sn'][0])) {
            $LDAP_LastName = $entries[$x]['sn'][0];
            if ($LDAP_LastName == "NULL") {
                $LDAP_LastName = "";
            }
        }

        //First Name
        $LDAP_FirstName = "";
        if (!empty($entries[$x]['givenname'][0])) {
            $LDAP_FirstName = $entries[$x]['givenname'][0];
            if ($LDAP_FirstName == "NULL") {
                $LDAP_FirstName = "";
            }
        }

        //Company
        $LDAP_CompanyName = "";
        if (!empty($entries[$x]['company'][0])) {
            $LDAP_CompanyName = $entries[$x]['company'][0];
            if ($LDAP_CompanyName == "NULL") {
                $LDAP_CompanyName = "";
            }
        }

        //Department
        $LDAP_Department = "";
        if (!empty($entries[$x]['department'][0])) {
            $LDAP_Department = $entries[$x]['department'][0];
            if ($LDAP_Department == "NULL") {
                $LDAP_Department = "";
            }
        }

        //Job Title
        $LDAP_JobTitle = "";
        if (!empty($entries[$x]['title'][0])) {
            $LDAP_JobTitle = $entries[$x]['title'][0];
            if ($LDAP_JobTitle == "NULL") {
                $LDAP_JobTitle = "";
            }
        }

        //IPPhone
        $LDAP_OfficePhone = "";
        if (!empty($entries[$x]['ipphone'][0])) {
            $LDAP_OfficePhone = $entries[$x]['ipphone'][0];
            if ($LDAP_OfficePhone == "NULL") {
                $LDAP_OfficePhone = "";
            }
        }

        //FAX Number
        $LDAP_OfficeFax = "";
        if (!empty($entries[$x]['facsimiletelephonenumber'][0])) {
            $LDAP_OfficeFax = $entries[$x]['facsimiletelephonenumber'][0];
            if ($LDAP_OfficeFax == "NULL") {
                $LDAP_OfficeFax = "";
            }
        }

        //Mobile Number
        $LDAP_CellPhone = "";
        if (!empty($entries[$x]['mobile'][0])) {
            $LDAP_CellPhone = $entries[$x]['mobile'][0];
            if ($LDAP_CellPhone == "NULL") {
                $LDAP_CellPhone = "";
            }
        }

        //Telephone Number
        $LDAP_DDI = "";
        if (!empty($entries[$x]['telephonenumber'][0])) {
            $LDAP_DDI = $entries[$x]['telephonenumber'][0];
            if ($LDAP_DDI == "NULL") {
                $LDAP_DDI = "";
            }
        }

        //Email address
        $LDAP_InternetAddress = "";
        if (!empty($entries[$x]['mail'][0])) {
            $LDAP_InternetAddress = $entries[$x]['mail'][0];
            if ($LDAP_InternetAddress == "NULL") {
                $LDAP_InternetAddress = "";
            }
        }

        //Home phone
        $LDAP_HomePhone = "";
        if (!empty($entries[$x]['homephone'][0])) {
            $LDAP_HomePhone = $entries[$x]['homephone'][0];
            if ($LDAP_HomePhone == "NULL") {
                $LDAP_HomePhone = "";
            }
        }


        ?>


        <tr>
            <th scope="row"><?= $x + 1 ?></th>
            <th><?= $LDAP_samaccountname ?></th>
            <td><?= $LDAP_LastName ?></td>
            <td><?= $LDAP_FirstName ?></td>
            <td><?= $LDAP_CompanyName ?></td>
            <td><?= $LDAP_Department ?></td>
            <!--<td><?php //$LDAP_OfficePhone
            ?></td>
            <td>
            <?php //$LDAP_OfficeFax
            ?></td>
            <td><?php //$LDAP_CellPhone
            ?></td>-->
            <td><?= $LDAP_DDI ?></td>
            <td><?= $LDAP_InternetAddress ?></td>
            <td><?= $LDAP_HomePhone ?></td>
        </tr>

        </tbody>


        <?php

        endfor; //END for loop
        endif; //END FALSE !== $result

        ldap_unbind($ldap_connection); // Clean up after ourselves.
        echo " </table>";

        endif; //END ldap_bind
        ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
