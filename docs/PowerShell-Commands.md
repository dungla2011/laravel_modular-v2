# PowerShell Commands Reference

Tài liệu này ghi chú các lệnh PowerShell đúng để sử dụng trong Windows environment.

## Health Check Endpoints

### ❌ Lệnh KHÔNG hoạt động:
```powershell
# Lệnh này sẽ bị lỗi trong PowerShell
curl -s http://127.0.0.1:8000/health | ConvertFrom-Json | ConvertTo-Json -Depth 3
```

### ✅ Lệnh đúng:
```powershell
# Sử dụng Invoke-RestMethod thay vì curl
Invoke-RestMethod -Uri "http://127.0.0.1:8000/health" | ConvertTo-Json -Depth 3

# Hoặc có thể dùng với biến để dễ đọc hơn
$response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/health"
$response | ConvertTo-Json -Depth 3
```

## Lý do lỗi:

1. **curl trong PowerShell**: `curl` trong PowerShell là alias của `Invoke-WebRequest`, không phải curl Unix
2. **Tham số -s**: PowerShell không hỗ trợ tham số `-s` (silent) như curl Unix
3. **Pipe processing**: PowerShell xử lý pipe khác với bash/zsh

## Các lệnh PowerShell khác hữu ích:

### Test API endpoints:
```powershell
# GET request
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/endpoint" -Method GET

# POST request with JSON body
$body = @{
    name = "Test"
    email = "test@example.com"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/endpoint" -Method POST -Body $body -ContentType "application/json"
```

### Check application status:
```powershell
# Check if Laravel app is running
try {
    $response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/health/live" -TimeoutSec 5
    Write-Host "Application is running: $($response.status)" -ForegroundColor Green
} catch {
    Write-Host "Application is not responding" -ForegroundColor Red
}
```

### Download and display formatted JSON:
```powershell
# Pretty print JSON response
Invoke-RestMethod -Uri "http://127.0.0.1:8000/health" | ConvertTo-Json -Depth 10 | Out-Host
```

## Ghi chú quan trọng:

- **Luôn sử dụng `Invoke-RestMethod`** thay vì `curl` trong PowerShell
- **Sử dụng `-Uri`** parameter để chỉ định URL
- **Sử dụng `ConvertTo-Json -Depth X`** để format JSON output
- **Thêm error handling** với `try-catch` cho production scripts
