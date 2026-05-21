# HRIS Mobile App (Flutter)

Karyawan-facing mobile app untuk HRIS Indonesia. Connect ke Laravel API di `../` via Sanctum bearer token.

## Stack

- **Flutter** 3.4+ (Dart 3+)
- **Riverpod** state management
- **Dio** HTTP client + auth interceptor (bearer token)
- **FlutterSecureStorage** untuk token persistence
- **Geolocator** GPS + mock detection
- **ImagePicker** selfie kamera depan
- **GoogleMlKit** face detection (Phase 5 anti-spoofing)
- **Firebase Messaging** push notification (FCM)

## Folder Structure

```
flutter_app/
├── pubspec.yaml
├── lib/
│   ├── main.dart                       # Entry, ProviderScope, AuthGate
│   ├── core/
│   │   ├── network/api_client.dart     # Dio + token interceptor
│   │   ├── auth/auth_provider.dart     # Riverpod auth state + service
│   │   ├── storage/                    # Hive offline cache
│   │   └── theme/app_theme.dart        # Brand colors
│   ├── features/
│   │   ├── auth/login_screen.dart
│   │   ├── dashboard/dashboard_screen.dart   # Bottom nav + home tab
│   │   ├── attendance/attendance_screen.dart # Clock-in GPS+selfie
│   │   ├── leave/leave_screen.dart           # Saldo + request
│   │   ├── payslip/payslip_screen.dart       # List + PDF view
│   │   ├── approval/approval_screen.dart     # Inbox approve/reject
│   │   └── profile/                          # (TODO: profile + WA bot link)
│   └── shared/widgets/
└── assets/images/
```

## Setup Lokal (Windows)

```powershell
# 1. Install Flutter SDK (jika belum)
# https://docs.flutter.dev/get-started/install/windows

# 2. Init platform folders (Android + iOS) — sekali per project
cd "D:\project laravel\hris\flutter_app"
flutter create --platforms android,ios --org id.hris --project-name hris_mobile .

# 3. Install packages
flutter pub get

# 4. Run android emulator atau device
flutter run

# Backend API URL (auto): http://10.0.2.2:8000/api/v1 (Android emulator → host)
# Untuk iOS simulator: http://127.0.0.1:8000/api/v1
# Untuk real device: ganti ke IP LAN host yang serve Laravel
```

## Connect ke Laravel Backend

Edit `lib/core/network/api_client.dart` → ubah `defaultBaseUrl` sesuai environment.

Test login dengan demo account:
- Email: `employee@hris.test`
- Password: `password`

## Build Release

```powershell
flutter build apk --release      # Android APK
flutter build appbundle --release # Play Store AAB
flutter build ios --release      # iOS (di Mac)
```

## Permissions Wajib

### Android (`android/app/src/main/AndroidManifest.xml`)
```xml
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />
<uses-permission android:name="android.permission.USE_BIOMETRIC" />
```

### iOS (`ios/Runner/Info.plist`)
```xml
<key>NSLocationWhenInUseUsageDescription</key>
<string>Untuk clock-in dengan GPS pada lokasi kerja</string>
<key>NSCameraUsageDescription</key>
<string>Untuk selfie verifikasi saat clock-in</string>
<key>NSFaceIDUsageDescription</key>
<string>Login cepat dengan Face ID</string>
```

## Push Notification (FCM)

1. Setup Firebase project + download `google-services.json` (Android) & `GoogleService-Info.plist` (iOS)
2. Update `lib/main.dart`:
   ```dart
   await Firebase.initializeApp();
   FirebaseMessaging.instance.requestPermission();
   final token = await FirebaseMessaging.instance.getToken();
   // POST token to /api/v1/devices → backend simpan ke user_devices table
   ```

## Roadmap Mobile

- [x] Auth login + Sanctum token persistence
- [x] Dashboard home dengan menu cepat
- [x] Attendance clock-in GPS + selfie + mock-detection
- [x] Leave saldo + history
- [x] Payslip list
- [x] Approval inbox (swipe approve/reject)
- [ ] Leave request form (date picker, leave type picker, attachment upload)
- [ ] Overtime request
- [ ] Reimbursement submit dengan foto struk
- [ ] Payslip PDF viewer with password lock (NIK/DOB)
- [ ] Offline cache attendance queue (Hive)
- [ ] Face recognition + anti-spoofing (Phase 5)
- [ ] Biometric login (fingerprint/Face ID)
- [ ] Push notification + deep link
- [ ] Org chart visualization
- [ ] Training/LMS course player
- [ ] Multi-language (id/en)
