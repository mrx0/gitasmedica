<?php

//sclad_prihod_add.php
//Добавить приход

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		//!!!Доделать права
		//if (($clients['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
//			$orgs = SelDataFromDB('spr_org', '', '');
//			$permissions = SelDataFromDB('spr_permissions', '', '');
            $filials_j = getAllFilials(true, true, true);

			$goods_arr = array();
			
			if (isset($_GET['g_id'])){
                array_push($goods_arr, $_GET['g_id']);
			}
			//var_dump($fio_arr );
			
			echo '
				<div id="status">
					<header>
						<h2>Добавить приход</h2>
					</header>';

            echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">';

            echo '
					</div>';

			echo '
					<div id="data">';
			echo '
						<div id="errrror"></div>';

            echo '
                                    <div class="cellsBlock2">
                                        <div class="cellLeft">Филиал/Склад</div>
                                        <div class="cellRight">';
            echo '
                                            <select name="SelectFilial" id="SelectFilial">';

            $selected_filial = 0;

            if (isset($_SESSION['filial'])){
                $selected_filial = $_SESSION['filial'];
            }

            echo '
                                                <option value="0" ', $selected_filial == 0 ? 'selected': '' ,'>Главный склад [ПР21]</option>';



            foreach ($filials_j as $filial_item) {

                $selected = '';

                if ($selected_filial == $filial_item['id']) {
                    $selected = 'selected';
                }

                echo '
                                                <option value="' . $filial_item['id'] . '" ' . $selected . '>' . $filial_item['name2'] . '</option>';
            }

            echo '
                                            </select>
                                        </div>
                                    </div>';

			echo '                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Дата прихода</div>
                            <div class="cellRight">';

			echo selectDate (date('d', time()), date('m', time()), date('Y', time()), 2004, 1);

            echo '	
                                <label id="sel_date_error" class="error"></label>
                                <label id="sel_month_error" class="error"></label>
                                <label id="sel_year_error" class="error"></label>
                            </div>
                        </div>
					
                        <div class="cellsBlock2">
                            <div class="cellLeft">Поставщик</div>
                            <div class="cellRight">
                                <input type="text" name="provider" id="provider" value="">
                                <label id="provider_error" class="error"></label>
                            </div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">№ / дата документа прихода</div>
                            <div class="cellRight">
                                <input type="text" name="prov_doc" id="o" value="">
                                <label id="prov_doc_error" class="error"></label>
                            </div>
                        </div>';

        echo '			
                                                <div  style="display: inline-block;/* width: 380px; height: 600px;*/">';

        echo '
                                                    <div id="tabs_w" style="font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100%">
                                                        <ul>
                                                            <li><a href="#price">Прайс</a></li>';


        echo '
                                                        </ul>
                                                        <div id="price">';

        //Прайс

        //Быстрый поиск
        echo '	
                                                            <div style="margin: 0 0 5px; font-size: 11px; cursor: pointer; text-align: left;">';
        echo $block_fast_filter;
        echo '
                                                            </div>';

        echo '	
                                                            <div style="margin: 10px 0 5px; font-size: 11px; cursor: pointer;">
                                                                <span class="dotyel a-action lasttreedrophide">скрыть всё</span>, <span class="dotyel a-action lasttreedropshow">раскрыть всё</span>
                                                            </div>';
        echo '
                                                            <div style=" /*width: 350px;*/ height: 450px; overflow: scroll; border: 1px solid #CCC;">
                                                                <ul class="ul-tree ul-drop live_filter" id="lasttree">';

        //Показывает дерево прайса
        //showTree2(0, '', 'list', 0, FALSE, 0, FALSE, 'spr_pricelist_template', 0, 5);

        echo '
                                                                </ul>
                                                            </div>';
        echo '		
                                                        </div>';
        echo '
                                                    </div>';

        echo '
                                                </div>';

        //Результат
        echo '			
                                            <div class="invoice_rezult" style="display: inline-block; border: 1px solid #c5c5c5; border-radius: 3px; position: relative;">';

        echo '	
                                                <div id="errror" class="invoceHeader" style="position: relative;">
                                                    <div style="position: absolute; bottom: 0; right: 2px; vertical-align: middle; font-size: 11px;">
                                                        <div>	
                                                            <input type="button" class="b" value="Сохранить наряд" onclick="showInvoiceAdd(' . 5 . ', \'add\', false)">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div style="">К оплате: <div id="calculateInvoice" style="">0</div> руб.</div>
                                                    </div>';

            echo '
                                                    <div>
                                                        <div style="">Страховка: <div id="calculateInsInvoice" style="">0</div> руб.</div>
                                                    </div>';

        /*echo '
                        <div>
                            <div style="">Скидка: <div id="discountValue" class="calculateInvoice" style="color: rgb(255, 0, 198);">' . $discount . '</div><span  class="calculateInvoice" style="color: rgb(255, 0, 198);">%</span></div>
                        </div>';*/
        echo '
                                                </div>';
        echo '
                                                    <div style="position: absolute; top: 0; left: 200px; vertical-align: middle; font-size: 11px; width: 300px;">
                                                            <!--<div style="display: inline-block; vertical-align: top;">
                                                                Настройки: 
                                                            </div>-->
                                                            <div style="display: inline-block; vertical-align: top;">
                                                                <div style="margin-bottom: 2px;">
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                         
                                                                    </div><!-- / -->
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                         
                                                                    </div>
                                                                </div>
                                                                <div style="margin-bottom: 2px;">                                                                    
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                         <div class="settings_text" onclick="clearInvoice();">Очистить всё</div>
                                                                    </div><!-- / -->';

        echo '
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                        
                                                                    </div><!-- / -->
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                        
                                                                    </div>';

        /*echo '
                                    </div>';*/
        echo '
                                                                <div style="margin-bottom: 2px;">
                                                                    <div style="display: inline-block; vertical-align: top;">
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';

        echo '
                                                    <div id="invoice_rezult" style="width: 800px; height: 500px; overflow: scroll; float: none">
                                                    </div>';
        echo '
                                                </div>';





        echo '				
                        <div id="errror"></div>
                        <input type="button" class="b" value="Добавить" onclick="Ajax_add_client('.$_SESSION['id'].')">
					</div>';
				
			echo '
					</div>
				</div>
				
				<script type="text/javascript">
					
				</script>';
//		}else{
//			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
//		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>