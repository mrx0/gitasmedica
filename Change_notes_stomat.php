<?php

//Change_notes_stomat.php
//

	session_start();
	
	if (empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		
				$for_notes = array (
					1 => 'Каласепт, Метапекс, Септомиксин (Эндосольф)',
					2 => 'Временная пломба',
					3 => 'Открытый зуб',
					4 => 'Депульпин',
					5 => 'Распломбирован под вкладку (вкладка)',
					6 => 'Имплантация (ФДМ ,  абатмент, временная коронка на импланте)',
					7 => 'Временная коронка',
					8 => 'Санированные пациенты ( поддерживающее лечение через 6 мес)',
					9 => 'Прочее',					
					10 => 'Установлены брекеты',					
				);
		//var_dump ($_POST);
	
		echo '
				<div class="cellsBlock3">
				
					<div class="cellRight">
						<table id="add_notes_here" style="display:block;">
							<tr>
								<td colspan="2">
										Внимание! Дата изменяется от текущей!<br /><br />
										<form action="Change_notes_stomat_f.php">
											<select name="change_notes_type" id="change_notes_type">';
		for ($i=1; $i <= count($for_notes); $i++){
			$sel = '';
			if ($i == $_POST['type']){
				$sel = 'selected';
			}
			echo '<option value="'.$i.'" '.$sel.'>'.$for_notes[$i].'</option>';
		}
		echo '
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
										<input type=\'button\' class="b" value=\'изменить\' onclick=Ajax_change_notes_stomat('.$_POST['id'].')>
									</td>
								</tr>
							</table>
								
						</div>
					</div>
					
';
	}					


?>