<?php
$participants = [ // {{{ 
    'Anna',
    'Ben',
    'Clara'
]; // }}} 
// {{{ $expensesTSV
$expensesTSV = <<< EOL
Name	Ausgabe	für was
Anna	39,95 €	Internet 7/2022
Ben	365,00 €	Miete 8/2022
Clara	96,00 €	Abschlag Stadtwerke
Anna	13,79 €	Putzmittel
Clara	14,50 €	Obst und Gemüse
Ben	77,60 €	Getränke Party
Ben	28,56 €	Lebensmittel Rewe
EOL; // }}} 
$expensesLines = explode("\n", str_replace("\r\n", "\n", $expensesTSV));
// print_r($expensesLines);

$participantExpenses = [];
$participantDiffToAvg = [];
foreach($participants as $participant) 
    $participantExpenses[$participant] = 0;

foreach($expensesLines as $n=>$line) {
    if($n==0)
        continue;
    // echo 'line: '.$line.'<br />';
    list($name, $expense, $what) = explode("\t", $line);
    $expense = str_replace([',', ' €'], ['.', ''], $expense);
    // echo 'name: '.$name.', expense: '.$expense.', what: '.$what.'<br>';
    $participantExpenses[$name] += $expense;
}


// Calculate average
$average = array_sum(array_values($participantExpenses))/count(array_values($participantExpenses));

// Diff to average
foreach($participants as $participant) 
    $participantDiffToAvg[$participant] = $participantExpenses[$participant] - $average;

$matrix = [];
foreach($participants as $iR=>$pR) {
    // echo $iR.' ' . $pR . '<br>';
    // echo '<li>';
    $matrix[$pR]= [];
    foreach($participants as $iSp=>$pSp) {
        // echo '[ '.$iSp.' '.$pSp;
        // echo ' ';
        $matrix[$pR][$pSp] = null;
        // Zeile <> Spalte?
        if($pR==$pSp) {
            $matrix[$pR][$pSp] = 0;
            // echo ' ]';
            continue;
        }
        if($participantExpenses[$pR]>$average) {
            $matrix[$pR][$pSp] = 0;
            // echo ' ]';
            continue;
        }
        // echo '('.zeilensumme($pR).'|'.spaltensumme($pSp, $iR).') ';
        $f1 = zeilensumme($pR)<>$average;
        $f2 = $participantExpenses[$pSp]>=$average;
        $f3 = (spaltensumme($pSp, $iR)+$participantExpenses[$pSp])>$average;
        $f4 = $average - zeilensumme($pR, $iR);
        $f5 = -1*spaltensumme($pSp, $iR) + $participantExpenses[$pSp] - $average;

        // echo $f1.' | '.$f2.' | '.$f3.' | '.$f4.' | '.$f5;
        $cell = ($participantExpenses[$pR]<=$average)*(
            $f1
            * $f2
            * (
                $f3
                * min(
                    $f4,
                    $f5
                )
            )
        );
        $matrix[$pR][$pSp] = sqrt($cell*$cell);
        // echo ' ]';

    }
    // echo '<br>';
}

// in E5: B5:D5
function zeilensumme($p) // {{{ 
{
    Global $participants;
    Global $participantExpenses;
    Global $matrix;

    $pKeys = array_flip($participants);
    $sum = $participantExpenses[$p];
    foreach($participants as $iSp=>$pSp) {
        if($pKeys[$p]>=$iSp)
            break;
        $sum += $matrix[$p][$pSp];
    }
    return $sum;
} // }}} 
function spaltensumme($p, $zeile) // {{{ 
{
    Global $participants;
    Global $matrix;

    $sum = 0;
    foreach($participants as $iZ=>$pZ) {
        if($iZ>=$zeile)
            break;
        $sum += $matrix[$pZ][$p];
    }    
    return $sum;
} // }}} 

/* in E5:
    =($A5<>E$2)*(                                                   // Name Z <> Name Sp?
        ($B5<=$B$1)*(                                               // $expense[$pR]<=$average
            (SUMME($B5:D5)<>$B$1)                                   // f1 Zeilensumme bis links vorher
            * (INDEX($B:$B;SPALTE())>=$B$1)                         // f2
            * (
                (SUMME(E$2:E4; INDEX($B:$B;SPALTE()))>$B$1)         // f3 Spaltensumme + expense > average
                * (MIN(
                    $B$1-SUMME($B5:D5);                             // f4 $average - zeilensumme
                    -SUMME(E$2:E4) + INDEX($B:$B;SPALTE()) - $B$1)  // f5 spaltensumme + $expense - $average
                )
            )
        )
    )
 */
// $B$5 = $average

?>
<!doctype html>
<html>
    <head>
        <title>Cost Splitting</title>
    </head>
    <body>
        <h1>Cost Splitting</h1>
        Average: <?= $average ?><br /><br />

        Expenses:<br />
        <?php print_r($participantExpenses); ?><br /><br />

        Diff to Average:<br>
        <?php print_r($participantDiffToAvg); ?><br /><br />

        Matrix:<br>
        <pre><?php print_r($matrix); ?></pre><br /><br />

        <table border="1">
            <thead>
                <tr>
                    <th>Name</th><th>gezahlt</th>
                    <?php foreach($participants as $participant) : ?>
                    <th><?= $participant ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($matrix as $pR=>$row) : ?>
                <tr>
                    <td><?= $pR ?></td>
                    <td><?= $participantExpenses[$pR] ?></td>
                    <?php foreach($row as $pSp=>$amount) : ?>
                    <?php if($pR==$pSp) : ?>
                    <td>X</td>
                    <?php else : ?>
                    <td><?= $amount ?></td>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php foreach($matrix as $pR=>$row) : ?>
        <?php foreach($row as $pSp=>$amount) : if($amount==0) continue; ?>
        <?= $pR ?> zahlt an <?= $pSp ?>: <?= $amount ?><br>
        <?php endforeach; ?>
        <?php endforeach; ?>
</table>
    </body>
</html>
