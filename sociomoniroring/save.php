<?php
// Устанавливаем правильную кодировку для работы с разными языками
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=UTF-8");

// Сохраняем сразу в формате XLS
$filename = 'survey_results.xls';

// Проверяем, существует ли уже файл
$fileExists = file_exists($filename);

// Открываем файл для добавления данных
$file = fopen($filename, 'a');

if (!$file) {
    die("Ошибка: не удалось открыть файл для записи.");
}

// Используем знак табуляции вместо запятой/точки с запятой
$delimiter = "\t"; 

// Если файла еще не было, создаем его и пишем заголовки столбцов
if (!$fileExists) {
    // Невидимая метка (BOM), чтобы Excel идеально понял арабский и французский языки
    fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
    $headers = ['Дата и время', 'Язык'];
    for ($i = 1; $i <= 33; $i++) {
        $headers[] = 'Вопрос ' . $i;
    }
    // Записываем заголовки
    fputs($file, implode($delimiter, $headers) . "\n"); 
}

// Собираем ответы конкретного студента
$rowData = [];
$rowData[] = date('Y-m-d H:i:s'); // Дата и время
$rowData[] = isset($_POST['lang']) ? $_POST['lang'] : '';

// Проходим циклом по всем 33 вопросам
for ($i = 1; $i <= 33; $i++) {
    $key = 'q' . $i;
    $answer = isset($_POST[$key]) ? trim($_POST[$key]) : '';
    
    // ВАЖНО: удаляем случайные переносы строк и табуляции внутри самих ответов, 
    // чтобы они не сломали структуру таблицы Excel
    $answer = str_replace(array("\r", "\n", "\t"), ' ', $answer);
    
    $rowData[] = $answer;
}

// Записываем собранную строку в файл
fputs($file, implode($delimiter, $rowData) . "\n");
fclose($file);

// Сообщаем HTML-форме, что всё прошло успешно
echo "Success";
?>
