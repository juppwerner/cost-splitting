<?php
namespace app\dictionaries;

use Yii;

abstract class CurrencyCodesDictEwf
{    
    Const AUD = 'AUD';
    Const BGN = 'BGN';
    Const BRL = 'BRL';
    Const CAD = 'CAD';
    Const CHF = 'CHF';
    Const CNY = 'CNY';
    Const CYP = 'CYP';
    Const CZK = 'CZK';
    Const DKK = 'DKK';
    Const EEK = 'EEK';
    Const EUR = 'EUR';
    Const GBP = 'GBP';
    Const HKD = 'HKD';
    Const HRK = 'HRK';
    Const HUF = 'HUF';
    Const IDR = 'IDR';
    Const ILS = 'ILS';
    Const INR = 'INR';
    Const ISK = 'ISK';
    Const JPY = 'JPY';
    Const KRW = 'KRW';
    Const LTL = 'LTL';
    Const LVL = 'LVL';
    Const MTL = 'MTL';
    Const MXN = 'MXN';
    Const MYR = 'MYR';
    Const NOK = 'NOK';
    Const NZD = 'NZD';
    Const PHP = 'PHP';
    Const PLN = 'PLN';
    Const ROL = 'ROL';
    Const RON = 'RON';
    Const RUB = 'RUB';
    Const SEK = 'SEK';
    Const SGD = 'SGD';
    Const SIT = 'SIT';
    Const SKK = 'SKK';
    Const THB = 'THB';
    Const TRL = 'TRL';
    Const TRY = 'TRY';
    Const USD = 'USD';
    Const ZAR = 'ZAR';

    public static function all()
    {
        return [
            self::AUD => Yii::t('app', 'Australian dollar') . ' (AUD)',
			self::BGN => Yii::t('app', 'Bulgarian lev') . ' (BGN)',
			self::BRL => Yii::t('app', 'Brazilian real') . ' (BRL)',
			self::CAD => Yii::t('app', 'Canadian dollar') . ' (CAD)',
			self::CHF => Yii::t('app', 'Swiss franc') . ' (CHF)',
            self::CZK => Yii::t('app', 'Czech koruna') . ' (CZK)',
			self::DKK => Yii::t('app', 'Danish krone') . ' (DKK)',
            self::EUR => Yii::t('app', 'Euro') . ' (EUR)',
            self::GBP => Yii::t('app', 'Pound sterling') . ' (GBP)',
            self::HKD => Yii::t('app', 'Hong Kong dollar') . ' (HKD)',
			self::HRK => Yii::t('app', 'Croatian kuna') . ' (HRK)',
			self::HUF => Yii::t('app', 'Hungarian forint') . ' (HUF)',
			self::ILS => Yii::t('app', 'Israeli new shekel') . ' (ILS)',
			self::IDR => Yii::t('app', 'Indonesian rupiah') . ' (IDR)',
			self::INR => Yii::t('app', 'Indian rupee') . ' (INR)',
            self::ISK => Yii::t('app', 'Icelandic króna (plural: krónur)') . ' (ISK)',
			self::JPY => Yii::t('app', 'Japanese yen') . ' (JPY)',
            self::KRW => Yii::t('app', 'South Korean won') . ' (KRW)',
            self::MXN => Yii::t('app', 'Mexican peso') . ' (MXN)',
			self::MYR => Yii::t('app', 'Malaysian ringgit') . ' (MYR)',
			self::NOK => Yii::t('app', 'Norwegian krone') . ' (NOK)',
			self::NZD => Yii::t('app', 'New Zealand dollar') . ' (NZD)',
			self::PHP => Yii::t('app', 'Philippine peso') . ' (PHP)',
			self::PLN => Yii::t('app', 'Polish złoty') . ' (PLN)',
            self::RON => Yii::t('app', 'Romanian leu') . ' (RON)',
            self::SEK => Yii::t('app', 'Swedish krona') . ' (SEK)',
			self::SGD => Yii::t('app', 'Singapore dollar') . ' (SGD)',
            self::THB => Yii::t('app', 'Thai baht') . ' (THB)',
			self::TRY => Yii::t('app', 'Turkish lira') . ' (TRY)',
			self::USD => Yii::t('app', 'United States dollar') . ' (USD)',
			self::ZAR => Yii::t('app', 'South African rand') . ' (ZAR)',
			self::CNY => Yii::t('app', 'CNY'),
            self::CYP => Yii::t('app', 'CYP'),
            self::EEK => Yii::t('app', 'EEK'),
			self::LTL => Yii::t('app', 'LTL'),
            self::LVL => Yii::t('app', 'LVL'),
            self::MTL => Yii::t('app', 'MTL'),
			self::ROL => Yii::t('app', 'ROL'),
			self::RUB => Yii::t('app', 'RUB'),
			self::SIT => Yii::t('app', 'SIT'),
            self::SKK => Yii::t('app', 'SKK'),
        ];
    }
    public static function get($key)
    {
        $all = self::all();

        if (isset($all[$key])) {
            return $all[$key];
        }

        return Yii::t('app', '(not set)');
    }

    /**
     * Returns an array of currency labels indexed by currency code
     * The array is sorted by labels. Codes without label will come last
     * @return mixed
     */
    public static function allByLabel()
    {
        $prefix = 'ZZZ ';
        $currencyCodes = self::all();
        // Mark codes without label with high alphabetical order prefix
        foreach($currencyCodes as $code=>$label) {
            if($code==$label)
                $currencyCodes[$code] = $prefix.$label;
        }
        // Sort by label
        asort($currencyCodes);
        // Remove prefix on codes without label
        foreach($currencyCodes as $code=>$label) {
            if(substr($label, 0, 4)===$prefix)
                $currencyCodes[$code] = '(' . substr($label, 4) . ')';
        }
        return $currencyCodes;
    }
}