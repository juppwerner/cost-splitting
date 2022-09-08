Lookup
=================


Step # 1:
=========

To add a lookup table to your database, following is the sql for lookup:

	CREATE TABLE IF NOT EXISTS `lookup` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `type` varchar(100) DEFAULT NULL,
	  `name` varchar(100) DEFAULT NULL,
	  `code` int(11) DEFAULT '1',
	  `comment` text,
	  `active` tinyint(1) DEFAULT '1',
	  `sort_order` int(11) DEFAULT '1',
	  `created_at` int(11) DEFAULT NULL,
	  `created_by` int(11) DEFAULT NULL,
	  `updated_by` int(11) DEFAULT NULL,
	  `updated_at` int(11) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `CK_Type_Name_Unique` (`type`,`name`),
	  KEY `created_by` (`created_by`),
	  KEY `updated_by` (`updated_by`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

Or:

Configuration
-------------

**Database Migrations**

Before usage this extension, we'll also need to prepare the database.

```
php yii migrate --migrationPath=@app/modules/lookup/migrations
```

/**************************************/


Step # 2:
=========

To access the lookup functionality anywhere in you application (either frontend or backend) follow the following steps:

	In your main.php under config folder add the following:
		'components' => [
			---
	        'lookup' => [
	            'class' => 'app\models\Lookup',
	        ],
	        ---
	    ]

Step # 3:
=========

Following are the few usage of lookup functionality:


	/*** dropdown list from lookup ***/

	<?= $form->field($model, 'active')->dropDownList(
		Yii::$app->lookup->items('yes_no'),
		//['1'=>'Active', '2' => 'Pending'],
		['prompt'=>'--- Select ---'] 
	) ?>


	/*** RadioButton List ***/

	<?= $form->field($model, 'gender')->radioList(
		Yii::$app->lookup->items('male_female'), ['separator' => '']
	) ?>


	/*** CheckBoxes List ***/

	<?= $form->field($model, 'language')->checkboxList(
	        Yii::$app->lookup->items('language'), ['separator' => '']
	    ) ?>


	/*** Dropdown List from Lookup ***/

	<?= $form->field($model, 'language')->dropDownList(
	        Yii::$app->lookup->items('language'), ['prompt' => '--- Select ---']
	    ) ?>
