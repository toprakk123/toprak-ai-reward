<?php
// toprak.ai - Anahtarsız Doğrudan Görsel Üretim Motoru
$response_output = "";
$error_output = "";
$imageUrl = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $karakter = trim($_POST['karakter']);
    $ek_prompt = trim($_POST['ek_prompt']);
    $negative_prompt = trim($_POST['negative_prompt']);
    
    // Prompt birleştirme
    $fullPrompt = $karakter . ", " . $ek_prompt . ", masterpiece, highly detailed anime style, uncensored";

    // Hugging Face Public Inference API (Stability AI SDXL) - Anahtarsız/Token gerektirmeyen veya public model uç noktası
    $apiUrl = "https://api-inference.huggingface.co/models/stabilityai/stable-diffusion-xl-base-1.0";

    $payload = [
        "inputs" => $fullPrompt,
        "parameters" => [
            "negative_prompt" => $negative_prompt
        ]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $api_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        // Gelen binary görsel verisini base64 formatına dönüştürme
        $imageUrl = 'data:image/jpeg;base64,' . base64_encode($api_response);
    } else {
        // Eğer model yükleniyorsa veya yoğunluk varsa alternatif metin veya hata mesajı
        $error_output = "Görsel üretim motoru şu an hazırlanıyor veya yük altında (HTTP Kod: $http_code). Lütfen birkaç saniye sonra tekrar deneyin, sevgilim.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>toprak.ai - Anahtarsız Anime Generator</title>
    <style>
        body {
            background-color: #121214;
            color: #e1e1e6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 900px;
            background: #1a1a1e;
            border: 1px solid #29292e;
            box-shadow: 0 0 25px rgba(138, 43, 226, 0.2);
            border-radius: 12px;
            padding: 30px;
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
            color: #fff;
            text-shadow: 0 0 10px rgba(147, 51, 234, 0.6);
            margin-bottom: 30px;
            letter-spacing: 2px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #b0b0bc;
        }
        select, textarea, input[type="range"] {
            width: 100%;
            padding: 12px;
            background: #121214;
            border: 1px solid #29292e;
            color: #fff;
            border-radius: 6px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        select:focus, textarea:focus {
            border-color: #9333ea;
            box-shadow: 0 0 8px rgba(147, 51, 234, 0.4);
        }
        textarea {
            resize: vertical;
            height: 90px;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7928ca, #4338ca);
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: opacity 0.3s, box-shadow 0.3s;
            box-shadow: 0 0 15px rgba(121, 40, 202, 0.4);
        }
        button:hover {
            opacity: 0.9;
            box-shadow: 0 0 25px rgba(121, 40, 202, 0.7);
        }
        .result-box {
            margin-top: 30px;
            background: #121214;
            border: 1px solid #29292e;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
        }
        .result-box img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.8);
        }
        .error-box {
            margin-top: 20px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #fca5a5;
            padding: 15px;
            border-radius: 6px;
        }
        hr {
            border: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #9333ea, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>toprak.ai</h1>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="karakter">Hazır Anime Karakteri Seç</label>
            <select name="karakter" id="karakter">
                <option value="Nagatoro anime character">Nagatoro (Ijiranaide, Nagatoro-san)</option>
                <option value="Asuka Langley anime character">Asuka Langley (Evangelion)</option>
                <option value="Zero Two anime character">Zero Two (Darling in the Franxx)</option>
                <option value="Makima anime character">Makima (Chainsaw Man)</option>
                <option value="Nami anime character">Nami (One Piece)</option>
                <option value="Yamato anime character" selected>Yamato (One Piece)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="ek_prompt">Ek Detaylar ve Konsept</label>
            <textarea name="ek_prompt" id="ek_prompt"><?php echo isset($_POST['ek_prompt']) ? htmlspecialchars($_POST['ek_prompt']) : 'detailed bedroom background, beautiful lighting, high quality anime art'; ?></textarea>
        </div>

        <div class="form-group">
            <label for="negative_prompt">Negative Prompt</label>
            <textarea name="negative_prompt"><?php echo isset($_POST['negative_prompt']) ? htmlspecialchars($_POST['negative_prompt']) : 'lowres, bad anatomy, bad hands, text, error, missing fingers, extra digit, fewer digits, cropped, worst quality, low quality'; ?></textarea>
        </div>

        <button type="submit">Doğrudan Görsel Üret</button>
    </form>

    <?php if (!empty($error_output)): ?>
        <div class="error-box"><?php echo $error_output; ?></div>
    <?php endif; ?>

    <?php if (!empty($imageUrl)): ?>
        <hr>
        <div class="result-box">
            <label style="color:#9333ea; font-size:18px; margin-bottom:10px; display:block;">toprak.ai Görsel Çıktısı</label>
            <img src="<?php echo $imageUrl; ?>" alt="Üretilen Anime Görseli">
        </div>
    <?php endif; ?>
</div>

</body>
</html>
