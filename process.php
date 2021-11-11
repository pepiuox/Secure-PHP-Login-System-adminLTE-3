<?php

$npages = $_COOKIE['pages'];
$npg = array();
$call = array();
for ($x = 1; $x <= $npages; $x++) {
    $npg[] = "\$text" . $x . " = \$_POST['text" . $x . "'];" . "\n";
    $call[] = "getData(\$text" . $x . ");" . "\n";
}
$vpages = implode(" ", $npg);
$calls = implode(" ", $call);
$numpgs = '';
$numpgs .= '<?php' . "\n";
$numpgs .= $vpages . "\n";
$numpgs .= $calls . "\n";
$numpgs .= '?>' . "\n";
$filep = 'tempg.php';
$tpfile = fopen($filep, "w") or die("Unable to open file!");
fwrite($tpfile, $numpgs);
fclose($tpfile);

function getData($text) {
    $text = str_replace("á", "a", $text);
    $text = str_replace("é", "e", $text);
    $text = str_replace("í", "i", $text);
    $text = str_replace("ó", "o", $text);
    $text = str_replace("ú", "u", $text);
    $text = str_replace("Á", "A", $text);
    $text = str_replace("É", "E", $text);
    $text = str_replace("Í", "I", $text);
    $text = str_replace("Ó", "O", $text);
    $text = str_replace("Ú", "U", $text);
    //$text = str_replace(":", "", $text);
    $rest = substr($text, 0, -29);
    $cont = "'" . $rest . "'";

    $con = new mysqli("localhost", "root", "truelove", "perucompras");
    $variable = $con->query("SELECT *,
	variable.id_categoria AS idcat
	FROM variable 
	LEFT JOIN categoria ON variable.id_categoria = categoria.id_categoria
	WHERE act_variable=1
	ORDER BY id_variable ASC");

    $vars = array();
    $vnoms = array();
    $catgs = array();
    $cnoms = array();
//$pcont = array();

    while ($row = $variable->fetch_array()) {
        $catgs[] = $row['idcat'];
        $cnoms[] = $row['nom_categoria']; // category names
        $vars[] = $row['id_variable'];
        $vnoms[] = $row['nom_variable']; // variables names
    }

    $catgst = implode("', '", $catgs);
    $cnomst = implode("', '", $cnoms);
    $varst = implode("', '", $vars);
    $vnomst = implode("', '", $vnoms);

    $cts = "'" . $catgst . "'";
    $cno = "'" . $cnomst . "'";
    $vrs = "'" . $varst . "'";
    $vls = "'" . $vnomst . "'";
    $dtls = "'Comp.Anual SIAF','Nro','Ficha - producto','Marca','Codigo Unico de Producto','Cantidad','Importe(PEN)','IMPORTE TOTAL(PEN)'";
    $arrayTxt = '';
    $arrayTxt .= '<?php' . "\n";
    $arrayTxt .= '$pcont = array(' . $cont . ');
    ' . "\n";
    $arrayTxt .= '$ncts = array(' . $cts . ');
    ' . "\n";
    $arrayTxt .= '$ncos = array(' . $cno . ');
    ' . "\n";
    $arrayTxt .= '$nvrs = array(' . $vrs . ');
    ' . "\n";
    $arrayTxt .= '$nvls = array(' . $vls . ');
    ' . "\n";
    $arrayTxt .= '$ndtls = array(' . $dtls . ');
    ' . "\n";
    $arrayTxt .= '$mixv = array(' . $vls . ', ' . $cno . ', ' . $dtls . ');
    ' . "\n";
    $arrayTxt .= '
    ?>' . "\n";

    $filename = 'temp.php';
    $tempfile = fopen($filename, "w") or die("Unable to open file!");
    fwrite($tempfile, $arrayTxt);
    fclose($tempfile);
    include $filename;
    
    $id = 1;
    
    foreach ($ncts as $idc) {
        $idcs = $idc;
    }
    foreach ($nvrs as $idv) {
        $idvs = $idv;
    }
    
    echo '<table class="table-primary">'
    . '<thead>';
    echo '<tr class="table-secondary">';
    foreach ($pcont as $t) {
        if (in_array($t, $nvls)) {
            if ($t === 'COMPRA') {
                echo '<th>';
                echo $t;
                echo '</th>';
                echo '<th>';
                echo 'IM';
                echo '</th>';
            } else {
                echo '<th>';
                echo $t;
                echo '</th>';
            }
        }
    }
    foreach ($ndtls as $dt) {
        echo '<th>';
        echo $dt;
        echo '</th>';
    }
    echo '</thead>';
    echo '<tbody>';
    echo '<tr>';
    foreach ($pcont as $v) {
        if (!empty($mixv)) {
            if (!in_array($v, $mixv)) {
                echo '<td>';
                if (!$v === $ncos) {
                    continue;
                } else {
                    echo $v;
                }
                echo '</td>';
            }
        }
    }
    echo '</tr>';
    echo '</tbody>'
    . '</table>';
}

include $filep;
?>

