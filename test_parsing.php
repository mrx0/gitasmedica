<?php

//test_parsing.php
//Загрузка файла для парсинга

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';

    if (($it['add_own'] == 1) || ($it['add_new'] == 1) || $god_mode){
        // include_once 'DBWork.php';
        // include_once 'functions.php';
        //
        // $filials_j = getAllFilials(false, false, false);
        // $permissions = SelDataFromDB('spr_permissions', '', '');

        echo '
				<header>
                    <div class="nav">
                        <a href="sclad.php" class="b">Склад</a>
                        <a href="sclad_prihods.php" class="b">Приходные накладные</a>
                    </div>
				</header>';

        echo '
				<style>
					.parsing-container {
						max-width: 800px;
						margin: 40px auto;
						padding: 0 20px;
					}
					
					.parsing-header {
						text-align: center;
						margin-bottom: 40px;
					}
					
					.parsing-header h2 {
						font-size: 32px;
						color: #2c3e50;
						margin-bottom: 10px;
						font-weight: 600;
					}
					
					.parsing-header p {
						color: #7f8c8d;
						font-size: 16px;
					}
					
					.upload-area {
						background: linear-gradient(135deg, #bd42ff 0%, #50f7ff 100%);
						border-radius: 20px;
						padding: 60px 40px;
						text-align: center;
						position: relative;
						overflow: hidden;
						box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
						/*transition: all 0.3s ease;*/
					}
					
/*					.upload-area:hover {
						transform: translateY(-5px);
						box-shadow: 0 15px 50px rgba(102, 126, 234, 0.4);
					}*/
					
					.upload-area.drag-over {
						transform: scale(1.02);
						box-shadow: 0 15px 60px rgba(102, 126, 234, 0.5);
					}
					
					.upload-icon {
						font-size: 64px;
						margin-bottom: 20px;
						animation: float 3s ease-in-out infinite;
					}
					
					@keyframes float {
						0%, 100% { transform: translateY(0px); }
						50% { transform: translateY(-10px); }
					}
					
					.upload-text {
						color: white;
						margin-bottom: 10px;
					}
					
					.upload-text h3 {
						font-size: 24px;
						margin-bottom: 10px;
						font-weight: 600;
						color: white;
					}
					
					.upload-text p {
						font-size: 16px;
						opacity: 0.9;
						margin-bottom: 25px;
					}
					
					.file-input-wrapper {
						position: relative;
						display: inline-block;
					}
					
					.file-input-wrapper input[type="file"] {
						position: absolute;
						opacity: 0;
						width: 100%;
						height: 100%;
						cursor: pointer;
						z-index: 2;
					}
					
					.browse-button {
						background: white;
						color: #667eea;
						padding: 14px 35px;
						border-radius: 50px;
						font-size: 16px;
						font-weight: 600;
						border: none;
						cursor: pointer;
						transition: all 0.3s ease;
						display: inline-block;
						box-shadow: 0 4px 15px rgba(0,0,0,0.2);
					}
					
					.browse-button:hover {
						transform: translateY(-2px);
						box-shadow: 0 6px 20px rgba(0,0,0,0.3);
					}
					
					.file-types {
						margin-top: 20px;
						color: white;
						font-size: 14px;
						opacity: 0.8;
					}
					
					.file-preview {
						margin-top: 30px;
						background: white;
						border-radius: 15px;
						padding: 20px;
						display: none;
						animation: slideIn 0.3s ease;
					}
					
					@keyframes slideIn {
						from {
							opacity: 0;
							transform: translateY(20px);
						}
						to {
							opacity: 1;
							transform: translateY(0);
						}
					}
					
					.file-preview-content {
						display: flex;
						align-items: center;
						justify-content: space-between;
						padding: 15px;
						background: #f8f9fa;
						border-radius: 10px;
					}
					
					.file-info {
						display: flex;
						align-items: center;
						gap: 15px;
					}
					
					.file-icon {
						font-size: 40px;
					}
					
					.file-details h4 {
						margin: 0 0 5px 0;
						color: #2c3e50;
						font-size: 16px;
					}
					
					.file-details p {
						margin: 0;
						color: #7f8c8d;
						font-size: 14px;
					}
					
					.remove-file {
						background: #e74c3c;
						color: white;
						border: none;
						padding: 8px 15px;
						border-radius: 8px;
						cursor: pointer;
						font-size: 14px;
						transition: all 0.3s ease;
					}
					
					.remove-file:hover {
						background: #c0392b;
						transform: scale(1.05);
					}
					
					.submit-button {
						width: 100%;
						background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
						color: white;
						padding: 16px;
						border: none;
						border-radius: 12px;
						font-size: 18px;
						font-weight: 600;
						cursor: pointer;
						margin-top: 20px;
						transition: all 0.3s ease;
						box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
					}
					
					.submit-button:hover:not(:disabled) {
						transform: translateY(-2px);
						box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
					}
					
					.submit-button:disabled {
						opacity: 0.6;
						cursor: not-allowed;
					}
					
					.info-cards {
						display: grid;
						grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
						gap: 20px;
						margin-top: 40px;
					}
					
					.info-card {
						background: white;
						padding: 25px;
						border-radius: 15px;
						text-align: center;
						box-shadow: 0 4px 15px rgba(0,0,0,0.05);
						transition: all 0.3s ease;
					}
					
					.info-card:hover {
						transform: translateY(-5px);
						box-shadow: 0 6px 20px rgba(0,0,0,0.1);
					}
					
					.info-card-icon {
						font-size: 36px;
						margin-bottom: 15px;
					}
					
					.info-card h4 {
						color: #2c3e50;
						margin-bottom: 8px;
						font-size: 18px;
					}
					
					.info-card p {
						color: #7f8c8d;
						font-size: 14px;
						margin: 0;
					}
				</style>
			';

        echo '
				<div class="parsing-container">
					<div class="parsing-header">
						<h2>Загрузка накладной</h2>
						<!--<p>Загрузите Excel файл для обработки и анализа данных</p>-->
					</div>
					
					<form id="uploadForm" action="test_parsing_excel_upload_upload.php" method="post" enctype="multipart/form-data">
						<div class="upload-area" id="uploadArea">
							<div class="upload-text">
								<h3>Перетащите файл сюда</h3>
								<p style="text-align: center">или</p>
							</div>
							<div class="file-input-wrapper">
								<input type="file" name="excel_file" id="excelFile" accept=".xls,.xlsx" required>
								<div class="browse-button">Выберите файл</div>
							</div>
							<div class="file-types">
								Поддерживаемые форматы: .xls, .xlsx
							</div>
						</div>
						
						<div class="file-preview" id="filePreview">
							<div class="file-preview-content">
								<div class="file-info">
									<div class="file-icon">📄</div>
									<div class="file-details">
										<h4 id="fileName">Имя файла</h4>
										<p id="fileSize">Размер файла</p>
									</div>
								</div>
								<button type="button" class="remove-file" id="removeFile">✕ Удалить</button>
							</div>
						</div>
						
						<button type="submit" class="submit-button" id="submitButton" disabled>
							Загрузить и обработать
						</button>
					</form>
				</div>
				
				<script>
					const uploadArea = document.getElementById("uploadArea");
					const fileInput = document.getElementById("excelFile");
					const filePreview = document.getElementById("filePreview");
					const fileName = document.getElementById("fileName");
					const fileSize = document.getElementById("fileSize");
					const removeFile = document.getElementById("removeFile");
					const submitButton = document.getElementById("submitButton");
					
					// Предотвращаем стандартное поведение для drag and drop
					["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
						uploadArea.addEventListener(eventName, preventDefaults, false);
						document.body.addEventListener(eventName, preventDefaults, false);
					});
					
					function preventDefaults(e) {
						e.preventDefault();
						e.stopPropagation();
					}
					
					// Подсветка области при наведении файла
					["dragenter", "dragover"].forEach(eventName => {
						uploadArea.addEventListener(eventName, highlight, false);
					});
					
					["dragleave", "drop"].forEach(eventName => {
						uploadArea.addEventListener(eventName, unhighlight, false);
					});
					
					function highlight(e) {
						uploadArea.classList.add("drag-over");
					}
					
					function unhighlight(e) {
						uploadArea.classList.remove("drag-over");
					}
					
					// Обработка загрузки файла
					uploadArea.addEventListener("drop", handleDrop, false);
					fileInput.addEventListener("change", handleFiles, false);
					
					function handleDrop(e) {
						const dt = e.dataTransfer;
						const files = dt.files;
						
						if (files.length > 0) {
							const file = files[0];
							const validExtensions = [".xls", ".xlsx"];
							const fileExtension = file.name.substring(file.name.lastIndexOf(".")).toLowerCase();
							
							if (validExtensions.includes(fileExtension)) {
								fileInput.files = files;
								displayFile(file);
							} else {
								alert("Пожалуйста, загрузите файл формата .xls или .xlsx");
							}
						}
					}
					
					function handleFiles(e) {
						const files = e.target.files;
						if (files.length > 0) {
							displayFile(files[0]);
						}
					}
					
					function displayFile(file) {
						fileName.textContent = file.name;
						fileSize.textContent = formatFileSize(file.size);
						filePreview.style.display = "block";
						submitButton.disabled = false;
					}
					
					function formatFileSize(bytes) {
						if (bytes === 0) return "0 Bytes";
						const k = 1024;
						const sizes = ["Bytes", "KB", "MB", "GB"];
						const i = Math.floor(Math.log(bytes) / Math.log(k));
						return Math.round(bytes / Math.pow(k, i) * 100) / 100 + " " + sizes[i];
					}
					
					removeFile.addEventListener("click", function() {
						fileInput.value = "";
						filePreview.style.display = "none";
						submitButton.disabled = true;
					});
				</script>
			';

    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>