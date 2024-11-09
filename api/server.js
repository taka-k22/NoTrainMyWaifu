const express = require('express');
const fs = require('fs');
const path = require('path');
const cors = require('cors');

const app = express();

app.use(cors());
app.use(express.json({ limit: '10mb' }));

// 保存先ディレクトリを指定
const saveDirectory = path.join(__dirname, '../json_files');

// 保存先ディレクトリが存在しない場合は作成
if (!fs.existsSync(saveDirectory)) {
    fs.mkdirSync(saveDirectory, { recursive: true });
}

app.post('/upload', (req, res) => {
    const { fileName, data } = req.body;

    const fileBaseName = path.basename(fileName, path.extname(fileName));
    const timestamp = new Date().toISOString().replace(/[-:.]/g, '').slice(0, 12);
    const uniqueFileName = `${fileBaseName}_${timestamp}.json`;

    // 指定したディレクトリにファイルを保存
    const filePath = path.join(saveDirectory, uniqueFileName);

    fs.writeFile(filePath, JSON.stringify(data), (err) => {
        if (err) {
            console.error(err);
            res.status(500).send('ファイルの保存に失敗しました。');
        } else {
            res.send('ファイルが正常に保存されました。');
        }
    });
});

app.listen(3000, () => {
    console.log('サーバーがポート3000で起動中...');
});
