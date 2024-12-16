<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Mail;
use App\Mail\SendMailable;
use App\User;
use App\Agent;
use App\ReferralCommission;
use App\WithdrawRequest;
use App\InactiveAmount;
use App\Referalcode;
use App\Transaction;
use App\Notification;
use App\Fee;
use App\Models\Ngnexchange;
use DB;

class updateNgnRatesDaily extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateNgnRatesDaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update other currency to ngn on daily basis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Request $request) {
    
        global $currencyList; 
        $actual_list=array();
        $first_array=array();
        $second_array=array();
        $ngn_data=Ngnexchange::where('id', 1)->first();
        foreach($currencyList as $curency) {
        if($curency!='NGN') {
        $other_currency_to_ngn=$ngn_data->other_currency_to_ngn;
        $ngn_to_other_currency=$ngn_data->ngn_to_other_currency;
        $ngn_conversion=$this->convertCurrency($curency,'NGN'); 
        $value_list[strtolower($curency).'_value']=$ngn_conversion;
        
        //for other currency to ngn
        $other_currency_to_ngn=($ngn_conversion*$other_currency_to_ngn)/100;
        $after_value_other_currency_to_ngn=$ngn_conversion+($other_currency_to_ngn);
        $first_array[strtolower($curency).'_value']=$after_value_other_currency_to_ngn;
        
        //for ngn to other currency
        $ngn_to_other_currency_rate=1/$ngn_conversion;
        $ngn_to_other_currency=($ngn_to_other_currency_rate*$ngn_to_other_currency)/100;
        $after_value_ngn_to_other_currency_rate=$ngn_to_other_currency_rate+($ngn_to_other_currency);
        $second_array[strtolower($curency).'_value']=number_format($after_value_ngn_to_other_currency_rate, 6, '.', '');
        }
        }
        Ngnexchange::where('id', 1)->update($first_array);
        Ngnexchange::where('id', 2)->update($second_array);
        Ngnexchange::where('id', 3)->update($value_list);
    }


    public function convertCurrency($toCurrency, $frmCurrency) {
        $apikey = CURRENCY_CONVERT_API_KEY;
        $query = $toCurrency . "_" . $frmCurrency;
        $curr_req = "https://api.currconv.com/api/v7/convert?q=" . $query . "&compact=ultra&apiKey=" . $apikey;
        $json = file_get_contents($curr_req);
        $obj = json_decode($json, true);
        $val = floatval($obj[$query]);
        // $val=439.759793;
        return $val;
        }

}
