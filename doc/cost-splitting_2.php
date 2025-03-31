<?php
/**
 * Berechnung siehe:
 * https://www.exceltactics.com/how-to-split-bills-and-share-expenses-using-a-free-excel-spreadsheet/
 */

$csvSep = "\t";
$projectCurrency = 'EUR';






// {{{ $expensesTSV
/*
$participants = [ // {{{ 
    'Anna',
    'Ben',
    'Clara'
]; // }}} 
*/
/*
$expensesTSV = <<< EOL
Datum	Name	Ausgabe	Währung	Kurs	Betrag	für was	Aufteilung	Gewichtung
01.08.2022	Anna	39,95	USD	1	0	Internet 7/2022	EQUAL	1/1/1
03.08.2022	Ben	365,00	€	1	0	Miete 8/2022	EQUAL	1/1/1
04.08.2022	Clara	96,00	€	1	0	Abschlag Stadtwerke	EQUAL	1/1/1
11.08.2022	Anna	13,79	€	1	0	Putzmittel	EQUAL	1/1/1
13.08.2022	Clara	14,50	€	1	0	Obst und Gemüse	EQUAL	1/1/1
13.08.2022	Ben	77,60	€	1	0	Getränke Party	EQUAL	1/1/1
-	Ben	28,56	€	1	0	Lebensmittel Rewe	EQUAL	1/1/1
EOL; // }}} 
 */

// {{{ $expensesTSV
$participants = [ // {{{ 
    'Rainer',
    'Joachim',
    // 'Susanne',
    // 'Sabine'
]; // }}} 

// Replace Payed By with these names:
$replaceNames = [
    'Susanne' => 'Rainer',
    'Sabine' => 'Joachim',
];

$expensesTSV = <<< EOL
Date	Name	Expense	Currency	exchangeRate	Amount	What	Method	Weights
2024-08-02	Joachim	18.86	EUR	1	18.86	Bier und Chips	EQUAL	1/1/1/1
2024-08-02	Susanne	150.00	EUR	1	150	Essen	EQUAL	1/1/1/1
2024-08-03	Joachim	60.00	EUR	1	60	Stadtrundfahrt 	EQUAL	1/1/1/1
2024-08-03	Sabine	47.00	EUR	1	47	Currywurst und Bier am Pier 7	EQUAL	1/1/1/1
2024-08-03	Joachim	46.54	EUR	1	46.54	Einkauf Norma	EQUAL	1/1/1/1
2024-08-03	Rainer	35.00	EUR	1	35	Eiscafé 	EQUAL	1/1/1/1
2024-08-04	Rainer	20.00	EUR	1	20	Eintritt Freilichtmuseum	EQUAL	1/1/1/1
2024-08-04	Rainer	16.00	EUR	1	16	Rhabarberschorle	EQUAL	1/1/1/1
2024-08-04	Joachim	17.30	EUR	1	17.3	Frühstück Tankstelle	EQUAL	1/1/1/1
2024-08-04	Joachim	33.60	EUR	1	33.6	Kuchen Café Prag	EQUAL	1/1/1/1
2024-08-04	Rainer	15.00	EUR	1	15	Getränke/Tanke Schwerin	EQUAL	1/1/1/1
2024-08-05	Rainer	26.00	EUR	1	26	Bäckerei Lübz	EQUAL	1/1/1/1
2024-08-05	Joachim	6.52	EUR	1	6.52	Brötchen Frühstück 	EQUAL	1/1/1/1
2024-08-05	Joachim	12.10	EUR	1	12.1	Wurst Käse Butter Norma	EQUAL	1/1/1/1
2024-08-05	Susanne	111.91	EUR	1	111.91	NETTO 	EQUAL	1/1/1/1
2024-08-05	Susanne	15.00	EUR	1	15	Getränke/Abfahrt Lübz	EQUAL	1/1/1/1
2024-08-05	Susanne	10.00	EUR	1	10	Trinkgeld Einweiser	EQUAL	1/1/1/1
2024-08-06	Joachim	38.14	EUR	1	38.14	Bier Olivenöl Wasser REWE	EQUAL	1/1/1/1
2024-08-06	Joachim	28.00	EUR	1	28	Hafengebühr Plau am See	EQUAL	1/1/1/1
2024-08-06	Joachim	4.00	EUR	1	4	Kurtaxe Plau am See	EQUAL	1/1/1/1
2024-08-07	Sabine	9.15	EUR	1	9.15	Bäckerei Plau	EQUAL	1/1/1/1
2024-08-07	Susanne	14.00	EUR	1	14	Einkauf in Waren	EQUAL	1/1/1/1
2024-08-07	Joachim	68.80	EUR	1	68.8	Hafengebühr Waren	EQUAL	1/1/1/1
2024-08-08	Sabine	9.53	EUR	1	9.53	Brötchen Waren	EQUAL	1/1/1/1
2024-08-08	Joachim	41.00	EUR	1	41	Hafengebühr Plau am See inkl. Dusch/Strom/Kurtaxe	EQUAL	1/1/1/1
2024-08-08	Joachim	43.61	EUR	1	43.61	Einkauf REWE Plau am See	EQUAL	1/1/1/1
2024-08-09	Susanne	12.55	EUR	1	12.55	Bäckerei Plau 2	EQUAL	1/1/1/1
2024-08-09	Joachim	40.00	EUR	1	40	Hafengebühr Plau am See inkl. Dusch/Strom/Kurtaxe	EQUAL	1/1/1/1
2024-08-09	Susanne	19.60	EUR	1	19.6	Lebensmittel REWE Plau	EQUAL	1/1/1/1
2024-08-10	Sabine	9.82	EUR	1	9.82	Bäckerei Plau 3	EQUAL	1/1/1/1
2024-08-10	Susanne	30.68	EUR	1	30.68	NETTO Markt in Parchim	EQUAL	1/1/1/1
2024-08-10	Sabine	18.00	EUR	1	18	Hafengebühr Parchim	EQUAL	1/1/1/1
2024-08-10	Joachim	109.00	EUR	1	109	Pizzeria Parchim inkl. Trinkgeld	EQUAL	1/1/1/1
EOL; // }}} 

/*
$expensesTSV = <<< EOL
Datum	Name	Ausgabe	Währung	Kurs	Betrag	für was	Aufteilung	Gewichtung
01.08.2022	Anna	40	USD	1	0	Internet 7/2022	PERCENTAGE	0.33/0.33/0.33
EOL;
 */

$expensesLines = explode("\n", str_replace("\r\n", "\n", $expensesTSV));
// print_r($expensesLines);

$display = [];

$participantExpenses = [];
$participantParticipation = [];
$participantBalance = [];

foreach($participants as $participant) 
    $participantExpenses[$participant] = 0;

foreach($expensesLines as $n=>$line) {
    if($n==0)
        continue;
    // echo 'line: '.$line.'<br />';
    list($date, $name, $expense, $currency, $exchangeRate, $amount, $what, $method, $weights) = explode($csvSep, $line);
    $expense = str_replace([',', ' €'], ['.', ''], $expense);
    $amount =  $expense * $exchangeRate;
    $weights = explode('/', $weights);
    if(array_key_exists($name, $replaceNames)) {
        $name = $replaceNames[$name];
    }
    // Validate row
    $validation = 'OK';
    switch($method){
        case 'PERCENTAGE':
            if(abs(1-array_sum($weights))>0.009)
                $validation = sprintf('Sum of weights %s is not equal to 1', array_sum($weights));
            break;
        case 'AMOUNT':
            if(array_sum($weights)!=$expense)
                $validation = sprintf('Sum of weights %0.2f is not equal to expense %0.2f', array_sum($weights), $expense);
            break;
    }
    $display[] = [
        'date' => $date,
        'name' => $name,
        'expense' => $expense,
        'currency' => $currency,
        'exchangeRate' => $exchangeRate,
        'amount' => sprintf('%0.2f', $amount),
        'what' => $what,
        'method' => $method,
        'weights' => join(' / ', $weights),
        'validation' => $validation,
    ];
    // echo 'name: '.$name.', expense: '.$expense.', what: '.$what.'<br>';
    $participantExpenses[$name] += $expense * $exchangeRate;
    foreach($participants as $n=> $participant) {
        $participantParticipation[$participant] = $participantParticipation[$participant] ?? 0;
        switch($method){
            case 'EQUAL';
                // $weights doesn't matter
                $participantParticipation[$participant] += $amount / count($participants);
                break;
            case 'PERCENTAGE':
                $participantParticipation[$participant] += $weights[$n] * $amount / array_sum($weights);
                break;
            case 'AMOUNT':
                $participantParticipation[$participant] += $weights[$n] * $exchangeRate;
                break;
        }
    }
}
foreach($participants as $participant)
    $participantBalance[$participant] = -($participantExpenses[$participant] - $participantParticipation[$participant]);

// Initialise array with settlement transactions
$t1 = array();
foreach($participants as $n=>$participant)
    $t1[0][$n] = $participantBalance[$participant];

// Initialise array with compensation payments
$compensation = array(
    0 => array(
        'Recipient' => '',
        'Debitor' => '', 
        'Amount' => '',
    ),
);

// Calculate compensations and settlement transactions
$r=1;
// only continue if there are any amounts in previous row which are bigger than 0.01
while(max($t1[$r-1])>0.01) {
    $min = min($t1[$r-1]);
    $max = max($t1[$r-1]);
    // Get Index of participant who paid most (amount is most negative one)
    $compensation[$r]['Recipient'] = array_search($min, $t1[$r-1])+1;
    // Get Index of participant who received most ( amount is most positive one)
    $compensation[$r]['Debitor'] = array_search($max, $t1[$r-1])+1;
    // Get amount as minimum of absolute amounts of recipient or debitor
    $compensation[$r]['Amount'] = min(abs($t1[$r-1][$compensation[$r]['Recipient']-1]), abs($t1[$r-1][$compensation[$r]['Debitor']-1]));
    // add t1 row;
    $t1[$r] = $t1[$r-1];
    // Reduce Amount at Debitor 
    $t1[$r][$compensation[$r]['Debitor']-1] -= $compensation[$r]['Amount'];
    // Add Amount to Recipient
    $t1[$r][$compensation[$r]['Recipient']-1] += $compensation[$r]['Amount'];
    $r++;
}


// Helper functions
// print_r an array surounded with <pre>
function par($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

// Print an array as HTML table
function array2table($array,$headers = null)
{
    if(is_null($headers))
        $headers = array_keys(current($array));

    ob_start();
?>
<?php if (count($array) > 0): ?>
<table border="1">
  <thead>
    <tr>
      <th><?php echo implode('</th><th>',$headers); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($array as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td><?php echo implode('</td><td>', $row); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
<?php    
    return ob_get_clean();
}

$merged = $compensation;
foreach($merged as $rowIdx=>$cells) {
    foreach($cells as $col=>$value) {
        if($col=='Recipient' and trim($value)!=='' and array_key_exists((int)($value-1), $participants))
            $merged[$rowIdx]['Recipient'] = $participants[$value-1];
        if($col=='Debitor' and trim($value)!=='' and array_key_exists((int)($value-1), $participants))
            $merged[$rowIdx]['Debitor'] = $participants[$value-1];
    }
}
foreach($t1 as $rowIdx=>$cells) {
    foreach($cells as $colIdx=>$cell) {
        $merged[$rowIdx][$participants[$colIdx]] = $cell;
    }
}
?>
<!doctype html>
<html>
    <head>
        <title>Cost Splitting</title>
    </head>
    <body>
        <h1>Cost Splitting</h1>

        <h2>Participants</h2>
        <?= array2table(array($participants)); ?>

        <h2>Expenses Data:</h2>
        <?= array2table($display) ?>

        <h2>Expenses:</h2>
        <?= array2table(array(0=>$participantExpenses)); ?>

        <h2>Participation:</h2>
        <?= array2table(array($participantParticipation)); ?>

        <h2>Balance:</h2>
        <?= array2table(array($participantBalance)); ?>

        <h2>Compensation:</h2>
        <?= array2table($merged); ?>

        <h2>Settlements:</h2>
        <?php for($i=1; $i<count($compensation); $i++) : ?>
        <strong><?= sprintf('%s pays to %s %0.2f %s.', $participants[$compensation[$i]['Debitor']-1], $participants[$compensation[$i]['Recipient']-1], $compensation[$i]['Amount'], $projectCurrency) ?></strong><br>
        <?php endfor; ?>
    </body>
</html>
