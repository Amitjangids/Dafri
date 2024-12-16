                             <?php 
                             $full_name_string=trim($recordInfo->first_name.' '.$recordInfo->last_name); 
                             $exp_name=explode(" ",$full_name_string);
                             $last_name=end($exp_name);
                             $short_name="";
                             for($i=0;$i < count($exp_name)-1; $i++)
                             {
                             if($i!=count($exp_name)-2 || count($exp_name)==2)
                             {

                             $short_name.=$exp_name[$i][0].".";
                             }
                             else{
                             $short_name.=$exp_name[$i][0];
                             }
                             }
                             $full_name=strtoupper($short_name." ".$last_name);
                             echo $full_name;
                             ?>