<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDirCropped = 'uploads/';
    //$uploadDirRaw = 'uploads-raw/';
    $outputDir = 'output/';

    if (!is_dir($uploadDirCropped)) mkdir($uploadDirCropped, 0755, true);
    //if (!is_dir($uploadDirRaw)) mkdir($uploadDirRaw, 0755, true);
    if (!is_dir($outputDir)) mkdir($outputDir, 0755, true);

    if (isset($_FILES['file'])) {
        $fileNameCropped = uniqid() . '-cropped.jpg';
        $uploadFilePathCropped = $uploadDirCropped . $fileNameCropped;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePathCropped)) {
            http_response_code(500);
            echo json_encode(['error' => 'サムネイルのアップロードに失敗しました。']);
            exit;
        }
    }

    //if (isset($_FILES['originalFile'])) {
        //$fileNameRaw = uniqid() . '-original.jpg';
        //$uploadFilePathRaw = $uploadDirRaw . $fileNameRaw;
        //if (!move_uploaded_file($_FILES['originalFile']['tmp_name'], $uploadFilePathRaw)) {
            //http_response_code(500);
            //echo json_encode(['error' => '生の画像データのアップロードに失敗しました。']);
            //exit;
        //}
    //}

    // HTMLテンプレートのコピー作成
    $templateFilePath = 'template.html';
    if (file_exists($templateFilePath)) {
        $templateContent = file_get_contents($templateFilePath);
    
        $originalFileName = pathinfo($_FILES['originalFile']['name'], PATHINFO_FILENAME);
    
        $newHtmlContent = str_replace(['{{imageFileName}}', '{{jsonFileName}}'], [$fileNameCropped, $originalFileName], $templateContent);


        $outputFileName = pathinfo($fileNameCropped, PATHINFO_FILENAME) . '.html';
        $outputFilePath = $outputDir . $outputFileName;

        if (file_put_contents($outputFilePath, $newHtmlContent) === false) {
            http_response_code(500);
            echo json_encode(['error' => 'HTMLファイルの生成に失敗しました。']);
            exit;
        }

        //パス変更必要
        $outputUrl = "https://your-domain.com/artfort/$outputFilePath";
        echo json_encode(['message' => 'アップロードとHTML生成が完了しました。', 'url' => $outputUrl]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'テンプレートファイルが見つかりません。']);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => '不正なリクエストです。']);
}
