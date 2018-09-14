<?php

//Change_notes_stomat.php
//

	session_start();

    if (empty($_SESSION['login']) || empty($_SESSION['id'])){
        header("location: enter.php");
    }else{
        require 'variables.php';
		
        $result = '';

        $result .= '
            <div class="cellsBlock3" style="margin-bottom: 0;">
            
                <div class="cellRight" style="background-color: rgb(225, 226, 225);">';

        $result .= '<div id="notes_change_note" style="font-size: 80%; margin: 10px 0;"></div>';

        $result .= ' 
                    <table id="add_notes_here" style="display:block;">
                        <tr>
                            <td colspan="2">
                                    <i style="color:red; font-size: 90%;">Внимание! Дата изменяется от текущей!</i>';

        $result .= '                            
                                    <form action="Change_notes_stomat_f.php">
                                        <select name="change_notes_type" id="change_notes_type">';
		for ($i=1; $i <= count($for_notes); $i++){
			$sel = '';
			if ($i == $_POST['type']){
				$sel = 'selected';
			}
            $result .= '<option value="'.$i.'" '.$sel.'>'.$for_notes[$i].'</option>';
		}
        $result .= '
                                        </select>
                                    </form>
                                
                                </td>
                            </tr>
                            <tr>
                                <td>Месяцев</td>
                                <td>Дней</td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="number" size="2" name="change_notes_months" id="change_notes_months" min="0" max="12" value="0">
                                </td>
                                <td>
                                    <input type="number" size="2" name="change_notes_days" id="change_notes_days" min="0" max="31" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="button" class="b" value="Применить" onclick="Ajax_change_notes_stomat('.$_POST['id'].', '.$_POST['worker_id'].')">
                                    <input type="button" class="b" value="Отмена" onclick="$(\'#notes_change\').html(\'\'); $(\'#notes_change\').hide();">
                                </td>
                            </tr>
                        </table>
                            
                    </div>
                </div>';

        echo json_encode(array('result' => 'success', 'data' => $result));
	}					


?>