<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klasemen Sepak Bola</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: center; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        input[type="text"], textarea { width: 100%; padding: 5px; }
        .container { max-width: 800px; margin: auto; padding: 20px; }
        button { padding: 10px 20px; margin-top: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Input Skor Hasil</h2>
        <form method="post">
            <textarea name="inputSkor" rows="5" placeholder="Masukkan hasil pertandingan disini..."></textarea><br>
            <button type="submit">PROSES</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = $_POST['inputSkor'];
            $hasil = explode(',', $input);
            $timData = [];

            foreach ($hasil as $game) {
                $game = trim($game);
                preg_match('/(.*) (\d+) (.*) (\d+)/', $game, $match);

                if ($match) {
                    $tim1 = trim($match[1]);
                    $skor1 = intval($match[2]);
                    $tim2 = trim($match[3]);
                    $skor2 = intval($match[4]);

                    // Inisialisasi data tim jika belum ada
                    if (!isset($timData[$tim1])) {
                        $timData[$tim1] = ['tim' => $tim1, 'p' => 0, 'm' => 0, 'd' => 0, 'k' => 0, 'gm' => 0, 'gk' => 0, 'sg' => 0, 'poin' => 0];
                    }
                    if (!isset($timData[$tim2])) {
                        $timData[$tim2] = ['tim' => $tim2, 'p' => 0, 'm' => 0, 'd' => 0, 'k' => 0, 'gm' => 0, 'gk' => 0, 'sg' => 0, 'poin' => 0];
                    }

                    // Hitung gol, menang, seri, dan kalah
                    $timData[$tim1]['gm'] += $skor1;
                    $timData[$tim1]['gk'] += $skor2;
                    $timData[$tim2]['gm'] += $skor2;
                    $timData[$tim2]['gk'] += $skor1;

                    if ($skor1 > $skor2) {
                        $timData[$tim1]['m']++;
                        $timData[$tim2]['k']++;
                        $timData[$tim1]['poin'] += 3;
                    } elseif ($skor1 < $skor2) {
                        $timData[$tim2]['m']++;
                        $timData[$tim1]['k']++;
                        $timData[$tim2]['poin'] += 3;
                    } else {
                        $timData[$tim1]['d']++;
                        $timData[$tim2]['d']++;
                        $timData[$tim1]['poin'] += 1;
                        $timData[$tim2]['poin'] += 1;
                    }

                    $timData[$tim1]['sg'] = $timData[$tim1]['gm'] - $timData[$tim1]['gk'];
                    $timData[$tim2]['sg'] = $timData[$tim2]['gm'] - $timData[$tim2]['gk'];
                }
            }

            // Ubah menjadi array untuk sorting
            $timArray = array_values($timData);

            // Urutkan berdasarkan poin, selisih gol, dan gol masuk
            usort($timArray, function ($a, $b) {
                if ($b['poin'] != $a['poin']) return $b['poin'] - $a['poin'];
                if ($b['sg'] != $a['sg']) return $b['sg'] - $a['sg'];
                return $b['gm'] - $a['gm'];
            });

            // Tampilkan tabel klasemen
            echo "<h2>Klasemen</h2>";
            echo "<table>
                    <thead>
                        <tr>
                            <th>POS</th>
                            <th>Tim</th>
                            <th>P</th>
                            <th>M</th>
                            <th>D</th>
                            <th>K</th>
                            <th>GM</th>
                            <th>GK</th>
                            <th>SG</th>
                            <th>Poin</th>
                        </tr>
                    </thead>
                    <tbody>";
            $pos = 1;
            foreach ($timArray as $data) {
                echo "<tr>
                        <td>{$pos}</td>
                        <td>{$data['tim']}</td>
                        <td>{$data['p']}</td>
                        <td>{$data['m']}</td>
                        <td>{$data['d']}</td>
                        <td>{$data['k']}</td>
                        <td>{$data['gm']}</td>
                        <td>{$data['gk']}</td>
                        <td>" . ($data['sg'] >= 0 ? '+' . $data['sg'] : $data['sg']) . "</td>
                        <td>{$data['poin']}</td>
                    </tr>";
                $pos++;
            }
            echo "</tbody></table>";
        }
        ?>
    </div>
</body>
</html>
