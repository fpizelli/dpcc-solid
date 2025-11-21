<?php
declare(strict_types=1);

use App\Infra\Database\SQLiteConnection;
use App\Infra\Database\ParkingRepository;
use App\Infra\Services\CarPricing;
use App\Infra\Services\MotorbikePricing;
use App\Infra\Services\TruckPricing;
use App\Application\UseCases\RegisterVehicleEntry;
use App\Application\UseCases\RegisterVehicleExit;
use App\Application\UseCases\GenerateReport;

require __DIR__ . '/../vendor/autoload.php';

session_start();
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

$databasePath = __DIR__ . '/../database.sqlite';

$connection = new SQLiteConnection($databasePath);
$repository = new ParkingRepository($connection);

$pricingStrategies = [
    'car' => new CarPricing(),
    'bike' => new MotorbikePricing(),
    'truck' => new TruckPricing(),
];

$registerEntryUseCase   = new RegisterVehicleEntry($repository);
$registerExitUseCase    = new RegisterVehicleExit($repository, $pricingStrategies);
$generateReportUseCase  = new GenerateReport($repository);

$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'entry') {
        $plate       = trim((string)($_POST['plate'] ?? ''));
        $vehicleType = (string)($_POST['vehicle_type'] ?? '');

        if ($plate !== '' && in_array($vehicleType, ['car', 'bike', 'truck'], true)) {
            $registerEntryUseCase->execute($plate, $vehicleType);
            $message = 'Entrada registrada com sucesso.';
        } else {
            $message = 'Dados inválidos para entrada.';
        }
    }

    if ($action === 'exit') {
        $plate = trim((string)($_POST['plate'] ?? ''));

        if ($plate !== '') {
            $price = $registerExitUseCase->execute($plate);

            if ($price !== null) {
                $message = 'Saída registrada. Valor a pagar: R$ ' . number_format($price, 2, ',', '.');
            } else {
                $message = 'Nenhum registro de entrada aberto para esta placa.';
            }
        } else {
            $message = 'Placa inválida para saída.';
        }
    }

    $_SESSION['message'] = $message;
    header('Location: /');
    exit;
}

$report = $generateReportUseCase->execute();

$vehicleLabels = [
    'car' => 'Carro',
    'bike' => 'Moto',
    'truck' => 'Caminhão',
];

$reportHtml = '';

if (!empty($report)) {
    foreach ($report as $row) {
        $type = $row['vehicle_type'];

        if (isset($vehicleLabels[$type])) {
            $label = $vehicleLabels[$type];
        } else {
            $label = $type;
        }

        $total = (int)$row['total'];
        $revenue = (float)$row['revenue'];
        $revenueFormatted = number_format($revenue, 2, ',', '.');

        $reportHtml .= '<tr>';
        $reportHtml .= '<td class="px-4 py-2 text-gray-700">' . $label . '</td>';
        $reportHtml .= '<td class="px-4 py-2 text-right text-gray-700">' . $total . '</td>';
        $reportHtml .= '<td class="px-4 py-2 text-right text-gray-700">R$ ' . $revenueFormatted . '</td>';
        $reportHtml .= '</tr>';
    }
} else {
    $reportHtml .= '<tr>';
    $reportHtml .= '<td colspan="3" class="px-4 py-4 text-center text-gray-500">';
    $reportHtml .= 'Nenhum dado para exibir ainda.';
    $reportHtml .= '</td>';
    $reportHtml .= '</tr>';
}

if (!empty($message)) {
    $message = '<div class="mb-6 p-4 rounded border border-blue-200 bg-blue-50 text-blue-800">' . $message . '</div>';
} else {
    $message = '';
}

include __DIR__ . '/../templates/index.html';
