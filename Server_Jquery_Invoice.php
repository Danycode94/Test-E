<?php
//   * * * * * * * * * * * * * * * * * * *
//    P R O F O R M A - S E R V E R      *
//   * * * * * * * * * * * * * * * * * * *

include "../Configuration/parser.php";
include('../Configuration/config.php');
include('../Configuration/config1.php');


/**
 * Fonction for save a invoice hd and dt first save invoice
 */
if (isset($_POST["saveInvoiceHdFirstStep"])) {
    $quoteNumber = $_POST["saveInvoiceHdFirstStep"];

    $query = "SELECT * FROM quotehd WHERE quoteNumber='$quoteNumber'";
    $fetch_data = mysqli_query($con, $query);
    $row = mysqli_num_rows($fetch_data);
    $data = $inc = $invoice_idHd =  0;

    if ($row > 0) {
        while ($row = mysqli_fetch_assoc($fetch_data)) {
            $succursale_id    = $row['succursale_id'];
            $dateInvoice = test_input(date('Y-m-d'));
            $quoteIDHD        = $row['quoteIDHD'];
            $customers_id    = $row['customers_id'];
            $notes            = $row['notes'];
            $monnaie        = $row['monnaie'];
            $quantiteItems    = $row['quantiteItems'];
            $totalItems        = $row['totalItems'];
            $taxes            = $row['taxes'];
            $discountPourcentage = $row['discountPourcentage'];
            $discountAmount        = $row['discountAmount'];
            $grandTotal        = $row['grandTotal'];
            $ordermoney        = $row['orderMoney'];
            $taux        = $row['taux'];
            $statut        = 'E';
            $transport    = $row['transport'];
            $typePaiement    = 'Multi';
            $statutPaiement    = 'N';
            $typeFacture    = 'I';
            $user_override    = $row['user_override'];
            $date_override    = $row['date_override'];
            $totalCost        = 0;
            $deposit    = 0;

            // Insert Invoicehd
            $sql = "INSERT INTO invoicehd (succursale_id, dateInvoice, quoteIDHD, customers_id, notes, monnaie, quantiteItems, totalItems, taxes, discountPourcentage, discountAmount, grandTotal, ordermoney, taux, statut,transport, typePaiement, statutPaiement, typeFacture, user_Override, date_Override, totalCost, deposit) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "isiissddddddsdsdsssssdd",
                    $succursale_id,
                    $dateInvoice,
                    $quoteIDHD,
                    $customers_id,
                    $notes,
                    $monnaie,
                    $quantiteItems,
                    $totalItems,
                    $taxes,
                    $discountPourcentage,
                    $discountAmount,
                    $grandTotal,
                    $ordermoney,
                    $taux,
                    $statut,
                    $transport,
                    $typePaiement,
                    $statutPaiement,
                    $typeFacture,
                    $user_Override,
                    $date_Override,
                    $totalCost,
                    $deposit
                );
            }
        }

        // Recuperer Invoice IDHD
        if (mysqli_stmt_execute($stmt)) {
            $inc += 1;
            $invoice_idHd = mysqli_insert_id($con);
        } else {
            die("SQL query failed: " . mysqli_error($con));
        }
    }
    $fetch_data->close();

    if ($inc > 0) {
        $query_dt = "SELECT * FROM quotedt WHERE quoteIDHD = '$quoteNumber'";
        $fetch_data_dt = mysqli_query($con, $query_dt);
        $row_dt = mysqli_num_rows($fetch_data_dt);

        if ($row_dt > 0) {
            while ($row_dt = mysqli_fetch_assoc($fetch_data_dt)) {
                $succursale_id    = $row_dt['succursale_id'];
                $invoiceIDHD = $invoice_idHd;
                $produit_id    = $row_dt['produit_id'];
                $dateInvoice = $row_dt['dateQuote'];
                $quantite = $row_dt['quantite'];
                $unitPrice1    = $row_dt['unitPrice1'];
                $unitPrice2    = $row_dt['unitPrice2'];
                $statut    = 'E';
                $monnaie = $row_dt['monnaie'];
                $typeFacture = 'I';
                $averageCost = $row_dt['averageCost'];
                $orderUnit = $row_dt['orderUnit'];
                $descriptionUnit = $row_dt['descriptionUnit'];
                $quantite2 = $row_dt['quantite2'];
                $unit_id = $row_dt['unit_id'];
                $itemName = $row_dt['itemName'];
                $itemDescription = $row_dt['itemDescription'];
                $typeItem = $row_dt['typeItem'];

                // Insert Invoice dt
                $sql_dt = "INSERT INTO invoicedt (succursale_id, invoiceIDHD, produit_id, dateInvoice, quantite, unitPrice1,unitPrice2, statut, monnaie, typeFacture, taux, averageCost, orderUnit, descriptionUnit, quantite2, unit_id,itemName, itemDescription, typeItem)
			    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                if ($stmt_dt = mysqli_prepare($con, $sql_dt)) {
                    mysqli_stmt_bind_param(
                        $stmt_dt,
                        "iiisdddsssddssdisss",
                        $succursale_id,
                        $invoiceIDHD,
                        $produit_id,
                        $dateInvoice,
                        $quantite,
                        $unitPrice1,
                        $unitPrice2,
                        $statut,
                        $monnaie,
                        $typeFacture,
                        $taux,
                        $averageCost,
                        $orderUnit,
                        $descriptionUnit,
                        $quantite2,
                        $unit_id,
                        $itemName,
                        $itemDescription,
                        $typeItem
                    );

                    if (mysqli_stmt_execute($stmt_dt)) {
                    } else {
                        die("SQL query failed: " . mysqli_error($con));
                    }
                } else {
                    die("SQL query failed: " . mysqli_error($con));
                }
            }
        }
        $fetch_data_dt->close();
    }
    echo $invoice_idHd;
}

/**
 * Fonction for save a invoice part 2
 */
else if (isset($_POST["invoice_id"])) {
    $invoiceIDHD = $_POST["invoice_id"];
    $quoteIDHD = $_POST["quote_id_save"];

    $cash = $_POST["cash"];
    $debitcredit = $_POST["debitcredit"];
    $check = $_POST["check"];
    $numerocheck = $_POST["numerocheck"];
    $account = $_POST["account"];
    $depositonso = $_POST["depositonso"];

    $knowPayment = 0;
    $typePayment = "";
    if ($numerocheck == "") $numerocheck = "";

    if ($cash == "") $cash = 0;
    else {
        $typePayment = "Cash";
        $knowPayment += 1;
    }
    if ($debitcredit == "") $debitcredit = 0;
    else {
        $typePayment = "Debit Credit";
        $knowPayment += 1;
    }
    if ($check == "") $check = 0;
    else {
        $typePayment = "Check";
        $knowPayment += 1;
    }
    if ($account == "") $account = 0;
    else {
        $typePayment = "Account";
        $knowPayment += 1;
    }
    if ($depositonso == "") $depositonso = 0;
    else {
        $typePayment = "Deposit On So";
        $knowPayment += 1;
    }

    if ($knowPayment >= 2) $typePayment = "Multi";

    $tax = $_POST['tax'];
    $discountPourcent = $_POST['discountPourcent'];
    $discountAmount = $_POST['discountAmount'];
    $grandTotal = $_POST['total'];
    $shipping = $_POST['shipping'];
    $subTotal = $_POST['subTotal'];

    $statut = "P";
    $shipto = $_POST['shipto'];

    if ($discountPourcent == "") $discountPourcent = 0;
    if ($discountAmount == "") $discountAmount = 0;
    if ($tax == "") $tax = 0;
    if ($grandTotal == "") $grandTotal = 0;
    if ($shipping == "") $shipping = 0;
    if ($subTotal == "") $subTotal = 0;

    $code = "I-";
    $InvoiceNumber = fsearchCustCode($con, 'invoice');
    $inc = $inc1 = $quantiteItems = $totalItems = 0;
    $succursale_id = $_SESSION['succursaleid'];
    $datepaiement = test_input($_SESSION["datetoday"]);
    $customers_id = $_POST['customer_id_product'];
    $monnaie = $_POST['currency'];
    $taux = $_POST['exrate'];
    $typeFacture = "I";
    $orderMoney = 1;

    $query_dt = "SELECT * FROM quotedt WHERE quoteidHD = '$quoteIDHD'";
    $fetch_data_dt = mysqli_query($con, $query_dt);
    $row_dt = mysqli_num_rows($fetch_data_dt);

    if ($row_dt > 0) {
        while ($row_dt = mysqli_fetch_assoc($fetch_data_dt)) {
            $inc += 1;
            if ($row_dt['quantite'] > 0) $quantiteItems += $row_dt['quantite'];
            if ($row_dt['quantite2'] > 0) $quantiteItems += $row_dt['quantite2'];
        }
    }
    $fetch_data_dt->close();

    if ($inc > 0) {

        //Update invoice hd
        $sql_update_hd = "UPDATE invoicehd SET code='$code', invoiceNumber=$InvoiceNumber, shipto='$shipto', quantiteItems=$quantiteItems, totalItems=$subTotal, taxes=$tax, discountPourcentage=$discountPourcent, discountAmount=$discountAmount, grandTotal=$grandTotal, statut='$statut', transport=$shipping, typePaiement='$typePayment' WHERE invoiceIDHD=$invoiceIDHD";

        $result_update_hd = mysqli_query($con, $sql_update_hd);
        if ($result_update_hd) {

            //Update invoice dt
            $sql_update_dt = "UPDATE invoicedt SET statut='$statut' WHERE invoiceIDHD=$invoiceIDHD";
            $result_update_dt = mysqli_query($con, $sql_update_dt);

            if ($result_update_dt) {

                //save invoice pay
                $sql_pay = "INSERT INTO invoicepaye (invoiceIDHD, succursale_id, datepaiement, cash, dbcr, cheque, chequeNumber, account, statut, customers_id, orderMoney, monnaie, taux, typeFacture, deposit)
			    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                if ($stmt_pay = mysqli_prepare($con, $sql_pay)) {
                    mysqli_stmt_bind_param(
                        $stmt_pay,
                        "iisdddsdsissdsd",
                        $invoiceIDHD,
                        $succursale_id,
                        $datepaiement,
                        $cash,
                        $debitcredit,
                        $check,
                        $numerocheck,
                        $account,
                        $statut,
                        $customers_id,
                        $orderMoney,
                        $monnaie,
                        $taux,
                        $typeFacture,
                        $depositonso
                    );

                    if (mysqli_stmt_execute($stmt_pay)) {

                        //Update Quantity produit for a sell
                        $query_modify_quote_dt = "SELECT * FROM quotedt WHERE quoteidHD = '$quoteIDHD'";
                        $fetch_data_quote_dt = mysqli_query($con, $query_modify_quote_dt);
                        $row_dt_1 = mysqli_num_rows($fetch_data_quote_dt);

                        if ($row_dt_1 > 0) {
                            while ($row_dt_1 = mysqli_fetch_assoc($fetch_data_quote_dt)) {
                                $produit_id = $row_dt_1['produit_id'];
                                $quantityBuy = $quantityModify = 0;
                                $quantityToModify = returnQuantityProductById($con, $produit_id);
                                $baseunit = returnBaseUnitProductById($con, $produit_id);

                                if ($row_dt_1['quantite'] > 0) {
                                    $quantityBuy = $row_dt_1['quantite'];
                                    $quantityModify = $quantityToModify - ($quantityBuy * $baseunit);
                                }
                                if ($row_dt_1['quantite2'] > 0) {
                                    $quantityBuy = $row_dt_1['quantite2'];
                                    $quantityModify = $quantityToModify - $quantityBuy;
                                }

                                $sql_update_product = "UPDATE produit SET quantitetotal=$quantityModify WHERE produit_id=$produit_id";

                                $sql_update_product_result = mysqli_query($con, $sql_update_product);
                                if ($sql_update_product_result) {
                                    $inc1++;
                                }
                            }
                        }
                        $fetch_data_quote_dt->close();

                        if ($inc1 > 0) {
                            echo true;
                        }
                    } else {
                        die("SQL query failed: " . mysqli_error($con));
                    }
                } else {
                    die("SQL query failed: " . mysqli_error($con));
                }
            } else {
                echo die("SQL query quote dt failed: " . mysqli_error($con));
            }
        } else {
            echo die("SQL query quote hd failed: " . mysqli_error($con));
        }
    } else echo false;
}


/**
 * Fonction for save a invoice dt
 */
else if (isset($_POST["saveInvoiceDtFirstStep"])) {
    $invoiceIDHD = $_POST["saveInvoiceDtFirstStep"];

    $query = mysqli_query($con, "SELECT * FROM quotedt WHERE quoteIDHD = '$quoteIDHD'");
    $fetch_data = mysqli_query($con, $query);
    $row = mysqli_num_rows($fetch_data);
    $data = 0;

    if ($row > 0) {
        while ($row = mysqli_fetch_assoc($fetch_data)) {
            $succursale_id    = $row['succursale_id'];
            $invoiceIDHD = $invoiceIDHD;
            $produit_id    = $row['produit_id'];
            $dateInvoice = $row['dateQuote'];
            $quantite = $row['quantite'];
            $unitPrice1    = $row['unitPrice1'];
            $unitPrice2    = $row['unitPrice2'];
            $statut    = 'E';
            $monnaie = $row['monnaie'];
            $typeFacture = 'I';
            $averageCost = $row['averageCost'];
            $orderUnit = $row['orderUnit'];
            $descriptionUnit = $row['descriptionUnit'];
            $quantite2 = $row['quantite2'];
            $unit_id = $row['unit_id'];
            $itemName = $row['itemName'];
            $itemDescription = $row['itemDescription'];
            $typeItem = $row['typeItem'];

            // Insert Invoice dt
            $sql = "INSERT INTO invoicedt (succursale_id, invoiceIDHD, produit_id, dateInvoice, quantite, unitPrice1,unitPrice2, statut, monnaie, typeFacture, taux, averageCost, orderUnit, descriptionUnit, quantite2, unit_id,itemName, itemDescription, typeItem)
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "iiisdddsssddssdisss",
                    $succursale_id,
                    $invoiceIDHD,
                    $produit_id,
                    $dateInvoice,
                    $quantite,
                    $unitPrice1,
                    $unitPrice2,
                    $statut,
                    $monnaie,
                    $typeFacture,
                    $taux,
                    $averageCost,
                    $orderUnit,
                    $descriptionUnit,
                    $quantite2,
                    $unit_id,
                    $itemName,
                    $itemDescription,
                    $typeItem
                );

                if (mysqli_stmt_execute($stmt)) {
                    $data = mysqli_insert_id($con);
                } else {
                    die("SQL query failed: " . mysqli_error($con));
                }
            } else {
                die("SQL query failed: " . mysqli_error($con));
            }
        }
    }
    $fetch_data->close();
    echo $data;
}






/**
 * Fonction for search code Invoice HD
 */
else if (isset($_POST["codeInvoiceHD"])) {
    $invoiceIDHD = $_POST["codeInvoiceHD"];

    $query = "SELECT * FROM invoicehd WHERE invoiceIDHD=$invoiceIDHD";
    $fetch_data = mysqli_query($con, $query);
    $row = mysqli_num_rows($fetch_data);
    if ($row > 0) {
        while ($row = mysqli_fetch_assoc($fetch_data)) {
            $data = $row['code'] . $row['invoiceNumber'];
        }
    }
    echo $data;
}
