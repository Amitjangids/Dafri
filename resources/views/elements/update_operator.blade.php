{{Form::select('operator_type', $operatorList,$detectedOperator, ['id'=>'operator_type','class' => 'form-control required','placeholder' => 'Choose Service Provider','onChange'=>'getPlan()'])}}