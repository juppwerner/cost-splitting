<?php
use yii\bootstrap4\Html;
use app\dictionaries\ExpenseTypesDict;
use app\models\Expense;

$showParticipants = $showParticipants ?? false;
$showFooterHeader = $showFooterHeader ?? false;
$totalProjectCost = 0; 
$participantDetails = []; 
$participantSums = []; 
$sum = 0; 
$participants = array_values($model->participantsList); 
$defaultParticipantDetails = [
    'sumExpenses'=>0, 
    'countExpenses'=>0, 
    'sumExpensesSelf'=>0, 
    'sumExpensesOthers'=>0, 
    'totalProjectValue'=>0, 
    'countExpensesByOthers'=>0, 
    'sumExpensesByOthers'=>0
];
?>
<?php if(count($breakdown)===0) : ?>
<div class="mt-2 mb-3"><?= Yii::t('app', '(no expenses found)') ?></div>

<?php else : ?>

<table class="table table-striped table-responsive table-hover" style="width:100%">

    <thead>
        <?php ob_start(); ?>
        <tr>
            <th><?= Yii::t('app', 'Date') ?></th>
            <th><?= Yii::t('app', 'Title') ?></th>
            <th><?= Yii::t('app', 'Payed By') ?></th>
            <?php if($showParticipants) : ?><th><?= Yii::t('app', 'Recipients') ?></th><?php endif; ?>
            <th><?= Yii::t('app', 'Amount') ?></th>
            <?php if($model->useCurrency) : ?>
            <th><?= Yii::t('app', 'Exchange Rate') ?></th>
            <th><?= Yii::t('app', 'Amount {currency}', ['currency'=>$model->currency]) ?></th>
            <?php endif; ?>
            <?php foreach($participants as $participant) : ?>
            <?php if(!array_key_exists($participant, $participantSums)) $participantSums[$participant] = 0; ?>
            <th colspan="2"><?= $participant ?></th>
            <?php endforeach; ?>
        </tr>
        <?php $headerRow = ob_get_clean(); ?>
        <?= $headerRow; ?>
    </thead>

    <tbody>
        <?php foreach($breakdown as $row) : ?>
        <?php if(!array_key_exists($row->payedBy, $participantDetails)) $participantDetails[$row->payedBy] = $defaultParticipantDetails; ?>
        <tr>
            <td class="text-center">
                <?= Yii::$app->formatter->asDate($row->itemDate, 'php:'.Yii::t('app', 'Y-m-d')) ?>
            </td>

            <td>
                <?= $row->title ?> <?= Html::a('.', ['/expense/update', 'id'=>$row->id], ['class'=>'d-print-none']) ?>
            </td>

            <td class="text-center">
                <?= $row->payedBy ?>
            </td>

            <?php if($showParticipants) : ?><td class="text-center">
                <?= $row->splitting==Expense::SPLITTING_EQUAL ? join(', ', $model->participantsList) : str_replace(';', ', ', $row->participants) ?>
            </td><?php endif; ?>
            
            <td class="text-right">
                <?= Yii::$app->formatter->asCurrency($row->amount, $row->currency) ?>
            </td>

            <?php if($model->useCurrency) : ?>
            <td class="text-right">
                <?= $row->exchangeRate ?>
            </td>
            <!-- <?php // endif; ?>

            <?php // if($model->useCurrency) : ?> -->
            <td class="text-right">
                <?= Yii::$app->formatter->asCurrency($row->amount * $row->exchangeRate, $model->currency) ?>
            </td>
            <?php endif; ?>

            <!-- <td class="text-center"><?= Yii::$app->formatter->asDate($row->created_at, 'php:'.Yii::t('app', 'Y-m-d')) ?></td> -->
            <?php foreach($participants as $participant) : ?>
            <?php if(!array_key_exists($participant, $participantDetails)) $participantDetails[$participant] = $defaultParticipantDetails; ?>
            <td style="color:darkseagreen" class="text-right">
                <?php if($participant==$row->payedBy) : ?>
                    <?= Yii::$app->formatter->asDecimal($row->amount  * $row->exchangeRate, 2) ?>
                    <?php $participantSums[$participant] += $row->amount  * $row->exchangeRate; ?>
                    <?php if($row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) : ?>
                        <?php $participantDetails[$participant]['sumExpenses'] += $row->amount  * $row->exchangeRate; $participantDetails[$participant]['countExpenses']++; ?>
                    <?php endif; // !money transfer ?>
                <?php endif; // part. == payedBy ?>
            </td>
            <td style="color: red" class="text-right">
                <?php foreach($row->costitems as $costitem) : ?>
                <?php if($costitem->participant==$participant) : ?>
                    <?= Yii::$app->formatter->asDecimal($costitem->amount * $costitem->exchangeRate, 2) ?>
                    <?php $participantSums[$participant] -= $costitem->amount * $costitem->exchangeRate; ?>
                    <?php if($row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) : ?>
                        <?php $totalProjectCost += $costitem->amount * $costitem->exchangeRate; ?>
                        <?php if($row->payedBy===$participant) : ?>
                            <?php $participantDetails[$participant]['sumExpensesSelf'] += $costitem->amount * $costitem->exchangeRate; ?>
                        <?php else : ?>
                            <?php $participantDetails[$participant]['sumExpensesByOthers'] += $costitem->amount * $costitem->exchangeRate; ?>
                            <?php $participantDetails[$participant]['countExpensesByOthers'] ++; ?>
                        <?php endif; ?>
                        <?php $participantDetails[$participant]['totalProjectValue'] += $costitem->amount * $costitem->exchangeRate; ?>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if($row->payedBy===$participant and $row->expenseType !== ExpenseTypesDict::EXPENSETYPE_TRANSFER) : ?>
                        <?php $participantDetails[$row->payedBy]['sumExpensesOthers'] += $costitem->amount * $costitem->exchangeRate; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php endforeach; // costitems loop ?>
            </td>
            <?php endforeach; // participants col. loop ?>
        </tr>
        <?php endforeach; // breakdown rows loop ?>

        <!-- Participants Sums Row -->
        <tr>
            <td colspan="<?= ($showParticipants ? 5 : 4 ) + (int)$model->useCurrency*2 ?>">&nbsp;</td>
            <?php foreach($participants as $participant) : ?>
            <?php $sum += $participantSums[$participant]; ?>
            <td colspan="2" class="text-right"><?= Yii::$app->formatter->asCurrency($participantSums[$participant], 'EUR') ?></td>
            <?php endforeach; ?>
        </tr>

    </tbody>

    <?php if($showFooterHeader) : ?>
    <thead>
        <?= $headerRow ?>
    </thead>
    <?php endif; ?>
</table>

<?php endif; ?>