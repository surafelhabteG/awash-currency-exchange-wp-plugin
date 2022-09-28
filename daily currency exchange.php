<?php
    /**
 * Plugin Name:       daily currency exchange form.
 * Plugin URI:        http://www.360ground.com/
 * Description:       It handle the daily currency exchange entry and display for the public.
 * Version:           4.0.0
 * Author:            Surafel habte
 * Domain Path:       /languages
 */

add_shortcode('daily_currency', 'daily_currency_shortcode');

add_action( 'admin_menu', 'wporg_options_page');
function wporg_options_page() {
    add_menu_page(
        'Currency Entry',
        'Daily Currency',
        'manage_options',
        'wporg',
        'daily_currency_view',
        plugin_dir_url(__FILE__) . 'assets/images/icon.png',
        20
    );  
}

function daily_currency_view(){
    // JS
    wp_register_script('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('prefix_bootstrap');

    wp_register_script('jquery', '//code.jquery.com/jquery-3.5.1.slim.min.js');
    wp_enqueue_script('jquery');

    wp_register_script('admin', plugins_url('assets/js/admin.js', __FILE__));
	wp_enqueue_script('admin');

    // CSS
    wp_register_style('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css');    
    wp_enqueue_style('prefix_bootstrap');

    wp_enqueue_style( 'admin', plugins_url( 'assets/css/admin.css', __FILE__ ), false, '1.0', 'all' );

        global $wpdb;
        $success = $errors = false;
        $errorsMessage = $successMessage = "";
        $editResults = [];
        $actions = "";
        $selected = "selected";
        $notSelected = "";

        $createTableQuery = "CREATE TABLE IF NOT EXISTS aw360_daily_currency_exchange (
            id int NOT NULL AUTO_INCREMENT,
            currency varchar(45) NOT NULL,
            currencyText varchar(45) NOT NULL,
            buying varchar(45) NOT NULL,
            selling varchar(45) NOT NULL,
            dates varchar(45) NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
          )";

        $results = $wpdb->query($createTableQuery);  

        if(!empty($wpdb->last_error)){
            echo "error happen in the dayabase table";
        } else {
            if(is_admin()){
                if (isset($_POST['actions'])){
                    $actions = $_POST['actions'];

                    if($_POST['dates'] == ""){
                        $errorMessage = "date field is required. please select and try again";
                        $errors = true;
                        
                    } else {
                        if($actions == "create"){
        
                            $isExist = $wpdb->get_results("SELECT * FROM aw360_daily_currency_exchange WHERE DATE(dates) = '$_POST[dates]'"); 
                          
                            if(!empty($wpdb->last_error)){
                                $errors = true;
                                $errorMessage = $wpdb->last_error;
        
                            } else {
                                if(count($isExist) > 0){
                                    $errorMessage = "Daily currency Exchange is already recorded by the specified date (" . $_POST['dates'] . ")";
                                    $errors = true;
            
                                } else {
                                    $insertQuery = "INSERT INTO aw360_daily_currency_exchange(currency,currencyText,buying,selling,dates) VALUES";
                                    $insertQueryValues = array();
                            
                                    foreach($_POST as $key => $value) {
                                        if(!in_array($key, ['dates','submit','actions'])){
                                            if($value[0] == "" || $value[1] == ""){
                                                $errorMessage = "All the fields are required. please enter all the required value and try again";
                                                $errors = true;
                                                break;
                
                                            } else {
                                                array_push($insertQueryValues, "('$key','$value[2]','$value[0]','$value[1]','$_POST[dates]')");
                                            }
                                        }
                                    }
                            
                                    if(!$errors){
                                        $insertQuery .= implode(",", $insertQueryValues);
                                        $result = $wpdb->query($insertQuery);
                                        
                                        if(!$result){
                                            $errors = true;
                                            $errorMessage = $wpdb->last_error;
                                        } else {
                                            $success = true;
                                            $successMessage = "You save the daily currency exchange successfully.";
                                        }
                                    }
            
                                }
                            }
        
                    
                        }
        
                        if ($actions == "edit"){
                            if($_POST['dates'] !== ""){
                                $editResults = $wpdb->get_results("SELECT * FROM aw360_daily_currency_exchange WHERE DATE(dates) = '$_POST[dates]'"); 
                          
                                if(count($editResults) == 0){
                                    $errorMessage = "No record found by the specified date. please select another date and try again.";
                                    $errors = true;
                                } 
            
                            } else {
                                $errorMessage = "Date field is required. please select the date and try again.";
                                $errors = true;
        
                            }
        
                            if(!empty($wpdb->last_error)){
                                $errors = true;
                                $errorMessage = $wpdb->last_error;
                            }
                        }
        
                        if ($actions == "delete"){
                            if($_POST['dates'] !== ""){
                                $deleteResult = $wpdb->query("DELETE FROM aw360_daily_currency_exchange WHERE DATE(dates) = '$_POST[dates]'"); 
                                
                                if(!$deleteResult){
                                    $errors = true;
                                    $errorMessage = "Unable to delete the records.";
                                } else {
                                    $success = true;
                                    $successMessage = "Daily currency exchange for the" . $_POST['dates'] . " deleted successfully.";
                                }
            
                            } else {
                                $errorMessage = "Date field is required. please select the date and try again.";
                                $errors = true;
        
                            }
        
                            if(!empty($wpdb->last_error)){
                                $errors = true;
                                $errorMessage = $wpdb->last_error;
                            }
                        }
        
                        if ($actions == "update"){

                            foreach($_POST as $key => $value) {
                                if(!in_array($key, ['dates','submit','actions'])){
                                    if($value[0] == "" || $value[1] == ""){
                                        $errorMessage = "All the fields are required. please enter all the required value and try again";
                                        $errors = true;
                                        break;
        
                                    } else {
                                        $data = array('buying'=>$value[0],'selling'=>$value[1],'dates'=>$_POST['dates']);
                                        $wpdb->update('aw360_daily_currency_exchange', $data, array('id'=>$value[3]));

                                        if($wpdb->last_error != ""){
                                            $errors = true;
                                            $errorMessage = $wpdb->last_error;
                                            break;
                                        }
                                    }
                                }
                            }
                                                    
                            if(!$errors){
                                $success = true;
                                $successMessage = "You update the daily currency exchange successfully.";
                            } 
                        }                
                
                        // $errors = $wpdb->print_error();
                    }

                }
    
                echo "<div class='cardD wrap' style='border: 1px solid #e1e0e0;padding: 27px;margin: 22px 31px 0px 0px; !important'>";
    
                if($success){
                    echo "<div class='alert alert-success alert-dismissible fade show mb-4' role='alert'>
                        <strong>Success ! </strong>$successMessage
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                }
    
                if($errors){
                    echo "<div class='alert alert-danger alert-dismissible fade show mb-4' role='alert'>
                        <strong>Error ! </strong>$errorMessage
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                }
                
                
                echo "<div class='card-body'>
                        <form action='' id='submit' method='post' onsubmit='return confirmation();'>
                            <div class='row'>
                                <div class='col-sm-12 col-md-2 col-lg-2'>
                                    <p>CURRENCY</p>
                                    <input type='text' class='form-control mb-4' name='USD[2]' value='1 USD ($)' placeholder='1 USD ($)' readonly />
                                    <input type='text' class='form-control mb-4' name='EUR[2]' value='1 EUR (€)' placeholder='1 EUR (€)' readonly />
                                    <input type='text' class='form-control mb-4' name='GBP[2]' value='1 GBP (£)' placeholder='1 GBP (£)' readonly />
                                    <input type='text' class='form-control mb-4' name='CAD[2]' value='1 CAD ($)' placeholder='1 CAD ($)' readonly />
                                    <input type='text' class='form-control mb-4' name='AED[2]' value='1 AED (د.إ)' placeholder='1 AED (د.إ)' readonly />
                                    <input type='text' class='form-control mb-4' name='YUA[2]' value='1 YUA (€)' placeholder='1 YUA (¥)' readonly />
                                    <input type='text' class='form-control mb-4' name='CHF[2]' value='1 CHF (€)' placeholder='1 CHF (€)' readonly />
                                </div>
                                <div class='col-sm-12 col-md-2 col-lg-2'>
                                    <p>BUYING</p>
                                    <input type='number' step='any' class='form-control mb-4' name='USD[0]' value='" . $editResults[0]->buying  . "'min='1' />
                                    <input type='hidden' name='USD[3]' value='" . $editResults[0]->id ."' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='EUR[0]' value='" . $editResults[1]->buying  . "'min='1' />
                                    <input type='hidden' name='EUR[3]' value='" . $editResults[1]->id . "' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='GBP[0]' value='" . $editResults[2]->buying  . "'min='1' />
                                    <input type='hidden' name='GBP[3]' value='" . $editResults[2]->id . "' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='CAD[0]' value='" . $editResults[3]->buying  . "'min='1' />
                                    <input type='hidden' name='CAD[3]' value='" . $editResults[3]->id . "' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='AED[0]' value='" . $editResults[4]->buying  . "'min='1' />
                                    <input type='hidden' name='AED[3]' value='" . $editResults[4]->id . "' />

                                    <input type='number' step='any' class='form-control mb-4' name='YUA[0]' value='" . $editResults[5]->buying  . "'min='1' />
                                    <input type='hidden' name='YUA[3]' value='" . $editResults[5]->id . "' />

                                    <input type='number' step='any' class='form-control mb-4' name='CHF[0]' value='" . $editResults[6]->buying  . "'min='1' />
                                    <input type='hidden' name='CHF[3]' value='" . $editResults[6]->id .  "' />
    
                                </div>
                                <div class='col-sm-12 col-md-2 col-lg-2'>
                                    <p>SELLING</p>
                                    <input type='number' step='any' class='form-control mb-4' name='USD[1]' value='" . $editResults[0]->selling . "'min='1' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='EUR[1]' value='" . $editResults[1]->selling . "'min='1' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='GBP[1]' value='" . $editResults[2]->selling . "'min='1' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='CAD[1]' value='" . $editResults[3]->selling . "'min='1' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='AED[1]' value='" . $editResults[4]->selling . "'min='1' />
    
                                    <input type='number' step='any' class='form-control mb-4' name='YUA[1]' value='" . $editResults[5]->selling . "'min='1' />

                                    <input type='number' step='any' class='form-control mb-4' name='CHF[1]' value='" . $editResults[6]->selling . "'min='1' />
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-sm-12 col-md-2 col-lg-2'>
                                    <hr />
                                    <p>Date</p>
                                    <input type='date' class='form-control mb-4' name='dates' value='" . $editResults[0]->dates . "' />
    
                                    <hr />
                                    <h4>Actions</h4>
                                    <select name='actions' id='actions' class='form-control mb-4'>
                                        <option value='create'>Create</option>
                                        <option value='edit'>Edit</option>
                                        <option value='update'>Update</option>
                                        <option value='delete'>Delete</option>
                                    </select> 
                                    
                                    <input class='btn btn-primary mt-4' type='submit' name='submit' value='Submit' />
    
                                </div>
                            </div>  
                            </form>
                    </div>
                </div>";
    
            
            } else {
    
            global $wpdb;
            $results = [];
    
    
            if (isset($_POST['date'])){
                $results = $wpdb->get_results("SELECT * FROM aw360_daily_currency_exchange WHERE dates = $_POST[date]"); 
        
            } else {
                $results = $wpdb->get_results("SELECT * FROM aw360_daily_currency_exchange WHERE dates = '" . date('Y-m-d') . "'"); 
                // $results = $wpdb->get_results("SELECT * FROM aw360_daily_currency_exchange WHERE dates = '2022-09-19'"); 
                
                $errors = $wpdb->print_error();
            }

            echo "<div class='containerCard'>
                <div class='innerContainer'>
                <h3 class='header'>
                    Excange Rate " . date('M d, Y') .
                "</h3>
                <div class='tableContainer' style='padding: 0px 15px 0px 10px !important;'>";

            echo "<div class='row mb-2'>
                    <div class='col-sm-6 col-md-6 col-lg-6' style='color: gray !important;'>Currency</div>
                    <div class='col-sm-3 col-md-3 col-lg-3' style='color: gray !important;'>Buying</div>
                    <div class='col-sm-3 col-md-3 col-lg-3' style='color: gray !important;'>Selling</div>
                 </div>";    
                 
                if(count($results) > 0){
                    foreach($results as $key => $value){
                        $hiddenValue = json_encode([
                                                'buying'=>$value->buying, 
                                                'selling'=>$value->selling,
                                                'currency'=>$value->currency, 
                                                'currencyText'=> str_replace(' ', '_', $value->currencyText)
                                            ]);

                        if($i < 6){
                            echo "<div class='row mb-2'>
                                <div class='col-sm-6 col-md-6 col-lg-6'>
                                    <span>
                                        <img src='" . plugins_url( 'assets/images/' . $value->currency . '.png', __FILE__ ) ."' />
                                    </span>
                                    <span>
                                        $value->currencyText
                                    </span>
                                    <input type='hidden' id='$value->currency' value=". $hiddenValue ." />
                                </div>
                                <div class='col-sm-3 col-md-3 col-lg-3'>
                                    $value->buying
                                </div>
                                <div class='col-sm-3 col-md-3 col-lg-3'>
                                    $value->selling
                                </div>
                             </div>";   

                        } else {
                            break;
                        }

                    }

                    echo "<div class='row mb-4'>
                        <div class='col-sm-12 col-md-12 col-lg-12'>
                            <span id='seemore' style='
                            color: #F88F33 !important;
                            float: right !important;
                            cursor: pointer !important;
                            font-weight: 600 !important;
                        '>See More</span>     
                        </div>
                    </div>";
    
                } else {
                    echo "
                    <div class='row'>
                        <div class='col-sm-12 col-md-12 col-lg-12'>
                            -- no record found --
                        </div>
                    </div>";
                }  
                
                echo  "</div></div>";   

                $url = plugins_url( 'assets/images/',  __FILE__ );
                echo  "<input type='hidden' id='ImagebaseUrl' value='$url' />";
    
                echo "<div class='convertContainer' style='margin-top: -18px !important;'>
                    <h4 class='currencyConverterHeader'>Currency Converter</h4>
                    <p class='subTitle'>Insert the amount you want to calculate</p>
                    <p><input type='hidden' id='selectedButton' value='1' /></p>
                    <p><input type='hidden' id='selectedkeydown' value='' /></p>
                    <p>
                        <span><button class='buyingBtn' id='buyingBtn' onclick='setSelectedButton(1)'>Buying</button></span>
                        <span><button class='sellingBtn' id='sellingBtn' onclick='setSelectedButton(2)'>Selling</button></span>
                    </p>";

                echo "<div class='row mb-2'>
                        <div class='col-sm-12 col-md-1 col-lg-1'>
                            <img id='selectedCurrencyFlag' src='" . plugins_url( 'assets/images/' . $results[0]->currency . '.png', __FILE__ ) ."' />
                        </div>
                        <div class='col-sm-12 col-md-4 col-lg-4'>
                        <div class='dropdown'>
                        <button style='margin-top: -5px;' id='currencyDropdownButton' class='btn dropdown-toggle' type='button' 
                            data-bs-toggle='dropdown' aria-expanded='false'>
                        ".$results[0]->currencyText.
                        "</button>
                        <ul class='dropdown-menu'>";

                        $i = 0;

                        echo  "<input type='hidden' id='fromDropdown' value='" . $results[0]->currency . "' />";

                        foreach($results as $value){
                            if($i < 6){

                                echo "<li>
                                    <span class='dropdown-item' href='#' onclick='selectCurrencyDropdown($value->currency)'>
                                        $value->currencyText
                                    </span>
                                    </li>";

                            } else {
                                break;
                            }

                            $i++;

                        }
                       
                echo "</ul></div>";

                echo "</div>
                        <div class='col-sm-12 col-md-7 col-lg-7'>
                            <input type='number' class='form-control' id='fromValue' value=0 min=0 onkeyup='calculate(1)' />
                        </div>
                    </div>";   
    
                echo "<div class='row'>
                      <div class='col-sm-12 col-md-1 col-lg-1'>
                        <img id='selectedCurrencyFlag' src='" . plugins_url( 'assets/images/ETB.png', __FILE__ ) ."' />
                      </div>
                      <div class='col-sm-12 col-md-4 col-lg-4'>
                        <div class='dropdown'>
                            <button style='margin-top: -5px;' id='currencyDropdownButton' class='btn dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                ETB
                            </button>
                            <ul class='dropdown-menu'></ul>
                        </div>
                      </div>";
    
                echo "<div class='col-sm-12 col-md-7 col-lg-7'>
                        <input type='number' class='form-control' id='toValue' value=0 min=0 onkeyup='calculate(2)' />
                    </div>
                </div>";
            
                echo "</div></div>";  
                    
                // echo "</nav></div>";
            }

        }

}

function daily_currency_shortcode(){
    return daily_currency_view();
}