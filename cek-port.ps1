param(
    [int]$Port = 3306,
    [int]$TimeoutMs = 1500
)

$client = New-Object System.Net.Sockets.TcpClient
try {
    $task = $client.ConnectAsync('127.0.0.1', $Port)
    if ($task.Wait($TimeoutMs) -and $client.Connected) { exit 0 }
} catch {
}
exit 1