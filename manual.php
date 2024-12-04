<?php
session_start();
require_once 'config.php';
require_once 'tcpdf/tcpdf.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = require 'languages/' . $_SESSION['lang'] . '.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

$email = $_SESSION["email"];

// Funkcia na generovanie PDF zo zadaného HTML obsahu
function generatePDF($htmlContent) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Nastavenie vlastností dokumentu
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Manual');
    $pdf->SetSubject('Manual');
    $pdf->SetKeywords('Manual, PDF, export');

    $pdf->SetFont('dejavusans', '', 12);
    $pdf->AddPage();
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    return $pdf->Output('manual.pdf', 'S');
}

// Funkcia na obsah stránky manual_container
function generateManualContent() {
    ob_start();
    ?>
    <h3>Príručka</h3>
    <div class="manual_container">
        <?php
        $image_path = "images/manual/1.jpg";

        // Kontrola existence suboru
        if(file_exists($image_path)) {
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 1"></div>';
            echo '<div class="manual_text"><p>Po načítaní webstránky sa zobrazí prihlasovacia obrazovka.<br>Na každej stránke ma používateľ možnosť zmeniť jazyk medzi slovenčinou a angličtinou.<br>Na tejto prihlasovacej stránke sa môže používateľ prihlásiť (ak má vytvorené konto) s možnosťou prihlásiť sa ako administrátor.<br> Ďalej sa môže presmerovať na stránku registrácie alebo zobraziť otázku pomocou kódu.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/2.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 2"></div>';
            echo '<div class="manual_text"><p>Toto je stránka určená pre registráciu</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/3.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 3"></div>';
            echo '<div class="manual_text"><p>Po zadaní kódu otázku, na prihlasovacej obrazovke, sa používateľovi zobrazí daná otázka s možnosťou odpovede</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/4.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 4"></div>';
            echo '<div class="manual_text"><p>Toto je hlavná stránka ktorá obsahuje všekty fukcionality. Obsahuje navigačnú lištu ktorá sa vyskytuje na väčšine ostatných stránok.<br>Na navigačnej lište sa nachádza zmena jazyka, stránka "Otázky" (domovská obrazovka), stránka "Vytvoriť otázku", stránka "Zobraz výsledky" a stránka "Príručka" na ktorej sa práve nachádzame.<br>Na pravej strane navigačnej lišty sa nachádza mail používateľa ktorý je práve prihlásený a hneď za ním informácia o tom či je admin. Následne su tam možnosti správy používateľov, zmeny hesla alebo odhlásenia sa.<br>Obsah stránky tvorí zoznam otázok s ktorými je možné robiť rôzne činosti. Zoznam je možne zoradiť podľa predmetu alebo dátumu. Tlačidlom "export" si používateľ stiahne export otázok vo formáte JSON do svojho zariadenia.<br>V riadku tabuľky sa spolu s informáciami o otázke nachádzajú aj tlačidla na vymazanie, úpravu alebo kopírovanie otázky a taktiež tlačidlá na určenie či je otázka aktívna a tlačidlo na uzavretie hlasovania.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/5.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 5"></div>';
            echo '<div class="manual_text"><p>Toto je stránka určená pre vytvorenie otázky. Používateľ si môže v prvom selecte určiť či to bude otvorená otaźka alebo otázka s možnosťami.<br>V ďalśom selecte si môže určit v akom mene bude otázka vytvorená. Ak nie je administrátor tak jedinou možnosťou bude jeho vlastný mail. Do formuláru následne zadá znenie otázky a skratku predmetu a ak je to otázka s možnosťami tak tieto možnosti definuje.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/6.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 6"></div>';
            echo '<div class="manual_text"><p>Toto je vytváranie otázky s otvorenou odpoveďou. Pri nej si použivateľ volí či odpovede budú v formáte položiek zoznamu alebo Word Cloud.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/7.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 7"></div>';
            echo '<div class="manual_text"><p>Toto je stránka pre zobrazenie výsledkov. Na tejto stránke sú zobrazené otázky spolu s tým ako nane používatelia odpovedali.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/8.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 8"></div>';
            echo '<div class="manual_text"><p>Toto je stránka ktorá umožňuje adminovi spravovať všetkých registrované kontá.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/9.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 9"></div>';
            echo '<div class="manual_text"><p>Na tejto stránke si používateľ môže zmeniť heslo.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        echo '<br><br>';
        ?>
    </div>
    <?php
    return ob_get_clean();
}

// Obsluha požiadavky na export do PDF
if (isset($_POST['export_pdf'])) {
    $manualContent = generateManualContent();
    $pdfContent = generatePDF($manualContent);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="manual.pdf"');
    echo $pdfContent;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['manual']; ?></title>
    <link href="https://cdn.datatables.net/v/dt/dt-2.0.1/r-3.0.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css/manual.css">
    <link rel="stylesheet" href="css/main_page.css">
    <link rel="stylesheet" href="css/navbar.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <a href="?lang=sk"><img src="images/Flag_of_Slovakia.png" alt="SK" style="height: 20px; width: 30px"></a>
            ./.
            <a href="?lang=en"><img src="images/Flag_of_the_United_Kingdom.png" alt="EN"
                                    style="height: 20px; width: 30px"></a>

            <li class="nav-item active">
                <a class="nav-link" href="main_page.php"><?php echo $lang['questions']; ?><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create_question.php"><?php echo $lang['create_question']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="show_results.php"><?php echo $lang['show_results']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manual.php"><?php echo $lang['manual']; ?></a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <div class="nav-link"><?php echo $email; ?></div>
            </li>
            <li class="nav-item">
                <div class="nav-link">
                    <?php
                    if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
                        echo "(Admin)";
                    }
                    ?>
                </div>
            </li>

            <?php
            if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
                echo '<li class="nav-item">
            <a class="nav-link" href="manage_users.php">' . $lang['manage_users'] . '</a>
          </li>';
            }
            ?>

            <li class="nav-item">
                <a class="nav-link" href="change_password.php"><?php echo $lang['change_password']; ?></a>
            </li>
            <li class="nav-item">
                <a class="btn btn-danger" href="logout.php"><?php echo $lang["logout"]; ?></a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3><?php echo $lang["manual"]; ?></h3>

    <div class="select-boxes-div">
        <form method="post">

            <button type="submit" name="export_pdf" id="export_button">Export</button>
        </form>
    </div>

    <div class="manual_container">
        <?php
        $image_path = "images/manual/1.jpg";

        if(file_exists($image_path)) {
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 1"></div>';
            echo '<div class="manual_text"><p>Po načítaní webstránky sa zobrazí prihlasovacia obrazovka.<br>Na každej stránke ma používateľ možnosť zmeniť jazyk medzi slovenčinou a angličtinou.<br>Na tejto prihlasovacej stránke sa môže používateľ prihlásiť (ak má vytvorené konto) s možnosťou prihlásiť sa ako administrátor.<br> Ďalej sa môže presmerovať na stránku registrácie alebo zobraziť otázku pomocou kódu.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/2.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 2"></div>';
            echo '<div class="manual_text"><p>Toto je stránka určená pre registráciu</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/3.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 3"></div>';
            echo '<div class="manual_text"><p>Po zadaní kódu otázku, na prihlasovacej obrazovke, sa používateľovi zobrazí daná otázka s možnosťou odpovede</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/4.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 4"></div>';
            echo '<div class="manual_text"><p>Toto je hlavná stránka ktorá obsahuje všekty fukcionality. Obsahuje navigačnú lištu ktorá sa vyskytuje na väčšine ostatných stránok.<br>Na navigačnej lište sa nachádza zmena jazyka, stránka "Otázky" (domovská obrazovka), stránka "Vytvoriť otázku", stránka "Zobraz výsledky" a stránka "Príručka" na ktorej sa práve nachádzame.<br>Na pravej strane navigačnej lišty sa nachádza mail používateľa ktorý je práve prihlásený a hneď za ním informácia o tom či je admin. Následne su tam možnosti správy používateľov, zmeny hesla alebo odhlásenia sa.<br>Obsah stránky tvorí zoznam otázok s ktorými je možné robiť rôzne činosti. Zoznam je možne zoradiť podľa predmetu alebo dátumu. Tlačidlom "export" si používateľ stiahne export otázok vo formáte JSON do svojho zariadenia.<br>V riadku tabuľky sa spolu s informáciami o otázke nachádzajú aj tlačidla na vymazanie, úpravu alebo kopírovanie otázky a taktiež tlačidlá na určenie či je otázka aktívna a tlačidlo na uzavretie hlasovania.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/5.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 5"></div>';
            echo '<div class="manual_text"><p>Toto je stránka určená pre vytvorenie otázky. Používateľ si môže v prvom selecte určiť či to bude otvorená otaźka alebo otázka s možnosťami.<br>V ďalśom selecte si môže určit v akom mene bude otázka vytvorená. Ak nie je administrátor tak jedinou možnosťou bude jeho vlastný mail. Do formuláru následne zadá znenie otázky a skratku predmetu a ak je to otázka s možnosťami tak tieto možnosti definuje.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/6.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 6"></div>';
            echo '<div class="manual_text"><p>Toto je vytváranie otázky s otvorenou odpoveďou. Pri nej si použivateľ volí či odpovede budú v formáte položiek zoznamu alebo Word Cloud.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/7.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 7"></div>';
            echo '<div class="manual_text"><p>Toto je stránka pre zobrazenie výsledkov. Na tejto stránke sú zobrazené otázky spolu s tým ako nane používatelia odpovedali.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }

        $image_path = "images/manual/8.jpg";

        if(file_exists($image_path)) {
            echo '<br><br>';
            echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 8"></div>';
            echo '<div class="manual_text"><p>Toto je stránka ktorá umožňuje adminovi spravovať všetkých registrované kontá.</p></div>';
        } else {
            echo "Obrazok nie je k dispozicii";
        }


        $image_path = "images/manual/9.jpg";

        if(file_exists($image_path)) {
        echo '<br><br>';
        echo '<div class="manual_image"><img src="'.$image_path.'" alt="Obrazok 9"></div>';
        echo '<div class="manual_text"><p>Na tejto stránke si používateľ môže zmeniť heslo.</p></div>';
        } else {
        echo "Obrazok nie je k dispozicii";
        }

        echo '<br><br>';
        ?>
    </div>
</div>

</body>

</html>
