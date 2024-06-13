<?php
$csvSep = "\t";
$projectCurrency = 'EUR';

$participants = [ // {{{ 
    'Anna',
    'Ben',
    'Clara'
]; // }}} 
// {{{ $expensesTSV
$expensesTSV = <<< EOL
Datum	Name	Ausgabe	Währung	Kurs	Betrag	für was	Aufteilung	Gewichtung
01.08.2022	Anna	39,95	USD	1	0	Internet 7/2022	EQUAL	1/1/1
03.08.2022	Ben	365,00	€	1	0	Miete 8/2022	PERCENTAGE	0.333333/0/0.333333
04.08.2022	Clara	96,00	€	1	0	Abschlag Stadtwerke	EQUAL	1/1/1
11.08.2022	Anna	13,79	€	1	0	Putzmittel	EQUAL	1/1/1
13.08.2022	Clara	14,50	€	1	0	Obst und Gemüse	EQUAL	1/1/1
13.08.2022	Ben	77,60	€	1	0	Getränke Party	EQUAL	1/1/1
-	Ben	28,56	€	1	0	Lebensmittel Rewe	PERCENTAGE	0/1/1
EOL; // }}} 

$expensesTSV = <<< EOL
Datum	Name	Ausgabe	Währung	Kurs	Betrag	für was	Aufteilung	Gewichtung
01.08.2022	Anna	40	USD	1	0	Internet 7/2022	PERCENTAGE	0.33/0.33/0.33
EOL;

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
    foreach($participants as$n=> $participant) {
        $participantParticipation[$participant] = $participantParticipation[$participant] ?? 0;
        switch($method){
            case 'EQUAL';
                // $flags doesn't matter
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
function array2table($array)
{
    ob_start();
?>
<?php if (count($array) > 0): ?>
<table border="1">
  <thead>
    <tr>
      <th><?php echo implode('</th><th>', array_keys(current($array))); ?></th>
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

        <h2>t1:</h2>
        <?= array2table($t1); ?>

        <h2>Compensation:</h2>
        <?= array2table($compensation); ?>

        <h2>Settlements</h2>
        <?php for($i=1; $i<count($compensation); $i++) : ?>
        <strong><?= sprintf('%s pays to %s %0.2f %s.', $participants[$compensation[$i]['Debitor']-1], $participants[$compensation[$i]['Recipient']-1], $compensation[$i]['Amount'], $projectCurrency) ?></strong><br>
        <?php endfor; ?>
    </body>
</html>
